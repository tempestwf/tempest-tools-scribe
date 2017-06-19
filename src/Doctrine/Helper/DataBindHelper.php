<?php
namespace TempestTools\Crud\Doctrine\Helper;


use Doctrine\ORM\EntityManager;
use TempestTools\Common\Doctrine\Utility\EmTrait;
use TempestTools\Common\Helper\ArrayHelperTrait;
use TempestTools\Common\Utility\ErrorConstantsTrait;
use TempestTools\Common\Utility\TTConfigTrait;
use TempestTools\Crud\Doctrine\EntityAbstract;
use TempestTools\Crud\Doctrine\RepositoryAbstract;
use TempestTools\Common\Contracts\ArrayHelper as ArrayHelperContract;

class DataBindHelper implements \TempestTools\Crud\Contracts\DataBindHelper {
    use EmTrait, ArrayHelperTrait, TTConfigTrait, ErrorConstantsTrait;

    const ERRORS = [
        'noArrayHelper'=>[
            'message'=>'Error: No array helper set on DataBindHelper.',
        ],
    ];

    public function __construct(EntityManager $entityManager)
    {
        $this->setEm($entityManager);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param ArrayHelperContract|null $arrayHelper
     * @param array|null $path
     * @param array|null $fallBack
     * @param bool $force
     * @throws \RuntimeException
     */
    public function init( ArrayHelperContract $arrayHelper = NULL, array $path=NULL, array $fallBack=NULL, bool $force= true) {
        if ($arrayHelper !== NULL && ($force === true || $this->getArrayHelper() === NULL)) {
            $this->setArrayHelper($arrayHelper);
        }

        if ($path !== NULL && ($force === true || $this->getTTPath() === NULL)) {
            $this->setTTPath($path);
        }

        if ($fallBack !== NULL && ($force === true || $this->getTTFallBack() === NULL)) {
            $this->setTTFallBack($fallBack);
        }

        if (!$this->getArrayHelper() instanceof ArrayHelperContract) {
            throw new \RuntimeException($this->getErrorFromConstant('noArrayHelper'));
        }
    }

    /**
     * @param EntityAbstract $entity
     * @param array $params
     * @return EntityAbstract
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \RuntimeException
     * @throws \Mockery\Exception
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \InvalidArgumentException
     */
    public function bind(EntityAbstract $entity, array $params): EntityAbstract
    {
        $entity->allowed();
        $entity->setArrayHelper($this->getArrayHelper());
        $entity->setBindParams($params);
        /** @noinspection NullPointerExceptionInspection */
        $metadata = $this->getEm()->getClassMetadata($entity);
        $associateNames = $metadata->getAssociationNames();
        foreach ($params as $fieldName => $value) {
            if (in_array($fieldName, $associateNames, true)) {
                $targetClass = $metadata->getAssociationTargetClass($fieldName);
                $value = $this->fixScalarAssociationValue($value);
                $this->bindAssociation($entity, $fieldName, $value, $targetClass);
            } else {
                $entity->setField($fieldName, $value);
            }
        }
        return $entity;
    }

    /**
     * @param mixed $value
     * @return array
     */
    protected function fixScalarAssociationValue($value):array {
        $return = $value !== null && is_scalar($value) ? [
            'read' => [
                $value => [
                    'assignType' => 'set'
                ]
            ]
        ] : $value;
        return $return;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param EntityAbstract $entity
     * @param string $associationName
     * @param array $params
     * @param string $targetClass
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \InvalidArgumentException
     * @throws \Mockery\Exception
     * @throws \RuntimeException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function bindAssociation(EntityAbstract $entity, string $associationName, array $params = NULL, string $targetClass)
    {
        $repo = $this->getRepoForRelation($targetClass);
        $chainOverrides = ['transaction'=>false, 'flush'=>false];
        if ($params !== NULL) {
            foreach ($params as $chainType => $paramsForEntities) {
                $paramsForEntities = $this->prepareAssociationParams($entity, $associationName, $params);
                $foundEntities = $this->processChaining($chainType, $paramsForEntities, $chainOverrides, $repo);

                if ($foundEntities !== null) {
                    $this->bindEntities($foundEntities, $entity, $associationName);
                }
            }
        } else {
            $entity->bindAssociation('set', $associationName, NULL, true);
        }
    }

    /**
     * @param array $entities
     * @param EntityAbstract $targetEntity
     * @param string $associationName
     * @throws \RuntimeException
     */
    public function bindEntities (array $entities, EntityAbstract $targetEntity, string $associationName) {
        foreach ($entities as $foundEntity) {
            $params = $foundEntity->getBindParams();
            $assignType = $params['assignType'];
            $targetEntity->bindAssociation($assignType, $associationName, $foundEntity, true);
        }
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $chainType
     * @param array $params
     * @param array $chainOverrides
     * @param RepositoryAbstract $repo
     * @return array|null
     * @throws \RuntimeException
     * @throws \Mockery\Exception
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function processChaining (string $chainType, array $params, array $chainOverrides, RepositoryAbstract $repo):?array {
        $foundEntities = null;
        /** @var EntityAbstract $foundEntity */
        if ($chainType !== null) {
            switch ($chainType) {
                case 'create':
                    $foundEntities = $repo->create($params, $chainOverrides);
                    break;
                case 'read':
                    $foundEntities = $this->findEntitiesFromArrayKeys($params, $repo);
                    break;
                /*case 'update':
                    $foundEntity = $repo->update($info, $chainOverrides)[0];
                    break;
                case 'delete':
                    $foundEntity = $repo->delete($info, $chainOverrides)[0];
                    break;*/
            }
        }
        return $foundEntities;
    }
    /**
     * @param EntityAbstract $entity
     * @param string $associationName
     * @param array $paramsForEntities
     * @return array
     * @throws \RuntimeException
     */
    protected function prepareAssociationParams (EntityAbstract $entity, string $associationName, array $paramsForEntities):array {
        /** @var array $paramsForEntities */
        foreach ($paramsForEntities as $key=>$paramsForEntity) {
            if (isset($paramsForEntity['assignType'])) {
                unset($paramsForEntity['assignType']);
            }
            $paramsForEntities[$key] = $entity->processAssociationParams($associationName, $paramsForEntity);
        }
        return $paramsForEntities;
    }

    /**
     * @param array $array
     * @param RepositoryAbstract $repo
     * @return array
     */
    public function findEntitiesFromArrayKeys (array $array, RepositoryAbstract $repo):array {
        $keys = array_keys($array);
        $entities = $repo->findIn('id', $keys)->getQuery()->getResult();
        /** @var EntityAbstract $entity */
        foreach ($entities as $entity) {
            $entity->setBindParams($array[$entity->getId()]);
        }
        return $entities;
    }

    /**
     * @param string $targetClass
     * @throws \RuntimeException
     * @return RepositoryAbstract
     */
    public function getRepoForRelation(string $targetClass):RepositoryAbstract {
        /** @var RepositoryAbstract $repo */
        /** @noinspection NullPointerExceptionInspection */
        $repo = $this->getEm()->getRepository($targetClass);
        $repo->init($this->getArrayHelper(), $this->getTTPath(), $this->getTTFallBack(), false);

        // TODO: Use a contract here instead
        if (!$repo instanceof RepositoryAbstract) {
            throw new \RuntimeException($this->getErrorFromConstant('wrongTypeOfRepo'));
        }
        return $repo;
    }
}
?>
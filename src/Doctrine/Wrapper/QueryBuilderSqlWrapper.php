<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 8/14/2017
 * Time: 4:33 PM
 */

namespace TempestTools\Crud\Doctrine\Wrapper;

use Doctrine\DBAL\Query\QueryBuilder;

use TempestTools\Crud\Exceptions\QueryBuilderWrapperException;


class QueryBuilderSqlWrapper extends QueryBuilderDqlWrapper
{
    /** @var QueryBuilder $queryBuilder*/
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    protected $queryBuilder;

    /** @noinspection MagicMethodsValidityInspection */
    /** @noinspection PhpMissingParentConstructorInspection */
    /** @noinspection SenselessMethodDuplicationInspection */
    /**
     * QueryBuilderConstructionHelper constructor.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->setQueryBuilder($queryBuilder);
    }
    /** @noinspection PhpHierarchyChecksInspection */

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder():QueryBuilder
    {
        return $this->queryBuilder;
    }


    /**
     * @param QueryBuilder $queryBuilder
     */
    /** @noinspection SenselessMethodDuplicationInspection */
    /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
    public function setQueryBuilder(QueryBuilder $queryBuilder):void
    {
        $this->queryBuilder = $queryBuilder;
    }



    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param bool $paginate
     * @param bool $returnCount
     * @param int|null $hydrationType
     * @param bool $fetchJoin
     * @return mixed
     * @throws QueryBuilderWrapperException
     */
    public function getResult(bool $paginate=false, bool $returnCount=true, int $hydrationType=null, bool $fetchJoin = false)
    {
        $count = null;
        if ($paginate === true) {
            throw QueryBuilderWrapperException::paginationNotCompatible();
        }

        if ($hydrationType === null) {
            throw QueryBuilderWrapperException::hydrationNotCompatible();
        }

        $result = $this->getQueryBuilder()->execute();
        $count = $returnCount?$this->getQueryBuilder()->setFirstResult(0)->setMaxResults(null)->execute()->rowCount():null;

        return ['count'=>$count, 'result'=>$result];
    }


    /** @noinspection MoreThanThreeArgumentsInspection */


    /**
     * @param bool $useQueryCache
     * @param bool $useResultCache
     * @param int|null $timeToLive
     * @param string|null $cacheId
     * @param null $queryCacheDriver
     * @param null $resultCacheDriver
     * @throws \Doctrine\ORM\ORMException
     * @throws QueryBuilderWrapperException
     */
    public function setCacheSettings (bool $useQueryCache=true, bool $useResultCache = false, int $timeToLive=null, string $cacheId = null, $queryCacheDriver= null, $resultCacheDriver = null):void
    {
        if ($useQueryCache === true || $useResultCache === true || $queryCacheDriver !== null || $resultCacheDriver !== null) {
            throw QueryBuilderWrapperException::cacheNotCompatible();
        }

    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $className
     * @param string $alias
     * @param string|null $indexBy
     * @param bool $add
     * @throws QueryBuilderWrapperException
     */
    public function from(string $className, string $alias, string $indexBy=null, bool $add=false): void
    {
        if ($indexBy !== null) {
            throw QueryBuilderWrapperException::indexByNotCompatible();
        }

        if ($add === false) {
            /** @noinspection PhpParamsInspection */
            $this->getQueryBuilder()->$this->add('from', array(
                'table' => $className,
                'alias' => $alias
            ), false);
        } else {
            $this->getQueryBuilder()->from($className, $alias);
        }
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param string $join
     * @param string $alias
     * @param string|null $conditionType
     * @param string|null $condition
     * @param string|null $indexBy
     * @throws QueryBuilderWrapperException
     */
    public function leftJoin(string $join, string $alias, string $conditionType = null, string $condition = null, string $indexBy = null):void
    {
        if ($indexBy !== null) {
            throw QueryBuilderWrapperException::indexByNotCompatible();
        }
        $this->getQueryBuilder()->leftJoin($join, $alias, $conditionType, $condition);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param string $join
     * @param string $alias
     * @param string|null $conditionType
     * @param string|null $condition
     * @param string|null $indexBy
     * @throws QueryBuilderWrapperException
     */
    public function innerJoin(string $join, string $alias, string $conditionType = null, string $condition = null, string $indexBy = null):void
    {
        if ($indexBy !== null) {
            throw QueryBuilderWrapperException::indexByNotCompatible();
        }
        $this->getQueryBuilder()->innerJoin($join, $alias, $conditionType, $condition);
    }


    /**
     * @param string $operator
     * @throws QueryBuilderWrapperException
     */
    public function verifyOperatorAllowed(string $operator):void
    {
        if (!in_array($operator, static::SAFE_OPERATORS, true)) {
            throw QueryBuilderWrapperException::operatorNotSafe($operator);
        }
    }

}
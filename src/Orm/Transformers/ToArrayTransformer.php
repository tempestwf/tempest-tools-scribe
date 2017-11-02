<?php

namespace TempestTools\Crud\Orm\Transformers;

use TempestTools\Common\Doctrine\Transformers\SimpleTransformerAbstract;
use TempestTools\Crud\Contracts\Orm\EntityContract;

/**
 * Created by PhpStorm.
 * User: Will
 * Date: 9/15/2017
 * Time: 3:52 PM
 */

/**
 * Transforms entities to an array representation of their fields values
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class ToArrayTransformer extends SimpleTransformerAbstract {

    /**
     * @param EntityContract $entity
     * @return array
     */
    public function convert(EntityContract $entity):array
    {
        $settings = $this->getSettings();
        return $entity->toArray($settings);
    }
}
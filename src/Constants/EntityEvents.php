<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 6/2/2017
 * Time: 5:51 PM
 */

namespace TempestTools\Crud\Constants;



class EntityEvents{
    const PRE_SET_FIELD = 'preSetField';

    /**
     * @return array
     */
    static public function getAll():array {
        return [
            static::PRE_SET_FIELD
        ];
    }
}
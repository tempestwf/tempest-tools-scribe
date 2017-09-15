<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 6/2/2017
 * Time: 5:51 PM
 */

namespace TempestTools\Crud\Constants;

class EntityEventsConstants{
    const PRE_SET_FIELD = 'preSetField';
    const PRE_PROCESS_ASSOCIATION_PARAMS = 'preProcessAssociationParams';
    const PRE_PERSIST = 'prePersist';
    const POST_PERSIST = 'postPersist';
    const PRE_TO_ARRAY = 'preToArray';
    const POST_TO_ARRAY = 'postToArray';

    /**
     * @return array
     */
    public static function getAll():array {
        return [
            static::PRE_SET_FIELD,
            static::PRE_PROCESS_ASSOCIATION_PARAMS,
            static::PRE_PERSIST,
            static::POST_PERSIST,
            static::PRE_TO_ARRAY,
            static::POST_TO_ARRAY,
        ];
    }
}
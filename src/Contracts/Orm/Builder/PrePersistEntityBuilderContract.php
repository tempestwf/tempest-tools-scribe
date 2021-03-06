<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 8/22/2017
 * Time: 5:15 PM
 */

namespace TempestTools\Scribe\Contracts\Orm\Builder;

use TempestTools\Scribe\Contracts\Orm\EntityContract;

/**
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
interface PrePersistEntityBuilderContract
{
    /** @noinspection MoreThanThreeArgumentsInspection
     * @param EntityContract $entity
     * @param array $fieldSetting
     */
    public function enforce(EntityContract $entity, array $fieldSetting):void;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param EntityContract $entity
     * @param \Closure $fieldSetting
     */
    public function closure(EntityContract $entity, \Closure $fieldSetting):void;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param EntityContract $entity
     * @param mixed $fieldSetting
     */
    public function setTo(EntityContract $entity, $fieldSetting):void;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param EntityContract $entity
     * @param $fieldSetting
     */
    public function mutate(EntityContract $entity, $fieldSetting):void;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param EntityContract $entity
     * @param $fieldSetting
     */
    public function validate(EntityContract $entity, $fieldSetting):void;

    /**
     * @param $name
     * @param $arguments
     * @throws \TempestTools\Scribe\Exceptions\Orm\Helper\EntityArrayHelperException
     */
    public function __call($name, $arguments):void;
}
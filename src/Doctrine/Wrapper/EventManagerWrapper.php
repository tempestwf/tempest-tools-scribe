<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 8/15/2017
 * Time: 5:54 PM
 */

namespace TempestTools\Crud\Doctrine\Wrapper;

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use TempestTools\Common\Utility\EvmTrait;
use TempestTools\Crud\Contracts\EventManagerWrapperContract;
use TempestTools\Crud\Contracts\GenericEventArgsContract;


class EventManagerWrapper implements EventManagerWrapperContract
{
    use EvmTrait;

    /**
     * EventManagerWrapper constructor.
     *
     * @param EventManager|null $eventManager
     */
    public function __construct(EventManager $eventManager = null)
    {
        $eventManager = $eventManager ?? new EventManager();
        $this->setEvm($eventManager);
    }

    /**
     * @param EventSubscriber $target
     */
    public function addEventSubscriber (EventSubscriber $target):void
    {
        /** @noinspection NullPointerExceptionInspection */
        $this->getEvm()->addEventSubscriber($target);
    }

    /**
     * @param string $event
     * @param GenericEventArgsContract $args
     */
    public function dispatchEvent (string $event, GenericEventArgsContract $args):void
    {
        $evm = $this->getEvm();
        $evm->dispatchEvent($event, $args);
    }



}
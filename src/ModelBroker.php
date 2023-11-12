<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelBroker;

use Atk4\Core\HookTrait;
use Atk4\Data\Exception;
use Atk4\Data\Model;


/**
 * Singleton Pattern implementation taken from
 * https://github.com/DesignPatternsPHP/DesignPatternsPHP/blob/main/Creational/Singleton/Singleton.php
 */
final class ModelBroker
{

    use HookTrait;

    private static ?ModelBroker $instance = null;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): ModelBroker
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Subscribe to a hook spot. Call this function from any Class that should act upon an event like Model after insert.
     * $fx receives the same parameters as the particular Model hook. So, If you subscribe to Model::HOOK_AFTER_SAVE,
     * the $fx has the same 2 parameters as if adding a hook directly to this hook spot: the Model and $isUpdate
     *
     * @param string $hookSpot
     * @param \Closure $fx
     * @return void
     */
    public function subscribe(string $hookSpot, \Closure $fx): void
    {
        $this->onHookShort($hookSpot, $fx);
    }
}

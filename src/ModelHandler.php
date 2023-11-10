<?php declare(strict_types=1);

namespace PhilippR\Atk\Handler;

use Atk4\Core\HookTrait;
use Atk4\Data\Exception;
use Atk4\Data\Model;

final class ModelHandler
{

    use HookTrait;

    private static ?ModelHandler $instance = null;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): ModelHandler
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

    public function handleModelAfterSave(Model $entity): void {

    }
}

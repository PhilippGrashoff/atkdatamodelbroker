<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelBroker\Tests\Helpers;


use Atk4\Data\Model;
use PhilippR\Atk4\ModelBroker\ModelBroker;

class SomeOtherController
{
    public static function doSomething(Model $entity): void
    {
        if (!isset($_ENV['someOtherCounter'])) {
            $_ENV['someOtherCounter'] = 0;
        } else {
            $_ENV['someOtherCounter']++;
        }
    }

    public static function registerModelBrokerHooks(): void
    {
        ModelBroker::getInstance()->subscribe(
            Model::HOOK_BEFORE_INSERT,
            function (Model $entity, array &$data) {
                self::doSomething($entity);
            }
        );

        ModelBroker::getInstance()->subscribe(
            Model::HOOK_AFTER_INSERT,
            function (Model $entity) {
                self::doSomething($entity);
            }
        );

        ModelBroker::getInstance()->subscribe(
            Model::HOOK_BEFORE_UPDATE,
            function (Model $entity, array &$data) {
                self::doSomething($entity);
            }
        );

        ModelBroker::getInstance()->subscribe(
            Model::HOOK_AFTER_UPDATE,
            function (Model $entity) {
                self::doSomething($entity);
            }
        );
    }
}
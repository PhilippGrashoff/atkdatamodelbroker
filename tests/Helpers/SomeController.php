<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests\Helpers;

use Atk4\Data\Model;

class SomeController
{
    public function doSomethingBeforeSave(Model $entity, bool $isUpdate): void
    {
        $entity->set('name', 'SomeName');
    }

    public function doSomething(Model $entity): void
    {
        if (!isset($_ENV['someCounter'])) {
            $_ENV['someCounter'] = 0;
        } else {
            $_ENV['someCounter']++;
        }
    }
}
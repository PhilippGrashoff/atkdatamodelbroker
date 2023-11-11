<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests\Helpers;


use Atk4\Data\Model;

class SomeOtherController
{
    public function doSomething(Model $entity): void
    {
        if (!isset($_ENV['someOtherCounter'])) {
            $_ENV['someOtherCounter'] = 0;
        } else {
            $_ENV['someOtherCounter']++;
        }
    }
}
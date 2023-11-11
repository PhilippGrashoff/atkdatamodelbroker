<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests\Helpers;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelHandler\InvokeModelHandlerTrait;

class TestModel extends Model
{
    use InvokeModelHandlerTrait;

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
    }
}
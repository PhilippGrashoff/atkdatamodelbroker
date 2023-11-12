<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelBroker\Tests\Helpers;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelBroker\InvokeModelBrokerTrait;

class TestModel extends Model
{
    use InvokeModelBrokerTrait;

    public $table = 'testmodel';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
    }
}
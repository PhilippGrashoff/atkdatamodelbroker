# atkdatamodelbroker - a broker to subscribe to Model::save() hooks

## Why this repository?
I need to handle quite some actions when models are saved. My code often looked like
```php
//after save implementation in a Tour Model
$this->onHook(
     Model::HOOK_AFTER_SAVE,
     function (self $tour) {
        CalendarCacheController::handleTourChange($tour); 
        StockController::handleTourChange($tour);
     }
);
```

There some points I really do not like about this:
1) The model itself needs to know what additional actions should happen when it is saved/deleted.
2) Meaning that the model handles control flow along other models. I like my Models rather "stupid", focussing on properties management, sensible helper functions and their relations to other models.
3) As the model needs to call all actions that should happen after insert/update/delete, this also means that the code for these additional actions needs to be in the same repository. This is possibly leading to a huge monolith. I rather prefer splitting a bigger application into several repositories. This enforces good structuring and clear APIs.
   
__This simple repository is there to change these downpoints__. It is an implementation for [atk4/data](https://github.com/atk4/data). This very nice framework does not implement a pattern like MVC but has very strong Models. However, the logic of this repository can be simply adapted. It is a mix of a Singleton (could also be done by DI) and Hook Pattern.

## How this repository works
The ModelBroker is something which acts a bit like a MQTT Broker: Models can "publish" events like `after insert`, `after update` or `after delete`. Other classes can "subscribe" to these events and act upon them. 
The result is that:
- The model itself does not need to have any knowledge about the additional actions that are performed after an event like `after insert`.
- The model itself does not handle the control flow for these additional actions.
- As this is implemented using hooks, the additional actions can be added from outside the repository the Model is in.

There are 2 files in this repository which implement the logic:
- `InvokeModelBrokerTrait`: A trait to be added to a `Model` which wants to publish an event
- `ModelBroker`: The ModelBroker is called when the registered events. Other classes can subscribe to the broker to be called when these events happen.


## How to use it
- `InvokeModelBrokerTrait::publish()` and `ModelBroker::subscribe()` are the only 2 methods you need!
- Each `Model` can publish at any of the events(hook spots) that are called within `Model::save()`.
`InvokeModelBrokerTrait` needs to be added to a Model in order to do so. If so, you just have to call the `publish()` method and tell the method which hool spot to publish, e.g. `$this->publish(Model::HOOK_AFTER_SAVE)`.
- Any other Class can now subscribe to any of the events that were published by a model. To do so, they just need to call `ModelBroker::subscribe()` and tell this method to subscribe to which event and what to do when this event happens.

```php
//A model that publishes an event
class SomeModel extends Model
{
    use InvokeModelBrokerTrait;
     
    protected function init(): void 
    {
        $this->publish(Model::HOOK_AFTER_SAVE); //in here we only want to publish the after save spot
    }
    //other init() code like adding fields
}

//this Class wants to act to the after save event
class SomeController 
{
    public static function registerModelBrokerHooks(): void {
        ModelBroker::getInstance()->subscribe(
            Model::HOOK_AFTER_SAVE,
            function (Model $entity, bool $isUpdate) { //the same parameters are available as on Model::HOOK_AFTER_SAVE hook spot
                //some logic that should be performed when the after save event takes place
            }
        );
    }
);
```

Of course, this means that all additional actions need to be registered before the Model save is executed. So, e.g. in `App::init()`, some code like this needs to be added:
```php
SomeController::registerModelBrokerHooks();
SomeOtherController::registerModelBrokerHooks();
YetAnotherController::registerModelBrokerHooks();
```
## Coupling of Model and ModelBroker
The ModelBroker is invoked in the hook spots of `Model::save()`. This means that all the additional actions will be inside the same transactions as the `save()` itself. Thus, if one of the additional actions fails, the complete `save()` is rolled back.

## Several Models publishing the same event
If several models `publish()` the same event, any subscriber will receive this event from all these models.
```php
class ModelA extends Model 
{
    use InvokeModelBrokerTrait;
    
    protected function init(): void 
    {
        $this->publish(Model::HOOK_AFTER_SAVE);
    }
}

class ModelB extends Model 
{
    use InvokeModelBrokerTrait;
    
    protected function init(): void 
    {
        $this->publish(Model::HOOK_AFTER_SAVE);
    }
}

//inside some other class. This subscription will receive the after save event from both ModelA and ModelB.
ModelBroker::getInstance()->subscribe(
    Model::HOOK_AFTER_SAVE,
    function (Model $entity, bool $isUpdate) {
        //only act on ModelA
        if($entity instanceof ModelA) {
               //do something
        }
    }
);
```
Maybe in the future, the possibility to filter directly within `subscribe()` might be added.

## Installation
The easiest way to use this repository is to add it to your composer.json in the 'require' section:
```json
{
  "require": {
    "philippgrashoff/atkdatamodelbroker": "5.0.*"
  }
}
```

## Versioning
The version numbers of this repository correspond with the atk4\data versions. So 5.0.x is compatible with atk4\data 5.0.x and so on.

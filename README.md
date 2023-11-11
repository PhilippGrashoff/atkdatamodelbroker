# handlerforatk

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
   
__This simple repo is there to change these downpoints__. It is an implementation for [atk4/data](https://github.com/atk4/data). This very nice framework does not implement a pattern like MVC but has very strong Models. However, the logic of this repo can be simply adapted. It is a mix of a Singleton (could also be done by DI) and Hook Pattern.

## How this repository works
The ModelHandler is something which acts a bit like a MQTT Broker: Models can "publish" events like `afterInsert`, `afterUpdate` or `afterDelete`. Other classes can "subscribe" to these events and act upon them. 
The result is that:
- The model itself does not need to have any knowledge about the additional actions that are performed after an event like `afterInsert`.
- The model itself does not handle the control flow for these additional actions.
- As this is implemented using hooks, the additional actions can be added from outside the repository the Model is in.

## How to use it
Inside a model, you just have to invoke the ModelHandler, in this example for the `afterSave` event. This "publishes" the event to the ModelHandler:
```php
//inside Model::init()
$this->onHook(
     Model::HOOK_AFTER_SAVE,
     function (self $tour, bool $isUpdate) {
         ModelHandler::getInstance()->afterSave($tour, $isUpdate);
     }
);
```

Any Class that wants to act on this `afterSave` can now add a hook to the ModelHandler. This is like "subscribing" to this event:
```php
//Inside some Controller class
public static function registerModelHandlerHooks(): void
{
    ModelHandler::getInstance()->onHook(
        Model::HOOK_AFTER_SAVE,
        function (ModelHandler $modelHandler, Model $entity, bool $isUpdate) { //$modelHandler is added by atk4 HookTrait as first param
            //some logic that should be performed when the afterSave event takes place
        }
    );
);
```

Of course, this means that all additional actions need to be registered before the Model save is executed. So, e.g. in `App::init()`, some code like this needs to be added:
```php
SomeController::registerModelHandlerHooks();
SomeOtherController::registerModelHandlerHooks();
YetAnotherController::registerModelHandlerHooks();
```

## Coupling of Model and ModelHandler
In the example here and in the tests, the ModelHandler is invoked in the hook spots of `Atk4\Data\Model::save()`. This means that all the additional actions will be inside the same transactions as the save() itself. Thus, if one of the additional actions fails, the complete save() is rolled back.

If you want a looser coupling, you could extend `Model::save()` to something like:
```php
public function save(array $data = []) 
{
    parent::save($data);
    ModelHandler::getInstance()->afterSave($this, $this->isLoaded());
}
```
This way, the saving would be persisted even if some additional actions in the `afterSave()` would e.g. throw an Exception.

## Installation
The easiest way to use this repository is to add it to your composer.json in the 'require' section:
```json
{
  "require": {
    "philippgrashoff/handlerforatk": "5.0.*"
  }
}
```
## Versioning
The version numbers of this repository correspond with the atk4\data versions. So 5.0.x is compatible with atk4\data 5.0.x and so on.

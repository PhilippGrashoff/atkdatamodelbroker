# handlerforatk

# Why this repository?
I need to handle quite some actions when models are saved. Currently, my code looks like
```php
        $this->onHook(
            Model::HOOK_AFTER_SAVE,
            function (self $tour) {
                CalendarCacheController::handleTourChange($tour); 
                StockController::handleTourChange($tour);
            }
        );
```

or even worse, with the logic still inside the model
```php
  $this->onHook(
            Model::HOOK_AFTER_SAVE,
            function (self $model, $isUpdate) {
                if (!$model->executeHooksOnSave) {
                    return;
                }
                $tour = $model->getObject(Tour::class);
                $tour->checkNotifications();
                $tour->updateStock();
                $group = $model->getObject(Group::class);
                $group->checkNotifications();
                //we need to empty Calendar caches of all Tours and other GTTs of the group
                $group->emptyCalendarCacheOfAllTours();

                if (!$isUpdate) {
                    $tour->addMToMAudit('ADD', $group);
                    $group->addMToMAudit('ADD', $tour);
                }
            }
        );
```

There are 2 things about this I really do not like about this:
1) The model itself needs to know what additional actions should happen when it is saved/deleted
2) This also means that the code for these additional actions needs to be in the same repository, possibly leading to a huge monolith.
   
This simple repo is there to change these 2 downpoints. 

Using the Handler from this repo, the model hooks just look like:

```php
       $this->onHook(
            Model::HOOK_AFTER_SAVE,
            function (self $tour) {
                Handler::getInstance()->handleModelAfterSave($tour);
            }
        );
```
This already eliminates the first point: The model does not need to know any more what should additionally happen when it is saved.

The Handler is a mix of Singleton and Hook pattern; it opens hook spots where Controllers etc. can add their logic.
```php
    //simple Handler function just opening a hook spot
    public function handleModelAfterSave(Model $entity): void {
        $this->hook(self::HOOK_BEFORE_SAVE, [$entity]);
    }


    //some Controller adding some logic to this hook spot:
    Handler::getInstance()->onHook(Handler::HOOK_AFTER_SAVE, function(Model $entity)) {
       //do something
   }
```
As hooks can be added to the handler from anywhere, this provides a solution for 2).


Of course, this means that all these additional actions need to be registered BEFORE the model save is executed. So, e.g. in App::init(), some code like this needs to be added:
```php
$someController::registerSomeHookToHandler();
$someController::registerSomeOtherHookToHandler();
$someOtherController::registerSomeHookToHandler();
```

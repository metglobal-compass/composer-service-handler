### Symfony annotation-based services.yml generator

##### Use Case
Minimizing conflict issues occurred by single-file  based services.yml 
dependency injections
### Installation
        
1. Install with composer

   ```composer require metglobal/composer-service-handler```
    

2. Add ```Metglobal\\ServiceHandler\\ScriptHandler::buildServices``` 
command of "symfony-scripts" list in composer.json. It should look like 
   following:
    
        "scripts": {
            ...
            "post-install-cmd": [
               "Metglobal\\ServiceHandler\\ScriptHandler::buildServices",
               "@auto-scripts"
            ],
            "post-update-cmd": [
               "Metglobal\\ServiceHandler\\ScriptHandler::buildServices",
               "@auto-scripts"
            ],
            "post-autoload-dump": [
               "Metglobal\\ServiceHandler\\ScriptHandler::buildServices"
            ],
        }
        
        
3. Define which bundles will have automatic generated services.yml in 
app folder

    `service.yml`
    `````
    parameters:
        locale: en
        service_handler:
            App\:
                resource: 'src/'
                exclude: 'src/{Controller,Entity,Exclude,Repository,Kernel.php}'



4. Define which file will be handle in composer.json file extra 
section as `metglobal-services`

    `composer.json`
    `````
    "extra": {
         ...
         "metglobal-services": {
            "file": "config/services.yaml"
         }
    }

5. Add `services.yml` to .gitignore

     
### Usage
services.yml will be auto-generated after each each execution 
`composer install` or `composer update` command

Usage of `@Service` annotation at repository class
````php
namespace App\Repository;

use Metglobal\ServiceHandler\Annotation\Service;

/**
 * @Service(
 *     id="app.repository.my_repository",
 *     factory= {"@doctrine.orm.default_entity_manager", "getRepository"},
 *     arguments={"App:MyEntity"},
 *     calls={
 *          {"setSender", {"@App\Mailer\Sender"}}
 *     }
 *    )
 */
class MyRepository {

}
````

Usage of `@Service` annotation at event listener class
````php
namespace App\EventListener;

use Metglobal\ServiceHandler\Annotation\Service;
use Metglobal\ServiceHandler\Annotation\Tag;

/**
 * @Service(
 *     id="app.event_listener.my_listener",
 *     arguments={
 *          "@App\Repository\MyRepository",
 *          "@App\Mailer\Sender"     
 *     },
 *     tags={
 *          @Tag(name="kernel.event_listener", event="success", method="onSuccess"),
 *          @Tag(name="kernel.event_listener", event="fail", method="onFail")
 *     }
 *    )
 */
class MyListener {

}
````

`@Service` annotation does not have any different usage than a 
services.yml based definition.
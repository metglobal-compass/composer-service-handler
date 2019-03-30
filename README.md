### Symfony annotation-based services.yml generator

##### Use Case
Minimizing conflict issues occured by single-file  based services.yml 
dependency injections
### Installation
        
1. Install with composer

   ```composer require metglobal/composer-service-handler```
    

2. Add ```Metglobal\\ServiceHandler\\ScriptHandler::buildServices``` 
command to after ```Incenteev\\ParameterHandler\\ScriptHandler::buildParameters``` 
of "symfony-scripts" list in composer.json. It should look like following:
    
        "scripts": {
            "symfony-scripts": [
                "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
                "Metglobal\\ServiceHandler\\ScriptHandler::buildServices",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::removeSymfonyStandardFiles",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::prepareDeploymentTarget"
            ],
            "post-install-cmd": [
                "@symfony-scripts"
            ],
            "post-update-cmd": [
                "@symfony-scripts"
            ]
        }
        
        
3. Define which bundles will have automatic generated services.yml in 
app folder

    `service.yml`
    `````
    parameters:
        locale: en
        service_handler:
            AcmeBundle\:
                resource: '../../src/AcmeBundle/*'
                exclude: '../../src/AcmeBundle/{Controller,Entity,Exclude,Repository,Tests,AcmeBundle.php}'



4. Define which file will be handle in composer.json file extra 
section as `metglobal-services`

    `composer.json`
    `````
    "extra": {
        "symfony-app-dir": "app",
        "symfony-bin-dir": "bin",
        "symfony-var-dir": "var",
        "symfony-web-dir": "web",
        "symfony-assets-install": "relative",
        "incenteev-parameters": {
            "file": "app/config/parameters.yml"
        },
        "metglobal-services": {
            "file": "app/config/services.yml"
        },
        "branch-alias": null
    }

5. Add `services.yml` to .gitignore

     
### Usage
services.yml will be auto-generated after each each execution 
`composer install` or `composer update` command

Usage of `@Service` annotation at repository class
````php
namespace AcmeBundle\Repository;

use Metglobal\ServiceHandler\Annotation\Service;

/**
 * @Service(
 *     id="acme.repository.my_repository",
 *     factory= {"@doctrine.orm.default_entity_manager", "getRepository"},
 *     arguments={"AcmeBundle:MyEntity"},
 *     calls={
 *          {"setSender", {"@AcmeBundle\Mailer\Sender"}}
 *     }
 *    )
 */
class MyRepository {

}
````

Usage of `@Service` annotation at event listener class
````php
namespace AcmeBundle\EventListener;

use Metglobal\ServiceHandler\Annotation\Service;
use Metglobal\ServiceHandler\Annotation\Tag;

/**
 * @Service(
 *     id="acme.event_listener.my_listener",
 *     arguments={
 *          "@AcmeBundle\Repository\MyRepository",
 *          "@AcmeBundle\Mailer\Sender"     
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
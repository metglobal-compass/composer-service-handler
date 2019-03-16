### Symfony annotation-based services.yml generator

##### Use Case
Minimizing conflict issues occured by single-file  based services.yml dependency injections
### Installation

1. Add repository to composer.json

        {
            "type": "vcs",
            "url": "https://github.com/metglobal-compass/composer-service-handler.git"
        }
        
2. Install with composer

   ```composer require metglobal/composer-service-handler:dev-master```
    

3. Add ```Metglobal\\ServiceHandler\\ScriptHandler::buildServices``` command to 2nd position of "symfony-scripts" list in composer.json. It should look like following:
    
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
        
        
4. Define which bundles will have automatic generated services.yml in app folder

    `service.yml`
    `````
    parameters:
        locale: en
        service_handler:
            AcmeBundle\:
                resource: '../../src/AcmeBundle/*'
                exclude: '../../src/AcmeBundle/{Controller,Entity,Exclude,Repository,Tests,AcmeBundle.php}'


5. Add `services.yml` to .gitignore

     
### Usage
services.yml will be auto-generated after each each execution `composer install` command

Usage of `@Service` annotation
````$xslt
namespace ApiBundle\Repository;

use SymfonyAutoDiYml\Annotation\Service;

/**
 * @Service(
 *     id="api.repository.my_repository",
 *     factory= {"@doctrine.orm.default_entity_manager", "getRepository"},
 *     arguments={"ApiBundle:MyEntity"}
 *    )
 */
class MyRepository {

}
````

`@Service` annotation does not have any different usage than a services.yml based definition.
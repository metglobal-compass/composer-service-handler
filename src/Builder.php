<?php

namespace SymfonyAutoDiYml;

use Composer\Script\Event;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Finder\Finder;
use SymfonyAutoDiYml\Annotation\DI;
use SymfonyAutoDiYml\Finder\AnnotationFinder;
use SymfonyAutoDiYml\Finder\ComposerParser;
use SymfonyAutoDiYml\Finder\ConfigFinder;
use SymfonyAutoDiYml\Finder\DependencyFinder;
use SymfonyAutoDiYml\Finder\PhpClassFinder;
use SymfonyAutoDiYml\Finder\YamlParser;
use SymfonyAutoDiYml\Writer\YamlWriter;

class Builder
{
    /**
     * @var ConfigFinder
     */
    private $configFinder;

    /**
     * @var DependencyFinder
     */
    private $dependencyFinder;

    /**
     * @var YamlWriter
     */
    private $yamlWriter;

    /**
     * Builder constructor.
     * @param Event $event
     * @param ConfigFinder $configFinder
     * @param DependencyFinder $dependencyFinder
     * @param YamlWriter $yamlWriter
     */
    public function __construct(
        ConfigFinder $configFinder,
        DependencyFinder $dependencyFinder,
        YamlWriter $yamlWriter
    ) {
        $this->configFinder = $configFinder;
        $this->dependencyFinder = $dependencyFinder;
        $this->ymlWriter = $yamlWriter;
    }

    /**
     * @throws \ReflectionException
     */
    public function build()
    {
        $configYml = $this->configFinder->getConfigYml();

        $bundles = $configYml['parameters']['symfony-yml-builder']['bundles'];

        foreach ($bundles as $bundle) {
            // Find dependencies and cast to array for yaml convertion
            $bundleDir = sprintf("src/%s", $bundle);
            $dependencies = $this->dependencyFinder->find($bundleDir);
            $dependencies = array_map(function (DI $dependency) {
                return $dependency->toYamlArray();
            }, $dependencies);

            // Write yaml to files
            $yamlPath = sprintf('%s/Resources/config/services_di.yml', $bundleDir);
            $this->ymlWriter->write($yamlPath, ['services' => $dependencies]);
        }
    }

    /**
     * @param Event $event
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public static function buildServicesYml(Event $event)
    {
        $configFinder = new ConfigFinder(new ComposerParser($event), new YamlParser());
        $phpClassFinder = new PhpClassFinder(new Finder());
        $annotationFinder = new AnnotationFinder(new AnnotationReader(), new Reflector());
        $dependencyFinder = new DependencyFinder($phpClassFinder, $annotationFinder);
        $yamlWriter = new YamlWriter();

        $builder = new Builder($configFinder, $dependencyFinder, $yamlWriter);
        $builder->build();
    }
}

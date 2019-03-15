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
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * Builder constructor.
     * @param ConfigFinder $configFinder
     * @param DependencyFinder $dependencyFinder
     * @param YamlWriter $yamlWriter
     * @param YamlParser $yamlParser
     */
    public function __construct(ConfigFinder $configFinder, DependencyFinder $dependencyFinder, YamlWriter $yamlWriter, YamlParser $yamlParser)
    {
        $this->configFinder = $configFinder;
        $this->dependencyFinder = $dependencyFinder;
        $this->yamlWriter = $yamlWriter;
        $this->yamlParser = $yamlParser;
    }

    /**
     * @throws \ReflectionException
     */
    public function build()
    {
        $configYml = $this->configFinder->getConfigYml();

        $bundles = $configYml['parameters']['symfony-yml-builder']['bundles'];
        $exclude = $configYml['parameters']['symfony-yml-builder']['exclude'] ?? null;

        foreach ($bundles as $bundle) {
            // Find dependencies and cast to array for yaml convertion
            $bundleDir = sprintf("src/%s", $bundle);
            $dependencies = $this->dependencyFinder->find($bundleDir, $exclude);
            $dependencies = array_map(function (DI $dependency) {
                return $dependency->toYamlArray();
            }, $dependencies);

            // Read yaml dist
            $distPath = sprintf('%s/Resources/config/services.yml.dist', $bundleDir);
            $yaml = $this->yamlParser->parse($distPath);
            $yaml = $yaml ? $yaml : ['services' => []];

            // Merge dist and dependencies
            $yaml['services'] = array_merge($yaml['services'], $dependencies);

            // Write yaml to files
            $yamlPath = sprintf('%s/Resources/config/services.yml', $bundleDir);
            $this->yamlWriter->write($yamlPath, $yaml);
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

        $builder = new Builder($configFinder, $dependencyFinder, $yamlWriter, new YamlParser());
        $builder->build();
    }
}

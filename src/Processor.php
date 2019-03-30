<?php

namespace Metglobal\ServiceHandler;

use Composer\IO\IOInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Metglobal\ServiceHandler\Annotation\DI;
use Metglobal\ServiceHandler\Finder\AnnotationFinder;
use Metglobal\ServiceHandler\Finder\DependencyFinder;
use Metglobal\ServiceHandler\Finder\PhpClassFinder;
use Metglobal\ServiceHandler\Finder\YamlParser;
use Metglobal\ServiceHandler\Writer\YamlWriter;
use Symfony\Component\Finder\Finder;

class Processor
{
    private $io;

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    public function processFile(array $config)
    {
        $config = $this->processConfig($config);

        $bundles = $config['parameters']['service_handler']['bundles'];
        $exclude = $config['parameters']['service_handler']['exclude'];

        $phpClassFinder = new PhpClassFinder(new Finder());
        $annotationFinder = new AnnotationFinder(new AnnotationReader(), new Reflector());
        $dependencyFinder = new DependencyFinder($phpClassFinder, $annotationFinder);
        $yamlWriter = new YamlWriter();
        $yamlParser = new YamlParser();

        foreach ($bundles as $bundle) {
            // Find dependencies and cast to array for yaml convertion
            $bundleDir = sprintf("src/%s", $bundle);
            $dependencies = $dependencyFinder->find($bundleDir, $exclude);
            $dependencies = array_map(function (DI $dependency) {
                return $dependency->toYamlArray();
            }, $dependencies);

            // Read yaml dist
            $distPath = sprintf('%s/Resources/config/services.yml.dist', $bundleDir);
            $yaml = $yamlParser->parse($distPath);
            $yaml = $yaml ? $yaml : ['services' => []];

            // Merge dist and dependencies
            $yaml['services'] = array_merge($yaml['services'], $dependencies);

            // Write yaml to files
            $yamlPath = sprintf('%s/Resources/config/services.yml', $bundleDir);

            $exists = file_exists($yamlPath);

            $action = $exists ? 'Updating' : 'Creating';
            $this->io->write(sprintf('<info>%s the "%s" file</info>', $action, $yamlPath));

            $yamlWriter->write($yamlPath, $yaml);
        }
    }

    private function processConfig(array $config)
    {
        if (empty($config['parameters']['service_handler'])) {
            throw new \InvalidArgumentException('The parameters.service_handler setting is required to use this script handler.');
        }

        if (empty($config['parameters']['service_handler']['bundles'])) {
            throw new \InvalidArgumentException('The parameters.service_handler.bundles setting is required to use this script handler.');
        }

        if (empty($config['parameters']['service_handler']['exclude'])) {
            throw new \InvalidArgumentException('The parameters.service_handler.exclude setting is required to use this script handler.');
        }

        return $config;
    }
}
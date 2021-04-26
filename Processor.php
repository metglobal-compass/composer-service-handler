<?php

namespace Metglobal\ServiceHandler;

use Composer\IO\IOInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Metglobal\ServiceHandler\Annotation\Service;
use Metglobal\ServiceHandler\Finder\AnnotationFinder;
use Metglobal\ServiceHandler\Finder\DependencyFinder;
use Symfony\Component\Yaml\Yaml;

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

        $baseDir = \dirname($config['file']);

        $baseServiceValues = Yaml::parse(\file_get_contents($config['file']));

        $parameters = $this->processParameters((array)$baseServiceValues['parameters']);

        $serviceHandlers = $parameters['service_handler'] ?? [];

        $dependencyFinder = new DependencyFinder(
            new AnnotationFinder(new AnnotationReader())
        );

        $services = [];
        foreach ($serviceHandlers as $bundle => $paths) {
            // Now exclude AppBundle temporarily
            if ('AppBundle' == $bundle) {
                continue;
            }

            $dir = trim($baseDir.'/'.$paths['resource'], '*');

            $exclude = null;
            if (isset($paths['exclude'])) {
                $exclude = $baseDir.'/'.$paths['exclude'];
            }

            // Find dependencies and cast to array for yaml convertion
            $dependencies = array_map(
                function (Service $dependency) {
                    return $dependency->toYamlArray();
                },
                $dependencyFinder->find($bundle, $dir, $exclude)
            );

            if (empty($dependencies)) {
                continue;
            }

            // Read yaml dist
            $realFile = $config['output'] ?? $dir . 'Resources/config/services.yml';
            $distFile = $realFile.'.dist';

            if (!is_file($distFile)) {
                $this->io->write(sprintf('<info>Copying the "%s" file to "%s"</info>', $realFile, $distFile));

                \copy($realFile, $distFile);

                continue;
            }

            $distFileContent = Yaml::parse(\file_get_contents($distFile));

            if (!array_key_exists('services', $distFileContent)) {
                $distFileContent = ['services' => []];
            }

            // Merge dist and dependencies
            $distFileContent['services'] = array_merge((array)$distFileContent['services'], $dependencies);

            $this->io->write(
                sprintf('<info>Updating the "%s%s" file</info>', str_replace('\\', '/', $bundle), $realFile)
            );

            $content = Yaml::dump($distFileContent, 4, 4, true);

            \file_put_contents($realFile, $content);

            $services[$bundle] = $distFileContent;
        }

        return $services;
    }

    private function processConfig(array $config): array
    {
        if (empty($config['file'])) {
            throw new \InvalidArgumentException(
                'The extra.metglobal-services.file setting is required to use this script handler.'
            );
        }

        return $config;
    }

    private function processParameters(array $parameters)
    {
        if (empty($parameters['service_handler'])) {
            throw new \InvalidArgumentException(
                'The parameters.service_handler setting is required to use this script handler.'
            );
        }

        foreach ($parameters['service_handler'] as $parameter) {
            if (empty($parameter['resource'])) {
                throw new \InvalidArgumentException(
                    'The parameters.service_handler.resource setting is required to use this script handler.'
                );
            }

            if (empty($parameter['exclude'])) {
                throw new \InvalidArgumentException(
                    'The parameters.service_handler.exclude setting is required to use this script handler.'
                );
            }
        }

        return $parameters;
    }
}
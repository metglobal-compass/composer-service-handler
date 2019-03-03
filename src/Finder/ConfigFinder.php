<?php

namespace Metglobal\ServiceHandler\Finder;

class ConfigFinder
{
    /**
     * @var ComposerParser
     */
    private $composerParser;

    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * ConfigFinder constructor.
     * @param ComposerParser $composerParser
     * @param YamlParser $yamlParser
     */
    public function __construct(ComposerParser $composerParser, YamlParser $yamlParser)
    {
        $this->composerParser = $composerParser;
        $this->yamlParser = $yamlParser;
    }

    /**
     * @return mixed
     */
    public function getConfigYml()
    {
        $symfonyAppDir = $this->composerParser->getSymfonyAppDir();

        $yaml = $this->yamlParser->parse(sprintf("%s/config/config.yml", $symfonyAppDir));

        // Return empty array if parameter does not exists
        if (!isset($yaml['parameters']['service_handler']['bundles'])) {
            $yaml = [
                'parameters' => [
                    'service_handler' => [
                        'bundles' => [],
                    ]
                ]
            ];
        }

        if (!is_array($yaml['parameters']['service_handler']['bundles'])) {
            throw new \InvalidArgumentException(
                'parameters.service_handler.bundles config must be array of string which has bundle names'
            );
        }

        // Return empty array if parameter does not exists
        if (!isset($yaml['parameters']['service_handler']['exclude'])) {
            $yaml = [
                'parameters' => [
                    'service_handler' => [
                        'exclude' => [],
                    ]
                ]
            ];
        }

        if (!is_array($yaml['parameters']['service_handler']['exclude'])) {
            throw new \InvalidArgumentException(
                'parameters.service_handler.exclude config must be array of excluded bundle folders'
            );
        }

        return $yaml;
    }
}

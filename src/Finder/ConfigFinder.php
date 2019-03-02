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
        if (!isset($yaml['parameters']['symfony-yml-builder']['bundles'])) {
            $yaml = [
                'parameters' => [
                    'symfony-yml-builder' => [
                        'bundles' => [],
                    ]
                ]
            ];
        }

        if (!is_array($yaml['parameters']['symfony-yml-builder']['bundles'])) {
            throw new \InvalidArgumentException(
                'symfony-yml-builder.bundles parameter must be array of string which has bundle names'
            );
        }

        return $yaml;
    }
}

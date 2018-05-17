<?php

namespace SymfonyAutoDiYml\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PhpClassFinder
{
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * PhpClassFinder constructor.
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param $dir
     * @return array
     */
    public function find($dir)
    {
        $this->finder->in($dir)->files()->name('*.php');
        $files = $this->finder->getIterator();

        $classes = [];

        foreach ($files as $file) {
            $classes[] = $this->getClassName($file);
        }

        return $classes;
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    protected function getClassName(SplFileInfo $file)
    {
        $className = "\\" . str_replace(".php", "", $file->getFilename());
        preg_match("/namespace(.*);/", $file->getContents(), $matches);
        if (count($matches) > 0) {
            $className = trim($matches[1]) . $className;
        }

        return $className;
    }
}

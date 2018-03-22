<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\Base;

use stdClass;
use Exception;
use ReflectionClass;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase
{
    /**
     * @var array
     */
    private $prophecies;

    /**
     * @var stdClass
     */
    private $object;

    protected function setup() : void
    {
        $this->object = $this->buildTarget();
        $this->initialize();
    }

    protected function target()
    {
        return $this->object;
    }

    /**
     * Arguments injected in object to be tested
     */
    protected function setupArgs() : array
    {
        return [];
    }

    /**
     * More stuff on target ($this->target())
     */
    protected function initialize() : void
    {
        return ;
    }

    protected function prophesy(string $class)
    {
        if (!isset($this->prophecies[$class])) {
            $this->prophecies[$class] = $this->prophesize($class);
        }

        return $this->prophecies[$class];
    }

    protected function getTargetClassName() : string
    {
        return preg_replace('/(^Tests\\\Unit\\\)|(Test$)/', '', get_class($this));
    }

    private function buildTarget()
    {
        $class = $this->getTargetClassName();

        return $this->instantiateTarget($class);

    }

    private function instantiateTarget($class)
    {
        if (0 === count($this->setupArgs())) {
            return new $class;
        }

        $r = new ReflectionClass($class);

        return $r->newInstanceArgs($this->setupArgs());
    }

}
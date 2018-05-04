<?php

namespace Tests\Unit\Solean\Base;

use Generator;
use ReflectionClass;
use RuntimeException;
use BadFunctionCallException;

abstract class Builder
{
    private $members;

    public function __construct()
    {
        $this->reset();
    }

    public function build()
    {
        switch ($this->getTargetType()) {
            case 'entity':
                $object = $this->buildEntity();
                break;
            case 'dto':
                $object = $this->buildDTO();
                break;
            case 'vo':
                $object = $this->buildValueObject();
                break;
            default:
                throw new RuntimeException(sprintf('unknown target type "%s"', $this->getTargetType()));
        }

        return $object;
    }

    public function reset()
    {
        $this->members = [];

        return $this;
    }

    public function __call(string $name, array $arguments): void
    {
        if ('with' !== substr($name, 0, 4)) {
            throw new BadFunctionCallException(sprintf('Call to undefined method %s::%s()', get_class($this), $name));
        }

        $this->with(substr($name, 0, 4), lcfirst($arguments[0]));
    }

    public function with(string $name, $value)
    {
        $this->members[$name] = $value;

        return $this;
    }

    protected function getTargetType(): string
    {
        return 'entity';
    }

    private function buildEntity()
    {
        $class = $this->getTargetClass();
        $object = new $class();

        foreach ($this->members as $field => $value) {
            $method = sprintf('set%s', ucfirst($field));
            if ($value instanceof Builder) {
                $object->{$method}($value->build());
            } else {
                $object->{$method}($value);
            }
        }
        return $object;
    }

    private function buildDTO()
    {
        $class = $this->getTargetClass();

        $args = iterator_to_array($this->constructArgs($class, $this->members));

        $objectReflection = new ReflectionClass($class);
        $object = $objectReflection->newInstanceArgs($args);

        return  $object;
    }

    private function buildValueObject()
    {
        $class = $this->getTargetClass();
        $method = 'fromValues';
        $args = iterator_to_array($this->constructArgs($class, $this->members, $method));
        $object = call_user_func_array(sprintf('%s::%s', $this->getTargetClass(), $method), $args);

        return  $object;
    }

    private function constructArgs(string $class, array $data, string $method = '__construct'): Generator
    {
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethod($method)->getParameters() as $param) {
            if ($data[$param->name] instanceof Builder) {
                $data[$param->name] = $data[$param->name]->build();
            }

            (yield $param->name => isset($data[$param->name]) ? $data[$param->name] : null);
        }
    }

    public static function resetAll(...$builders)
    {
        foreach($builders as $builder) {
            $builder->reset();
        }
    }

    abstract protected function getTargetClass(): string;
}

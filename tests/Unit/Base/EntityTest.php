<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\Base;

use ReflectionClass;

abstract class EntityTest extends TestCase
{
    public function testSetterGetter()
    {
        $reflection = new ReflectionClass($this->getTargetClassName());

        foreach ($reflection->getMethods() as $method) {
            $setterName = $method->getShortName();
            if ($this->isSetter($setterName) && !$this->isIgnored($setterName)) {
                $getterName = $this->getGetterFromSetter($setterName);
                if ($reflection->hasMethod($getterName)) {
                    $parameter = $method->getParameters()[0];
                    $type = $parameter->getType() ? $parameter->getType()->getName() : null;
                    $value = $this->buildAppropriateFromType($type);

                    $this->target()->{$setterName}($value);
                    $got = $this->target()->{$getterName}();

                    if ($parameter->allowsNull()) {
                        $this->target()->{$setterName}(null);
                        $gotNull = $this->target()->{$getterName}();

                        $this->assertNull($gotNull, sprintf('%s should return null', $setterName));
                    }

                    $this->assertEquals($value, $got, sprintf('%s should return "%s"', $setterName, is_object($value) ? get_class($value) : (string) $value));
                }
            }
        }
    }

    protected function ignoreSetters()
    {
        return [];
    }

    private function isIgnored($method)
    {
        return in_array($method, $this->ignoreSetters());
    }

    private function isSetter(string $method): bool
    {
        return $this->isStringStartBy($method, 'set');
    }

    private function getGetterFromSetter(string $method): string
    {
        return str_replace('set', 'get', $method);
    }

    private function isStringStartBy(string $name, string $set): bool
    {
        return $set === substr($name, 0, strlen($set));
    }

    private function buildAppropriateFromType(?string $type)
    {
        switch ($type) {
            case null:
                return null;
            case 'string':
                return 'test';
            case 'int':
                return 12;
            case 'float':
                return 12.12;
            case 'array':
                return [];
            default:
                return new $type;
        }
    }
}

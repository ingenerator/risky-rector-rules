<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use Rector\Reflection\ReflectionResolver;

use const true;

final class AddMethodTypeGuard
{
    public function __construct(
        private readonly ReflectionResolver $reflectionResolver,
    ) {
    }

    public function shouldSkip(ClassMethod $method, AddMethodTypeConfig $config): bool
    {
        if ( ! $config->interfacesOnly) {
            return false;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($method);
        if ( ! $classReflection instanceof ClassReflection) {
            return \false;
        }
        if ( ! $classReflection->isInterface()) {
            return true;
        }

        return false;
    }
}

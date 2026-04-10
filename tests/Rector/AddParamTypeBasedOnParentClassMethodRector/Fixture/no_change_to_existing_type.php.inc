<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\NoChangeToExistingType;

interface SomeInterface
{
    public function method1(string $any);
    public function method2(string $thing);
    public function method3(string $goes);
}

class SomeClassWithTypedMethods implements SomeInterface {
    public function method1(string $any) {}
    public function method2(mixed $thing) {}

    /**
     * Note we don't change the type even if the implementing class method is invalid
     */
    public function method3(false $goes) {}
}

?>

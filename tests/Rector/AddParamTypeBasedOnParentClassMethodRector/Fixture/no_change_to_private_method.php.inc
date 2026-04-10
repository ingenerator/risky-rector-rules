<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\NoChangeToPrivateMethod;

class ClassA
{
    private function method1(string $any) {}
}

class ClassB extends ClassA
{
    private function method1($any) {}
}

?>

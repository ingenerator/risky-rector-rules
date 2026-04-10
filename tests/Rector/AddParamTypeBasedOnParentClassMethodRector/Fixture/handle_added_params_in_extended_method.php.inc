<?php
namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\HandleAddedParamsInExtendedMethod;

interface SomeTypedInterface
{
    public function method1(string $whatever): void;
}

class SomeClassA implements SomeTypedInterface
{
    public function method1($whatever, $extra = false): void
    {
    }
}

?>
-----
<?php
namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\HandleAddedParamsInExtendedMethod;

interface SomeTypedInterface
{
    public function method1(string $whatever): void;
}

class SomeClassA implements SomeTypedInterface
{
    public function method1(string $whatever, $extra = false): void
    {
    }
}

?>

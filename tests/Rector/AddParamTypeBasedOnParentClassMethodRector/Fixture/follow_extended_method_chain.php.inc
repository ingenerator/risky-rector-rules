<?php
namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\FollowExtendedMethodChain;

interface SomeTypedInterface
{
    public function method1(string $whatever): void;
    public function doSomething(bool $ok): void;

}

abstract class SomeClassA implements SomeTypedInterface
{
    public function method1(string|false $whatever): void
    {
    }
}

class SomeClassB extends SomeClassA
{
    public function method1($whatever): void
    {
    }

    public function noParent($any): void
    {
    }

    public function doSomething($ok): void
    {
    }
}
?>
-----
<?php
namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\FollowExtendedMethodChain;

interface SomeTypedInterface
{
    public function method1(string $whatever): void;
    public function doSomething(bool $ok): void;

}

abstract class SomeClassA implements SomeTypedInterface
{
    public function method1(string|false $whatever): void
    {
    }
}

class SomeClassB extends SomeClassA
{
    public function method1(string|false $whatever): void
    {
    }

    public function noParent($any): void
    {
    }

    public function doSomething(bool $ok): void
    {
    }
}
?>

<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\ApplyInterfaceTypeToMethod;

use DateTimeInterface;

interface SomeTypedInterface
{

    public function method1(string $whatever): void;

    public function partiallyTyped(DateTimeInterface $at, string $something): void;

}

class SomeClass implements SomeTypedInterface
{
    public function method1($whatever): void
    {
    }

    public function partiallyTyped(DateTimeInterface $at, $something): void
    {
    }

}
?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\ApplyInterfaceTypeToMethod;

use DateTimeInterface;

interface SomeTypedInterface
{

    public function method1(string $whatever): void;

    public function partiallyTyped(DateTimeInterface $at, string $something): void;

}

class SomeClass implements SomeTypedInterface
{
    public function method1(string $whatever): void
    {
    }

    public function partiallyTyped(DateTimeInterface $at, string $something): void
    {
    }

}
?>

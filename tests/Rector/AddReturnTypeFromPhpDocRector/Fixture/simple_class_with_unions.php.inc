<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @return int|null
     */
    public function method1() {}

    /**
     * @return bool|int
     */
    public function doSomething() {}
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    public function method1(): ?int {}

    public function doSomething(): bool|int {}
}

?>

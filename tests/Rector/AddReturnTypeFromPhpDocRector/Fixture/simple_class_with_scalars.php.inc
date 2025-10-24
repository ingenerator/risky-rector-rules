<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @return string
     */
    public function method1() {}

    /**
     * @return float
     */
    public function doSomething() {}
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    public function method1(): string {}

    public function doSomething(): float {}
}

?>

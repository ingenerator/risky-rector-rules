<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @return string
     */
    public function method1();

    /**
     * @return float
     */
    public function doSomething();
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    public function method1(): string;

    public function doSomething(): float;
}

?>

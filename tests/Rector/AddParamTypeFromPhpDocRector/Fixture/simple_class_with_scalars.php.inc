<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @param string $foo
     * @param int $bar
     * @param bool $isOk
     */
    public function method1($foo, $bar, $isOk) {}

    /**
     * @param string $msg
     * @param float $value
     */
    public function doSomething($msg, $value) {}
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    public function method1(string $foo, int $bar, bool $isOk) {}

    public function doSomething(string $msg, float $value) {}
}

?>

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

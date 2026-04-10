<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @param string $whatever
     */
    public function method1($whatever) {}

    /**
     * Some method with docs
     */
    public function doSomething() {}
}

?>

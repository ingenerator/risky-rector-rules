<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param string $whatever
     */
    public function method1($whatever);

    /**
     * Some method with docs
     */
    public function doSomething();

    public function undocumented();

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param string $whatever
     */
    public function method1($whatever): void;

    /**
     * Some method with docs
     */
    public function doSomething(): void;

    public function undocumented(): void;

}

?>

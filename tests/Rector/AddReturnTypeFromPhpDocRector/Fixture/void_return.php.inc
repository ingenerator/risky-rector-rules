<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @return void
     */
    public function method1();

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    public function method1(): void;

}

?>

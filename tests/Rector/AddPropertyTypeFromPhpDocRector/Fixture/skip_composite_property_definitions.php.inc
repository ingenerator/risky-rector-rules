<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * This is non-standard and likely not safe to update automatically.
     *
     * @var string
     */
    public string $prop1, $prop2, $prop3;

    /**
     * @var string
     */
    public $prop4;
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * This is non-standard and likely not safe to update automatically.
     *
     * @var string
     */
    public string $prop1, $prop2, $prop3;

    public string $prop4;
}

?>

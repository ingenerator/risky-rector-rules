<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @var string|null
     */
    public $prop1;

    /**
     * @var \stdClass|float|null
     */
    public $prop2;
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    public ?string $prop1;

    public \stdClass|float|null $prop2;
}

?>

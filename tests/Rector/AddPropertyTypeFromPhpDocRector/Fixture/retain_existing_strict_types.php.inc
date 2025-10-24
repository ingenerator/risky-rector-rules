<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @var string
     */
    public string $prop1;

    /**
     * @var object
     */
    public float $prop2;

    /**
     * @var bool
     */
    public bool $prop3;

    /**
     * @var \My\Custom\OtherThing
     */
    public \My\Custom\Something $with_wrong_type;

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    public string $prop1;

    /**
     * @var object
     */
    public float $prop2;

    public bool $prop3;

    /**
     * @var \My\Custom\OtherThing
     */
    public \My\Custom\Something $with_wrong_type;

}

?>

<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

use My\Custom\ImportedClass;

class SomeClass
{

    /**
     * @var \DateTimeImmutable
     */
    public $prop1;

    /**
     * @var \My\Custom\FQCN
     */
    public $prop2;

    /**
     * Note that the imported class will be FQCN but this will be fixed by other rectors
     *
     * @var ImportedClass
     */
    public $prop3;

    /**
     * @var array
     */
    public $prop4;

    /**
     * @var iterable
     */
    public $prop5;

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

use My\Custom\ImportedClass;

class SomeClass
{

    public \DateTimeImmutable $prop1;

    public \My\Custom\FQCN $prop2;

    /**
     * Note that the imported class will be FQCN but this will be fixed by other rectors
     */
    public \My\Custom\ImportedClass $prop3;

    public array $prop4;

    public iterable $prop5;

}

?>

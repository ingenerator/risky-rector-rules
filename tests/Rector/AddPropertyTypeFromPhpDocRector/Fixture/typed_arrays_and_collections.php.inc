<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @var list<string>
     */
    public $prop1;

    /**
     * @var array{foo: string, bar: string}
     */
    public $prop2;

    /**
     * @var \My\Custom\Collection<\DateTimeImmutable>
     */
    public $prop3;

    /**
     * @var iterable<object>
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
     * @var list<string>
     */
    public array $prop1;

    /**
     * @var array{foo: string, bar: string}
     */
    public array $prop2;

    /**
     * @var \My\Custom\Collection<\DateTimeImmutable>
     */
    public \My\Custom\Collection $prop3;

    /**
     * @var iterable<object>
     */
    public iterable $prop4;

}

?>

<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param list<string> $a
     * @param array{foo: string, bar: string} $b
     * @param \My\Custom\Collection<\DateTimeImmutable> $c
     * @param iterable<object> $d
     */
    public function method1($a, $b, $c, $d);

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param list<string> $a
     * @param array{foo: string, bar: string} $b
     * @param \My\Custom\Collection<\DateTimeImmutable> $c
     * @param iterable<object> $d
     */
    public function method1(array $a, array $b, \My\Custom\Collection $c, iterable $d);

}

?>

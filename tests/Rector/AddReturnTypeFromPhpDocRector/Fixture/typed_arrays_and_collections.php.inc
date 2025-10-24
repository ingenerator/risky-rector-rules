<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @return list<string>
     */
    public function method1($a, $b, $c, $d);

    /**
     * @return array{foo: string, bar: string}
     */
    public function method2($a, $b, $c, $d);

    /**
     * @return \My\Custom\Collection<\DateTimeImmutable>
     */
    public function method3($a, $b, $c, $d);

    /**
     * @return iterable<object>
     */
    public function method4($a, $b, $c, $d);

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @return list<string>
     */
    public function method1($a, $b, $c, $d): array;

    /**
     * @return array{foo: string, bar: string}
     */
    public function method2($a, $b, $c, $d): array;

    /**
     * @return \My\Custom\Collection<\DateTimeImmutable>
     */
    public function method3($a, $b, $c, $d): \My\Custom\Collection;

    /**
     * @return iterable<object>
     */
    public function method4($a, $b, $c, $d): iterable;

}

?>

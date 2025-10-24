<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

use My\Custom\ImportedClass;

interface SomeInterface
{

    /**
     * @return \DateTimeImmutable
     */
    public function method1();

    /**
     * @return \My\Custom\FQCN
     */
    public function method2();

    /**
     * Note that the imported class will be FQCN but this will be fixed by other rectors
     *
     * @return ImportedClass
     */
    public function method3();

    /**
     * @return array
     */
    public function method4();

    /**
     * @return iterable
     */
    public function method5();

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

use My\Custom\ImportedClass;

interface SomeInterface
{

    public function method1(): \DateTimeImmutable;

    public function method2(): \My\Custom\FQCN;

    /**
     * Note that the imported class will be FQCN but this will be fixed by other rectors
     */
    public function method3(): \My\Custom\ImportedClass;

    public function method4(): array;

    public function method5(): iterable;

}

?>

<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

use My\Custom\ImportedClass;

interface SomeInterface
{

    /**
     * Note that the imported class will be FQCN but this will be fixed by other rectors
     *
     * @param \DateTimeImmutable $a
     * @param \My\Custom\FQCN $b
     * @param ImportedClass $c
     * @param array $d
     * @param iterable $e
     */
    public function method1($a, $b, $c, $d, $e);

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

use My\Custom\ImportedClass;

interface SomeInterface
{

    /**
     * Note that the imported class will be FQCN but this will be fixed by other rectors
     */
    public function method1(\DateTimeImmutable $a, \My\Custom\FQCN $b, \My\Custom\ImportedClass $c, array $d, iterable $e);

}

?>

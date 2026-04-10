<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\RemoveOnlyRedundantPhpdoc;

use My\Custom\ImportedClass;

interface SomeInterface
{

    public function method1(string $whatever);

    /**
     * @param class-string $class
     */
    public function method2(string $class);

    public function method3(ImportedClass $a);
}

class SomeClass implements SomeInterface {
    /**
     * @param string $whatever
     */
    public function method1($whatever) {}

    /**
     * @param class-string $class
     */
    public function method2($class) {}

    /**
     * @param ImportedClass $a
     */
    public function method3($a) {}

}
?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeBasedOnParentClassMethodRector\Fixture\RemoveOnlyRedundantPhpdoc;

use My\Custom\ImportedClass;

interface SomeInterface
{

    public function method1(string $whatever);

    /**
     * @param class-string $class
     */
    public function method2(string $class);

    public function method3(ImportedClass $a);
}

class SomeClass implements SomeInterface {
    public function method1(string $whatever) {}

    /**
     * @param class-string $class
     */
    public function method2(string $class) {}

    public function method3(\My\Custom\ImportedClass $a) {}

}
?>

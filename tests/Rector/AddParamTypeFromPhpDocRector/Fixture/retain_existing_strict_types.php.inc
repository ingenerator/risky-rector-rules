<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param string $foo
     * @param object $bar
     * @param bool $isOk
     */
    public function method1(string $foo, float $bar, $isOk);

    /**
     * @param \My\Custom\OtherThing $foo
     */
    public function withWrongType(\My\Custom\Something $foo);

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param object $bar
     */
    public function method1(string $foo, float $bar, bool $isOk);

    /**
     * @param \My\Custom\OtherThing $foo
     */
    public function withWrongType(\My\Custom\Something $foo);

}

?>

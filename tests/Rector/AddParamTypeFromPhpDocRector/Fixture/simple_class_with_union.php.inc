<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @param string|null $foo
     * @param int|bool $bar
     * @param \stdClass|false|null $isOk
     */
    public function method1($foo, $bar, $isOk) {}

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    public function method1(?string $foo, int|bool $bar, \stdClass|false|null $isOk) {}

}

?>

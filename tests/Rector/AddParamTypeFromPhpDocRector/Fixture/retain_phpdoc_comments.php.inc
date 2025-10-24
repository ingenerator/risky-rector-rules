<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param string $foo The foo stuff
     * @param int $bar
     * @param bool $isOk Other information
     */
    public function method1($foo, $bar, $isOk);

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @param string $foo The foo stuff
     * @param bool $isOk Other information
     */
    public function method1(string $foo, int $bar, bool $isOk);

}

?>

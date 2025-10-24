<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    /**
     * @return string
     */
    public function method1(): string;

    /**
     * @return object
     */
    public function method2(): float;

    /**
     * @return bool
     */
    public function method3(): bool;

    /**
     * @return \My\Custom\OtherThing
     */
    public function withWrongType(): \My\Custom\Something;

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

interface SomeInterface
{

    public function method1(): string;

    /**
     * @return object
     */
    public function method2(): float;

    public function method3(): bool;

    /**
     * @return \My\Custom\OtherThing
     */
    public function withWrongType(): \My\Custom\Something;

}

?>

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
     * Note that we do not change this explicit void - that is covered by the AddReturnTypeFromPhpDocRector
     *
     * @return void
     */
    public function method2();

}

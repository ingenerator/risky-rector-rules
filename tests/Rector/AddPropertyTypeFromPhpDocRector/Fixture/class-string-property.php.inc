<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public $model = InvoiceItem::class;

}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\AddParamTypeFromPhpDocRector\Fixture;

class SomeClass
{

    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public string $model = InvoiceItem::class;

}

?>

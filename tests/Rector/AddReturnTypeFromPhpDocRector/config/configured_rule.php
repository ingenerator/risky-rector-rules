<?php

declare(strict_types=1);

use Ingenerator\RiskyRectorRules\PhpDocToStrictTypes\AddReturnTypeFromPhpDocRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddReturnTypeFromPhpDocRector::class);
};

<?php

declare(strict_types=1);

use Ingenerator\RiskyRectorRules\PhpDocToStrictTypes\AddMethodTypeConfig;
use Ingenerator\RiskyRectorRules\PhpDocToStrictTypes\AddParamTypeFromPhpDocRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AddParamTypeFromPhpDocRector::class, [
        AddMethodTypeConfig::INTERFACES_ONLY => true,
    ]);
};

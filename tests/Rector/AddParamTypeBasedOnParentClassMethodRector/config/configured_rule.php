<?php

declare(strict_types=1);

use Ingenerator\RiskyRectorRules\AddStrictTypes\AddParamTypeBasedOnParentClassMethodRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddParamTypeBasedOnParentClassMethodRector::class);
};

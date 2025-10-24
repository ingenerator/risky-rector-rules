<?php

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

enum TypeUsageContext
{
    case METHOD_PARAM;
    case METHOD_RETURN;
    case PROPERTY;

    public function allowsVoid(): bool
    {
        return $this === self::METHOD_RETURN;
    }
}

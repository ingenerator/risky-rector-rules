<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

final readonly class AddMethodTypeConfig
{
    public const INTERFACES_ONLY = 'interfaces_only';

    public function __construct(
        public bool $interfacesOnly = false,
    ) {
    }

    public static function fromRuleConfig(array $configuration): self
    {
        return new self(
            interfacesOnly: $configuration[self::INTERFACES_ONLY] ?? false
        );
    }
}

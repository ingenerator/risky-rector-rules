<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType;

final class StrictTypeFromDocTypeFactory
{
    public function convertPhpDocType(Type $phpDocType, TypeUsageContext $context): ?Node
    {
        if ($phpDocType instanceof ShortenedObjectType) {
            // Requires special handling to make sure that the type is added correctly *and* the phpdoc is correctly removed
            return new FullyQualified($phpDocType->getFullyQualifiedName());
        }

        if ($phpDocType instanceof ObjectType) {
            // These need to be returned as fully-qualified names
            return new FullyQualified($phpDocType->getClassName());
        }

        if ($phpDocType->isArray()->yes()) {
            // We need to drop any information about the shape / content etc of the array as this isn't valid in a strict type
            return new Identifier('array');
        }

        if ($phpDocType->isIterable()->yes()) {
            // Likewise drop information about `iterable`
            return new Identifier('iterable');
        }

        if ($phpDocType->isScalar()->yes()) {
            return new Identifier($phpDocType->describe(VerbosityLevel::getRecommendedLevelByType($phpDocType)));
        }

        if ($phpDocType->isVoid()->yes() && $context->allowsVoid()) {
            return new Identifier('void');
        }

        // Don't know how to represent this as a strict type
        return null;
    }
}

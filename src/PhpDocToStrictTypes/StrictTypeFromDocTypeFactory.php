<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType;

use function count;

final class StrictTypeFromDocTypeFactory
{
    public function convertPhpDocType(Type $phpDocType, TypeUsageContext $context): ?Node
    {
        if ($phpDocType instanceof UnionType) {
            return $this->convertUnionType($phpDocType, $context);
        }

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

        if ($phpDocType->isClassString()->yes()) {
            return new Identifier('string');
        }

        if ($phpDocType->isScalar()->yes()) {
            return new Identifier($phpDocType->describe(VerbosityLevel::getRecommendedLevelByType($phpDocType)));
        }

        if ($phpDocType->isVoid()->yes() && $context->allowsVoid()) {
            return new Identifier('void');
        }

        if ($phpDocType->isNull()->yes()) {
            return new Identifier('null');
        }

        // Don't know how to represent this as a strict type
        return null;
    }

    private function convertUnionType(UnionType $phpDocType, TypeUsageContext $context): NullableType|Node\UnionType|null
    {
        $types = $phpDocType->getTypes();
        $strictTypes = array_map(fn (Type $type) => $this->convertPhpDocType($type, $context), $types);

        if (count($types) !== count($strictTypes)) {
            // We can't safely convert all the types in the union
            return null;
        }

        $nonNullTypes = array_filter(
            $types,
            static fn (Type $type): bool => ! $type->isNull()->yes(),
        );

        if (count($nonNullTypes) === 1) {
            // It's a long-form e.g. `string|null` so we can just represent that as ?string
            return new NullableType($this->convertPhpDocType($nonNullTypes[0], $context));
        }

        return new Node\UnionType($strictTypes);
    }
}

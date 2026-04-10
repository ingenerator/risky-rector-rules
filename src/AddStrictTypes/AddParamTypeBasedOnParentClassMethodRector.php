<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\AddStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\MethodReflection;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\DeadCode\PhpDoc\TagRemover\ParamTagRemover;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Rector\ValueObject\MethodName;
use Rector\VendorLocker\ParentClassMethodTypeOverrideGuard;

use function assert;
use function count;

class AddParamTypeBasedOnParentClassMethodRector extends AbstractRector
{
    public function __construct(
        private readonly ParentClassMethodTypeOverrideGuard $parentClassMethodTypeOverrideGuard,
        private readonly StaticTypeMapper $staticTypeMapper,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly ParamTagRemover $paramTagRemover,
    ) {
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Class_);
        $hasChanged = false;
        foreach ($node->getMethods() as $classMethod) {
            if ($this->isNames($classMethod, [MethodName::CONSTRUCT, MethodName::DESTRUCT])) {
                continue;
            }

            $hasChanged = $this->refactorMethod($classMethod) || $hasChanged;
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function refactorMethod(ClassMethod $classMethod): bool
    {
        if ($classMethod->isPrivate()) {
            // Private methods can't have any parent to inherit from
            return false;
        }

        // Check if there are any parameters without strict types already defined
        $untypedParamIndices = $this->findUntypedParameterIndices($classMethod);
        if ($untypedParamIndices === []) {
            return false;
        }

        // Attempt to find types for all parameters that are not already typed by checking all parents and interfaces
        $foundParamTypes = array_filter($this->recursivelyFindParamTypes(
            $classMethod,
            array_fill_keys($untypedParamIndices, null),
        ));
        if ($foundParamTypes === []) {
            return false;
        }

        // Assign the found types and remove any redundant phpdoc
        foreach ($foundParamTypes as $index => $type) {
            $classMethod->params[$index]->type = $type;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);
        $this->paramTagRemover->removeParamTagsIfUseless($phpDocInfo, $classMethod);

        return true;
    }

    /**
     * @return list<int>
     */
    private function findUntypedParameterIndices(ClassMethod $classMethod): array
    {
        $untypedParamIndices = [];
        foreach ($classMethod->params as $index => $param) {
            if ( ! $param->type instanceof Node) {
                $untypedParamIndices[] = $index;
            }
        }

        return $untypedParamIndices;
    }

    /**
     * @param array<int, ?Node> $unspecifiedParamTypes
     *
     * @return array<int, ?Node>
     */
    private function recursivelyFindParamTypes(
        ClassMethod|MethodReflection $classMethod,
        array $unspecifiedParamTypes = [],
    ): array {
        $parentMethod = $this->parentClassMethodTypeOverrideGuard->getParentClassMethod($classMethod);

        if ( ! $parentMethod) {
            // Can't find a parent method, so can't inherit from anything
            return $unspecifiedParamTypes;
        }

        if (count($parentMethod->getVariants()) > 1) {
            // Parent method has multiple variants, so can't inherit from anything
            // (shouldn't happen, this is only internal classes)
            return $unspecifiedParamTypes;
        }

        $parentMethodParams = $parentMethod->getVariants()[0]->getParameters();
        foreach (array_keys($unspecifiedParamTypes) as $index) {
            if ($unspecifiedParamTypes[$index] !== null) {
                // We have already found a type for this parameter (e.g. in an earlier recursion)
                continue;
            }

            if ( ! isset($parentMethodParams[$index])) {
                // This parameter doesn't exist in the parent method (and therefore cannot exist in any parent above it)
                // So we can't give it a type, and there is no point recursing to look for it in parents.
                unset($unspecifiedParamTypes[$index]);
                continue;
            }

            // We have found a type declaration for this parameter.
            $unspecifiedParamTypes[$index] = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode(
                $parentMethodParams[$index]->getType(),
                TypeKind::PARAM,
            );
        }

        // If we have now found types for everything that was not specified, we can return
        if ($unspecifiedParamTypes === array_filter($unspecifiedParamTypes)) {
            return $unspecifiedParamTypes;
        }

        // Otherwise we need to recurse to the next parent class / interface to see if we can find types there.
        return $this->recursivelyFindParamTypes($parentMethod, $unspecifiedParamTypes);
    }
}

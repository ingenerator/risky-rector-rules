<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\DeadCode\PhpDoc\TagRemover\ReturnTagRemover;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function assert;

/**
 * @see \tests\Rector\PhpDocToStrictTypes\AddReturnTypeFromPhpDocRectorTest
 */
final class AddReturnTypeFromPhpDocRector extends AbstractRector
{
    public function __construct(
        private readonly ReturnTagRemover $returnTagRemover,
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add strict return types to methods from phpdoc', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        /**
                         * @return string
                         */
                        public function run()
                        {
                        }

                        /**
                         * @return array{foo: string}
                         */
                        public function other()
                        {
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        
                        public function run(): string
                        {
                        }

                        public function other(): array
                        {
                        }
                    }
                    CODE_SAMPLE,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof ClassMethod);

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        $hasChanged = $this->refactorReturnType($node, $phpDocInfo);
        $hasChanged = $this->returnTagRemover->removeReturnTagIfUseless($phpDocInfo, $node) || $hasChanged;

        if ($hasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            return $node;
        }

        return null;
    }

    private function buildStrictTypeFromParamType(Type $phpDocType): ?Node
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

        // Don't know how to represent this as a strict type
        return null;
    }

    private function refactorReturnType(ClassMethod $node, PhpDocInfo $phpDocInfo): bool
    {
        if ($node->returnType instanceof Node) {
            // Already has a strict type
            return false;
        }

        if ( ! $phpDocInfo->getReturnTagValue() instanceof ReturnTagValueNode) {
            // Does not have a phpdoc @return tag
            return false;
        }

        $strictType = $this->buildStrictTypeFromParamType($phpDocInfo->getReturnType());
        if ( ! $strictType instanceof Node) {
            // We can't convert to a strict type
            return false;
        }

        $node->returnType = $strictType;

        return true;
    }
}

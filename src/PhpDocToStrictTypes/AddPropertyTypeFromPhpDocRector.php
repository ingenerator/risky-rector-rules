<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\DeadCode\PhpDoc\TagRemover\VarTagRemover;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function assert;
use function count;

/**
 * @see \tests\Rector\PhpDocToStrictTypes\AddPropertyTypeFromPhpDocRectorTest
 */
final class AddPropertyTypeFromPhpDocRector extends AbstractRector
{
    public function __construct(
        private readonly VarTagRemover $varTagRemover,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly StrictTypeFromDocTypeFactory $typeFactory,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add strict types to properties from phpdoc', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                    
                        /**
                         * @var string
                         */
                        public $foo;

                        /**
                         * @var array{foo: string}
                         */
                        public $other;
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                    
                        public string $foo;

                        /**
                         * @var array{foo: string}
                         */
                        public array $other;
                    }
                    CODE_SAMPLE,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Property);

        if (count($node->props) > 1) {
            // Non-standard code with multiple props defined on a single `public $prop1, $prop2;` etc.
            // It's potentially not safe to assume a `@var` tag (if present at all) covers all of them
            // and to add strict types would be complex as we'd need to split them into separate
            // declarations. Better to let the user fix that first (possibly with a different Rector rule?)
            // and ignore it until they do.
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        $hasChanged = $this->refactorPropertyType($node, $phpDocInfo);
        $hasChanged = $this->varTagRemover->removeVarTagIfUseless($phpDocInfo, $node) || $hasChanged;

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function refactorPropertyType(Property $node, PhpDocInfo $phpDocInfo): bool
    {
        if ($node->type instanceof Node) {
            // Already has a strict type
            return false;
        }

        if ( ! $phpDocInfo->getVarTagValueNode() instanceof VarTagValueNode) {
            // Does not have a phpdoc @var tag
            return false;
        }

        $strictType = $this->typeFactory->convertPhpDocType($phpDocInfo->getVarType(), TypeUsageContext::PROPERTY);
        if ( ! $strictType instanceof Node) {
            // We can't convert to a strict type
            return false;
        }

        $node->type = $strictType;

        return true;
    }
}

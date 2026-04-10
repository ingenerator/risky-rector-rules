<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\DeadCode\PhpDoc\TagRemover\ReturnTagRemover;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function assert;

/**
 * @see \tests\Rector\PhpDocToStrictTypes\AddReturnTypeFromPhpDocRectorTest
 */
final class AddReturnTypeFromPhpDocRector extends AbstractRector implements ConfigurableRectorInterface
{
    private AddMethodTypeConfig $addMethodTypeConfig;

    public function __construct(
        private readonly ReturnTagRemover $returnTagRemover,
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly StrictTypeFromDocTypeFactory $typeFactory,
        private readonly AddMethodTypeGuard $interfaceOnlyGuard,
    ) {
        $this->addMethodTypeConfig = new AddMethodTypeConfig();
    }

    public function configure(array $configuration): void
    {
        $this->addMethodTypeConfig = AddMethodTypeConfig::fromRuleConfig($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add strict return types to methods from phpdoc', [
            new ConfiguredCodeSample(
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
                [
                    AddMethodTypeConfig::INTERFACES_ONLY => false,
                ]
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

        if ($this->interfaceOnlyGuard->shouldSkip($node, $this->addMethodTypeConfig)) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        $hasChanged = $this->refactorReturnType($node, $phpDocInfo);
        $hasChanged = $this->returnTagRemover->removeReturnTagIfUseless($phpDocInfo, $node) || $hasChanged;

        if ($hasChanged) {
            if ($node->name->toString() === '__construct') {
                // prevent __construct return type - but still allow void return tag removal
                $node->returnType = null;
            }
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            return $node;
        }

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

        $strictType = $this->typeFactory->convertPhpDocType($phpDocInfo->getReturnType(), TypeUsageContext::METHOD_RETURN);
        if ( ! $strictType instanceof Node) {
            // We can't convert to a strict type
            return false;
        }

        $node->returnType = $strictType;

        return true;
    }
}

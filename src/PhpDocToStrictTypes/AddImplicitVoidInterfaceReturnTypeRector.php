<?php

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function assert;

/**
 * @see \tests\Rector\PhpDocToStrictTypes\AddImplicitVoidInterfaceReturnTypeRectorTest
 */
final class AddImplicitVoidInterfaceReturnTypeRector extends AbstractRector
{
    public function __construct(
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly ReflectionResolver $reflectionResolver,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add strict void return types to interface methods with no documented return', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeInterface
                    {
                        /**
                         * @param string $command
                         */
                        public function run($command)
                        {
                        }

                        public function something()
                        {
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {

                        /**
                         * @param string $command
                         */
                        public function run($command)
                        {
                        }

                        public function something()
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

        if ($this->shouldSkipClassMethod($node)) {
            return null;
        }

        $node->returnType = new Node\Identifier('void');

        return $node;
    }

    private function shouldSkipClassMethod(ClassMethod $method): bool
    {
        if ($method->returnType instanceof Node) {
            // Already has a strict type
            return true;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($method);
        if ( ! $classReflection?->isInterface()) {
            // Only apply this rule to interfaces
            return true;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($method);

        if ($phpDocInfo->getReturnTagValue() instanceof ReturnTagValueNode) {
            // Has a phpdoc @return tag
            // NB this might be `@return void` but that will be handled by AddReturnTypeFromPhpDocRector
            return true;
        }

        if ($phpDocInfo->hasByName('phpstan-return')) {
            // Has a phpdoc @phpstan-return tag
            // We don't do anything automatic with these
            return true;
        }

        return false;
    }
}

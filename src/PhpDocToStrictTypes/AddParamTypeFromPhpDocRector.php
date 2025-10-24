<?php

/**
 * Based originally on an implementation by Michael Strelan for Drupal in
 * https://git.drupalcode.org/project/drupal/-/blob/cf322b485db62ec80680bd7148c576f20877ab36/core/lib/Drupal/Core/Rector/AddParamTypeFromPhpDocRector.php
 * which was not complete or merged at the time of forking.
 *
 * Adapted to get it working, meet our requirements and add tests etc.
 */

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpDocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\DeadCode\PhpDoc\TagRemover\ParamTagRemover;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function assert;

use const false;
use const true;

/**
 * @see \tests\Rector\PhpDocToStrictTypes\AddParamTypeFromPhpDocRectorTest
 */
final class AddParamTypeFromPhpDocRector extends AbstractRector
{
    public function __construct(
        private readonly ParamTagRemover $paramTagRemover,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly StrictTypeFromDocTypeFactory $typeFactory,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add strict types to method parameters from phpdoc', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        /**
                         * @param string $param
                         * @param array{foo: string} $other
                         */
                        public function run($param, $other)
                        {
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        /**
                         * @param array{foo: string} $other
                         */
                        public function run(string $param, array $other)
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
        $hasChanged = $this->refactorParamTypes($node, $phpDocInfo);
        $hasChanged = $this->paramTagRemover->removeParamTagsIfUseless($phpDocInfo, $node) || $hasChanged;

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function refactorParamTypes(ClassMethod $classMethod, PhpDocInfo $phpDocInfo): bool
    {
        $hasChanged = false;
        foreach ($classMethod->params as $param) {
            if ($param->type instanceof Node) {
                // Already has a strict type assigned, ignore the phpdoc
                continue;
            }
            $paramName = (string) $this->getName($param->var);

            if ( ! $phpDocInfo->getParamTagValueByName($paramName) instanceof ParamTagValueNode) {
                // They have not explicitly provided a phpdoc param type for this
                continue;
            }

            $strictType = $this->typeFactory->convertPhpDocType($phpDocInfo->getParamType($paramName));
            if ( ! $strictType instanceof Node) {
                // We can't safely convert the phpdoc to a strict type
                continue;
            }

            $hasChanged = true;
            $param->type = $strictType;

            if ($param->flags !== 0) {
                $param->setAttribute(AttributeKey::ORIGINAL_NODE, null);
            }
        }

        return $hasChanged;
    }
}

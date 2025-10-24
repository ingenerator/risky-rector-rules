<?php

/**
 * Based originally on an implementation by Michael Strelan for Drupal in
 * https://git.drupalcode.org/project/drupal/-/blob/cf322b485db62ec80680bd7148c576f20877ab36/core/lib/Drupal/Core/Rector/AddParamTypeFromPhpDocRector.php
 * which was not complete or merged at the time of forking.
 *
 * Adapted to get it working, meet our requirements and add tests etc.
 */

declare(strict_types=1);

namespace Ingenerator\RiskyRectorRules\PhpdocToStrictTypes;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\Type\VerbosityLevel;
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
                         */
                        public function run($param)
                        {
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function run(string $param)
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
                continue;
            }
            $paramName = (string) $this->getName($param->var);
            $paramTagValue = $phpDocInfo->getParamTagValueByName($paramName);
            if ( ! $paramTagValue instanceof ParamTagValueNode) {
                continue;
            }
            $paramType = $phpDocInfo->getParamType($paramName);
            if ( ! $paramType->isScalar()->yes()) {
                continue;
            }

            $hasChanged = true;

            $param->type = new Identifier($paramType->describe(VerbosityLevel::getRecommendedLevelByType($paramType)));
            if ($param->flags !== 0) {
                $param->setAttribute(AttributeKey::ORIGINAL_NODE, null);
            }
        }

        return $hasChanged;
    }
}

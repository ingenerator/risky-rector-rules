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
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\VerbosityLevel;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\DeadCode\PhpDoc\TagRemover\ParamTagRemover;
use Rector\FamilyTree\NodeAnalyzer\ClassChildAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use const false;
use const true;

/**
 * @see \tests\Rector\PhpDocToStrictTypes\AddParamTypeFromPhpDocRectorTest
 */
final class AddParamTypeFromPhpDocRector extends AbstractRector
{
    private bool $hasChanged = false;

    public function __construct(
        private readonly ReflectionResolver $reflectionResolver,
        private readonly ClassChildAnalyzer $classChildAnalyzer,
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

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     *                          The class method node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof ClassMethod && $this->shouldSkipClassMethod($node)) {
            return null;
        }
        $this->hasChanged = false;
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $this->refactorParamTypes($node, $phpDocInfo);
        $hasChanged = $this->paramTagRemover->removeParamTagsIfUseless($phpDocInfo, $node);
        if ($this->hasChanged) {
            return $node;
        }
        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function shouldSkipClassMethod(ClassMethod $classMethod): bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($classMethod);
        if ( ! $classReflection instanceof ClassReflection) {
            return false;
        }
        if ( ! $classReflection->isInterface()) {
            return true;
        }
        $methodName = $this->nodeNameResolver->getName($classMethod);

        return $this->classChildAnalyzer->hasParentClassMethod($classReflection, $methodName);
    }

    /**
     * @param ClassMethod $classMethod
     *                                 The class method node
     * @param PhpDocInfo  $phpDocInfo
     *                                 The PhpDocInfo utility
     */
    private function refactorParamTypes(Node $classMethod, PhpDocInfo $phpDocInfo): void
    {
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
            $this->hasChanged = true;
            $param->type = new Identifier($paramType->describe(VerbosityLevel::getRecommendedLevelByType($paramType)));
            if ($param->flags !== 0) {
                $param->setAttribute(AttributeKey::ORIGINAL_NODE, null);
            }
        }
    }
}

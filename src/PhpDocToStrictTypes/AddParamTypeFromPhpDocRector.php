<?php

declare (strict_types=1);

// cspell:ignore Symplify

namespace Drupal\Core\Rector;

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

/**
 * @see \Rector\Tests\Php80\Rector\FunctionLike\MixedTypeRector\MixedTypeRectorTest
 */
final class AddParamTypeFromPhpDocRector extends AbstractRector {
    /**
     * @readonly
     * @var \Rector\Reflection\ReflectionResolver
     */
    private $reflectionResolver;
    /**
     * @readonly
     * @var \Rector\FamilyTree\NodeAnalyzer\ClassChildAnalyzer
     */
    private $classChildAnalyzer;
    /**
     * @readonly
     * @var \Rector\DeadCode\PhpDoc\TagRemover\ParamTagRemover
     */
    private $paramTagRemover;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     */
    private $phpDocInfoFactory;
    /**
     * @var bool
     */
    private $hasChanged = \false;

    public function __construct(ReflectionResolver $reflectionResolver, ClassChildAnalyzer $classChildAnalyzer, ParamTagRemover $paramTagRemover, PhpDocInfoFactory $phpDocInfoFactory) {
        $this->reflectionResolver = $reflectionResolver;
        $this->classChildAnalyzer = $classChildAnalyzer;
        $this->paramTagRemover = $paramTagRemover;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }

    public function getRuleDefinition() : RuleDefinition {
        return new RuleDefinition('Change string docs type to string typed', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @param string $param
     */
    public function run($param)
    {
    }
}
CODE_SAMPLE
            , <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(string $param)
    {
    }
}
CODE_SAMPLE
        ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array {
        return [ClassMethod::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *   The class method node.
     */
    public function refactor(Node $node) : ?Node {
        if ($node instanceof ClassMethod && $this->shouldSkipClassMethod($node)) {
            return NULL;
        }
        $this->hasChanged = \false;
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $this->refactorParamTypes($node, $phpDocInfo);
        $hasChanged = $this->paramTagRemover->removeParamTagsIfUseless($phpDocInfo, $node);
        if ($this->hasChanged) {
            return $node;
        }
        if ($hasChanged) {
            return $node;
        }
        return NULL;
    }

    private function shouldSkipClassMethod(ClassMethod $classMethod) : bool {
        $classReflection = $this->reflectionResolver->resolveClassReflection($classMethod);
        if (!$classReflection instanceof ClassReflection) {
            return \false;
        }
        if (!$classReflection->isInterface()) {
            return \true;
        }
        $methodName = $this->nodeNameResolver->getName($classMethod);
        return $this->classChildAnalyzer->hasParentClassMethod($classReflection, $methodName);
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     *   The class method node.
     * @param \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo $phpDocInfo
     *   The PhpDocInfo utility.
     */
    private function refactorParamTypes($classMethod, PhpDocInfo $phpDocInfo) : void {
        foreach ($classMethod->params as $param) {
            if ($param->type instanceof Node) {
                continue;
            }
            $paramName = (string) $this->getName($param->var);
            $paramTagValue = $phpDocInfo->getParamTagValueByName($paramName);
            if (!$paramTagValue instanceof ParamTagValueNode) {
                continue;
            }
            $paramType = $phpDocInfo->getParamType($paramName);
            if (!$paramType->isScalar()->yes()) {
                continue;
            }
            $this->hasChanged = \true;
            $param->type = new Identifier($paramType->describe(VerbosityLevel::getRecommendedLevelByType($paramType)));
            if ($param->flags !== 0) {
                $param->setAttribute(AttributeKey::ORIGINAL_NODE, NULL);
            }
        }
    }

}

<?php

declare (strict_types=1);
namespace Rector\Symfony\Symfony52\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#validator
 * @see \Rector\Symfony\Tests\Symfony52\Rector\MethodCall\ValidatorBuilderEnableAnnotationMappingRector\ValidatorBuilderEnableAnnotationMappingRectorTest
 */
final class ValidatorBuilderEnableAnnotationMappingRector extends AbstractRector
{
    /**
     * @readonly
     */
    private ValueResolver $valueResolver;
    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Migrates from deprecated ValidatorBuilder->enableAnnotationMapping($reader) to ValidatorBuilder->enableAnnotationMapping(true)->setDoctrineAnnotationReader($reader)', [new CodeSample(<<<'CODE_SAMPLE'
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\ValidatorBuilder;

class SomeClass
{
    public function run(ValidatorBuilder $builder, Reader $reader)
    {
        $builder->enableAnnotationMapping($reader);
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\ValidatorBuilder;

class SomeClass
{
    public function run(ValidatorBuilder $builder, Reader $reader)
    {
        $builder->enableAnnotationMapping(true)->setDoctrineAnnotationReader($reader);
    }
}
CODE_SAMPLE
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [MethodCall::class];
    }
    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node) : ?Node
    {
        if (!$this->isName($node->name, 'enableAnnotationMapping')) {
            return null;
        }
        if (!$this->isObjectType($node->var, new ObjectType('Symfony\\Component\\Validator\\ValidatorBuilder'))) {
            return null;
        }
        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }
        if ($this->valueResolver->isTrueOrFalse($firstArg->value)) {
            return null;
        }
        if (!$this->isObjectType($firstArg->value, new ObjectType('Doctrine\\Common\\Annotations\\Reader'))) {
            return null;
        }
        $readerType = $firstArg->value;
        $firstArg->value = $this->nodeFactory->createTrue();
        return $this->nodeFactory->createMethodCall($node, 'setDoctrineAnnotationReader', [$readerType]);
    }
}

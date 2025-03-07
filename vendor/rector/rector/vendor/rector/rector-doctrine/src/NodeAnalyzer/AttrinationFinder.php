<?php

declare (strict_types=1);
namespace Rector\Doctrine\NodeAnalyzer;

use PhpParser\Node\Attribute;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
/**
 * @api
 */
final class AttrinationFinder
{
    /**
     * @readonly
     */
    private PhpDocInfoFactory $phpDocInfoFactory;
    /**
     * @readonly
     */
    private \Rector\Doctrine\NodeAnalyzer\AttributeFinder $attributeFinder;
    public function __construct(PhpDocInfoFactory $phpDocInfoFactory, \Rector\Doctrine\NodeAnalyzer\AttributeFinder $attributeFinder)
    {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->attributeFinder = $attributeFinder;
    }
    /**
     * @param \PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Param $node
     * @return \Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode|\PhpParser\Node\Attribute|null
     */
    public function getByOne($node, string $name)
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        if ($phpDocInfo instanceof PhpDocInfo && $phpDocInfo->hasByAnnotationClass($name)) {
            return $phpDocInfo->getByAnnotationClass($name);
        }
        return $this->attributeFinder->findAttributeByClass($node, $name);
    }
    /**
     * @param \PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Param $node
     */
    public function hasByOne($node, string $name) : bool
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        if ($phpDocInfo instanceof PhpDocInfo && $phpDocInfo->hasByAnnotationClass($name)) {
            return \true;
        }
        $attribute = $this->attributeFinder->findAttributeByClass($node, $name);
        return $attribute instanceof Attribute;
    }
    /**
     * @param string[] $classNames
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\Property $property
     */
    public function hasByMany($property, array $classNames) : bool
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($property);
        if ($phpDocInfo instanceof PhpDocInfo && $phpDocInfo->hasByAnnotationClasses($classNames)) {
            return \true;
        }
        $attribute = $this->attributeFinder->findAttributeByClasses($property, $classNames);
        return $attribute instanceof Attribute;
    }
    /**
     * @param string[] $classNames
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\Property $property
     * @return \Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode|\PhpParser\Node\Attribute|null
     */
    public function getByMany($property, array $classNames)
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($property);
        if ($phpDocInfo instanceof PhpDocInfo) {
            $foundDoctrineAnnotationTagValueNode = $phpDocInfo->getByAnnotationClasses($classNames);
            if ($foundDoctrineAnnotationTagValueNode instanceof DoctrineAnnotationTagValueNode) {
                return $foundDoctrineAnnotationTagValueNode;
            }
        }
        return $this->attributeFinder->findAttributeByClasses($property, $classNames);
    }
}

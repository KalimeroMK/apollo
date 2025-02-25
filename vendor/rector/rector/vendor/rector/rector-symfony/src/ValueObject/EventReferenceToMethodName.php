<?php

declare (strict_types=1);
namespace Rector\Symfony\ValueObject;

use PhpParser\Node\Expr\ClassConstFetch;
use Rector\Symfony\Contract\EventReferenceToMethodNameInterface;
final class EventReferenceToMethodName implements EventReferenceToMethodNameInterface
{
    /**
     * @readonly
     */
    private ClassConstFetch $classConstFetch;
    /**
     * @readonly
     */
    private string $methodName;
    public function __construct(ClassConstFetch $classConstFetch, string $methodName)
    {
        $this->classConstFetch = $classConstFetch;
        $this->methodName = $methodName;
    }
    public function getClassConstFetch() : ClassConstFetch
    {
        return $this->classConstFetch;
    }
    public function getMethodName() : string
    {
        return $this->methodName;
    }
}

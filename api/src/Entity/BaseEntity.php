<?php

namespace App\Entity;

use App\Helpers\Str;
use ReflectionClass;
use ReflectionException;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class BaseEntity
 *
 * @package App\Entity
 */
class BaseEntity
{
    /**
     * Fills entity attributes
     *
     * @param $properties
     */
    public function fill($properties)
    {
        $reflection = new ReflectionClass(static::class);

        foreach ($properties as $property => $value) {
            try {
                $reader = new AnnotationReader();

                /** @var Type $typeAnnotation */
                $reflectionProperty = $reflection->getProperty($property);
                $typeAnnotation     = $reader->getPropertyAnnotation($reflectionProperty, Type::class);

                if (Str::contains($typeAnnotation->type, 'Entity')) {
                    continue;
                }

                $reflectionMethod = $reflection->getMethod('set' . Str::studly($property));

                $this->{$reflectionMethod->getName()}($value);
            } catch (ReflectionException $e) {
                continue;
            }
        }
    }
}

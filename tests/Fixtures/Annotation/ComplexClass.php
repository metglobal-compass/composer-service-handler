<?php

namespace Metglobal\ServiceHandler\Tests\Fixtures\Annotation;

use Metglobal\ServiceHandler\Annotation\DI;
use Metglobal\ServiceHandler\Annotation\Tag;

/**
 * @DI(
 *     id="sample_bundle.complex_class",
 *     arguments={"@sample_bundle.simple_class"},
 *     factory={"@sample_bundle.complex_class_factory", "create"},
 *     calls={
 *      {"setField",{"value"}},
 *      {"setAnotherField",{"anotherValue"}},
 *     },
 *     public=false,
 *     abstract=true,
 *     lazy=true,
 *     parent="sample_bundle.parent_of_complex_class",
 *     tags={
 *      @Tag(name="tag_name", event="eventName", method="eventMethod", priority=15)
 *     },
 *     autoconfigure=false,
 *     autowire=true
 * )
 * @package Metglobal\ServiceHandler\Tests\Annotation
 */
class ComplexClass
{
}

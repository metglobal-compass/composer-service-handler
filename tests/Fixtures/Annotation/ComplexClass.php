<?php

namespace SymfonyAutoDiYml\Tests\Fixtures\Annotation;

use SymfonyAutoDiYml\Annotation\DI;
use SymfonyAutoDiYml\Annotation\Tag;

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
 *     parent="sample_bundle.parent_of_complex_class",
 *     tags={
 *      @Tag(name="tag_name", event="eventName", method="eventMethod")
 *     },
 * )
 * @package SymfonyAutoDiYml\Tests\Annotation
 */
class ComplexClass
{
}

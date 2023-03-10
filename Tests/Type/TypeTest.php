<?php

namespace SGK\BarcodeBundle\Tests\Type;

use PHPUnit\Framework\TestCase;
use SGK\BarcodeBundle\Type\Type;

/**
 * Class TypeTest.
 */
class TypeTest extends TestCase
{
    /**
     * testConfigureOptions.
     */
    public function testInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $type = new Type();
        $type->getDimension('Unknown Type');
    }
}

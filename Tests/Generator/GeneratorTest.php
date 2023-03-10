<?php

namespace SGK\BarcodeBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use SGK\BarcodeBundle\Generator\Generator;

/**
 * Class GeneratorTest.
 */
class GeneratorTest extends TestCase
{
    /**
     * @return array
     */
    public function getOptions()
    {
        return [[['code' => '0123456789', 'type' => 'c128', 'format' => 'html', 'width' => 2, 'height' => 30, 'color' => 'black']], [['code' => '0123456789', 'type' => 'c39', 'format' => 'svg']], [['code' => '0123456789', 'type' => 'qrcode', 'format' => 'png', 'width' => 5, 'height' => 5, 'color' => [0, 0, 0]]]];
    }

    /**
     * testGenerate.
     *
     * @param array $options
     *
     * @medium
     *
     * @dataProvider getOptions
     */
    public function testGenerate($options = [])
    {
        $generator = new Generator();

        $this->assertTrue(is_string($generator->generate($options)));
    }

    /**
     * @return array
     */
    public function getErrorOptions()
    {
        return [[['code' => '0123456789']], [['code' => '0123456789', 'type' => 'Unknown Type', 'format' => 'html']], [['code' => '0123456789', 'type' => 'c128', 'format' => 'Unknown Format']], [['code' => '0123456789', 'type' => 'c39', 'format' => 'svg', 'width' => 'width is int']], [['code' => '0123456789', 'type' => 'qrcode', 'format' => 'png', 'width' => 5, 'height' => 5, 'color' => 5]]];
    }

    /**
     * testConfigureOptions.
     *
     * @param array $options
     *
     * @medium
     *
     * @dataProvider getErrorOptions
     */
    public function testConfigureOptions($options = [])
    {
        $this->expectException(\Exception::class);
        $generator = new Generator();
        $generator->generate($options);
    }
}

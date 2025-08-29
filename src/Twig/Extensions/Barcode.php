<?php

namespace SGK\BarcodeBundle\Twig\Extensions;

use SGK\BarcodeBundle\Generator\Generator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class Project_Twig_Extension.
 */
class Barcode extends AbstractExtension
{
    /**
     * @var Generator
     */
    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'barcode',
                function($options = []) {
                    echo $this->generator->generate($options);
                }
            ),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'barcode';
    }
}

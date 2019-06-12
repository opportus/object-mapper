<?php


namespace Opportus\ObjectMapper\Tests\Map\Route\Point;


use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use PHPUnit\Framework\TestCase;

class PropertyPointTest extends TestCase
{

    /**
     * @var string
     */
    public $testedProperty;

    public function testCorrectPropertyPointConstruction(): void
    {
        $fqn = 'Opportus\ObjectMapper\Tests\Map\Route\Point\PropertyPointTest::$testedProperty';
        $propertyPoint = new PropertyPoint($fqn);

        $this->assertEquals($fqn, $propertyPoint->getFqn());
        $this->assertEquals('Opportus\ObjectMapper\Tests\Map\Route\Point\PropertyPointTest', $propertyPoint->getClassFqn());
        $this->assertEquals('testedProperty', $propertyPoint->getName());
    }

}
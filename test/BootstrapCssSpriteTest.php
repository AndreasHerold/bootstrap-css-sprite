<?php

namespace CssSpriteTest\BootstrapCssSprite;

use CssSprite\BootstrapCssSprite;
use PHPUnit_Framework_Testcase as TestCase;

/**
 * Class BootstrapCssSpriteTest
 *
 * @version 1.0
 * @license MIT <http://opensource.org/licenses/MIT>
 * @copyright 2014 AndreasHerold
 * @author Andreas Herold <andreas.herold at googlemail.com>
 */
class BootstrapCssSpriteTest extends TestCase
{
    /**
     * Testmethod for BootstrapCssSprite::addError
     */
    public function testAddError()
    {
        $sprite = new BootstrapCssSprite();

        $sprite->addError(BootstrapCssSprite::ERROR_NO_SOURCE_IMAGES, 'Test No Source Images');

        $errors = $sprite->getErrors();

        $this->assertEquals(1, count($errors));
    }

    /**
     * Testmethod for BootstrapCssSprite::_initDestImage
     */
    public function testInitDestImage()
    {
        $sprite = new BootstrapCssSprite();

        $method = $this->getProtectedMethod($sprite, '_initDestImage');

        $image = $method->invoke($sprite, 10, 10);
        $this->assertEquals('gd', get_resource_type($image));
    }

    /**
     * Testmethod for BootstrapCssSprite::_getImageClassName
     */
    public function testGetImageClassName()
    {
        $sourcePath = '/path/to/image';
        $fileName = 'imageName';
        $sprite = new BootstrapCssSprite();
        $sprite->setImgSourcePath($sourcePath);

        $method = $this->getProtectedMethod($sprite, '_getImageClassName');

        $path = $sourcePath . DIRECTORY_SEPARATOR . $fileName . '.png';
        $data = array('ext' => 'png');
        $this->assertEquals('.img-' . $fileName, $method->invoke($sprite, $path, $data));
    }

    /**
     * Testmethod for BootstrapCssSprite::_isMagicAction
     */
    public function testIsMagicAction()
    {
        $class = '.img-imageName';
        $sprite = new BootstrapCssSprite();
        $method = $this->getProtectedMethod($sprite, '_isMagicAction');

        $this->assertFalse($method->invoke($sprite, $class));
        $class .= '.hover';
        $this->assertTrue($method->invoke($sprite, $class));
    }

    /**
     * Testmethod for BootstrapCssSprite::_setImageCssData
     */
    public function testSetImageCssData()
    {
        $sprite = new BootstrapCssSprite();
        $method = $this->getProtectedMethod($sprite, '_setImageCssData');

        $cssList = array();
        $class = '.img-imageName';
        $data = array('x' => 0);

        $method->invoke($sprite, $cssList, $class, $data);

        $this->assertArrayHasKey('selectors', $cssList[0]);
        $this->assertArrayHasKey('styles', $cssList[0]);
    }
    /**
     * @param $class
     * @param $method
     * @return \ReflectionMethod
     */
    protected function getProtectedMethod($class, $method)
    {
        $reflectionClass = new \ReflectionClass($class);
        $method = $reflectionClass->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }
}

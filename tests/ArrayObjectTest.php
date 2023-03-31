<?php

/**
 * Array object unit test.
 *
 * @package     Plugin
 * @subpackage  MpDevToolsPHPUnit
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

declare(strict_types=1);

namespace CONTENIDO\Plugin\MpDevToolsPHPUnit;

use CONTENIDO\Plugin\MpDevTools\ArrayObject;
use PHPUnit\Framework\TestCase;

class ArrayObjectTest extends TestCase
{

    public function testConstructor()
    {
        $obj = new ArrayObject();
        $this->assertEquals(null, $obj[0]);

        $obj = new ArrayObject();
        $this->assertEquals(null, $obj['foo']);

        $obj = new ArrayObject(['bar']);
        $this->assertEquals('bar', $obj[0]);

        $obj = new ArrayObject(['foo' => 'bar']);
        $this->assertEquals('bar', $obj['foo']);
    }

    public function testMagicGet()
    {
        $obj = new ArrayObject();
        $this->assertEquals(null, $obj[0]);

        $obj = new ArrayObject();
        $this->assertEquals(null, $obj->foo);

        $obj = new ArrayObject();
        $this->assertEquals(null, $obj['foo']);

        $obj = new ArrayObject(['bar']);
        $this->assertEquals('bar', $obj[0]);

        $obj = new ArrayObject(['foo' => 'bar']);
        $this->assertEquals('bar', $obj->foo);

        $obj = new ArrayObject(['foo' => 'bar']);
        $this->assertEquals('bar', $obj['foo']);
    }

    public function testGet()
    {
        $obj = new ArrayObject();
        $this->assertEquals(null, $obj->get(0));

        $obj = new ArrayObject();
        $this->assertEquals(null, $obj->get('foo'));

        $obj = new ArrayObject(['bar']);
        $this->assertEquals('bar', $obj->get(0));

        $obj = new ArrayObject(['foo' => 'bar']);
        $this->assertEquals('bar', $obj->get('foo'));
    }

    public function testMagicSet()
    {
        $obj = new ArrayObject();
        $obj[0] = 'bar';
        $this->assertEquals('bar', $obj[0]);

        $obj = new ArrayObject();
        $obj['foo'] = 'bar';
        $this->assertEquals('bar', $obj['foo']);
    }

    public function testSet()
    {
        $obj = new ArrayObject();
        $obj->set(0, 'bar');
        $this->assertEquals('bar', $obj[0]);

        $obj = new ArrayObject();
        $obj->set('foo', 'bar');
        $this->assertEquals('bar', $obj['foo']);
    }

    public function testMagicIsset()
    {
        $obj = new ArrayObject();
        $this->assertEquals(false, isset($obj[0]));

        $obj = new ArrayObject();
        $obj['foo'] = 'bar';
        $this->assertEquals(true, isset($obj['foo']));
    }

    public function testIsset()
    {
        $obj = new ArrayObject();
        $this->assertEquals(false, $obj->isset(0));

        $obj = new ArrayObject();
        $obj['foo'] = 'bar';
        $this->assertEquals(true, $obj->isset('foo'));
    }

    public function testMagicUnset()
    {
        $obj = new ArrayObject();
        $obj[1] = '2';
        $obj['foo'] = 'bar';
        unset($obj['foo']);
        $this->assertEquals(false, isset($obj['foo']));
        $this->assertEquals(true, isset($obj[1]));
    }

    public function testUnset()
    {
        $obj = new ArrayObject();
        $obj[1] = '2';
        $obj['foo'] = 'bar';
        $obj->unset('foo');
        $this->assertEquals(false, isset($obj['foo']));
        $this->assertEquals(true, isset($obj[1]));
    }

    public function testFetch()
    {
        $obj = new ArrayObject();

        $obj->foo = [
            'bar' => [
                'baz' => 'asdf'
            ]
        ];

        $this->assertEquals('asdf', $obj->fetch('foo.bar.baz'));

        $expected = ['baz' => 'asdf'];
        $this->assertEquals($expected, $obj->fetch('foo.bar'));

        $expected = ['bar' => ['baz' => 'asdf']];
        $this->assertEquals($expected, $obj->fetch('foo'));

        $this->assertEquals(null, $obj->fetch(''));
        $this->assertEquals(null, $obj->fetch('not_valid_key'));
        $this->assertEquals(null, $obj->fetch('not.valid.key'));

        $obj = new ArrayObject();

        $obj->foo = [
            0 => [
                'baz' => 'asdf'
            ]
        ];

        $expected = ['baz' => 'asdf'];
        $this->assertEquals($expected, $obj->fetch('foo.0'));

        $obj = new ArrayObject();

        $obj[0] = [
            'bar' => [
                'baz' => 'asdf'
            ]
        ];

        $expected = ['baz' => 'asdf'];
        $this->assertEquals($expected, $obj->fetch('0.bar'));
    }

    public function testOffsetGet()
    {
        $obj = new ArrayObject();
        $this->assertEquals(null, $obj->offsetGet(0));

        $obj = new ArrayObject();
        $obj[1] = '2';
        $this->assertEquals('2', $obj->offsetGet(1));

        $obj = new ArrayObject();
        $obj->foo = 'bar';
        $this->assertEquals('bar', $obj->offsetGet('foo'));
    }

}
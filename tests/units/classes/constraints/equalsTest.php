<?php

namespace mageekguy\atoum\phpunit\tests\units\constraints;

use
	mageekguy\atoum,
	mageekguy\atoum\phpunit\constraints\equals as testedClass
;

require_once __DIR__ . '/../../../../vendor/phpunit/phpunit/tests/_files/Author.php';
require_once __DIR__ . '/../../../../vendor/phpunit/phpunit/tests/_files/Book.php';
require_once __DIR__ . '/../../../../vendor/phpunit/phpunit/tests/_files/ClassWithToString.php';
require_once __DIR__ . '/../../../../vendor/phpunit/phpunit/tests/_files/SampleClass.php';
require_once __DIR__ . '/../../../../vendor/phpunit/phpunit/tests/_files/Struct.php';

class equals extends \PHPUnit_Framework_TestCase
{
	public function testClass()
	{
		$this->assertInstanceOf('mageekguy\atoum\phpunit\constraint', new testedClass(uniqid()));
	}

	/**
	 * @dataProvider equalProvider
	 */
	public function testAssertEqualsSucceeds($expected, $actual, $delta = null, $canonicalize = null, $ignoreCase = null)
	{
		$constraint = new testedClass($expected, null, $delta, 10, $canonicalize, $ignoreCase);

		$this->assertSame($constraint, $constraint->evaluate($actual));
	}

	public function equalProvider()
	{
		return array_merge($this->equalValues(), $this->sameValues());
	}

	protected function equalValues()
	{
		$book1                  = new \book();
		$book1->author          = new \author('Terry Pratchett');
		$book1->author->books[] = $book1;
		$book2                  = new \book();
		$book2->author          = new \author('Terry Pratchett');
		$book2->author->books[] = $book2;

		$object1  = new \sampleClass(4, 8, 15);
		$object2  = new \sampleClass(4, 8, 15);
		$storage1 = new \splObjectStorage();
		$storage1->attach($object1);
		$storage2 = new \splObjectStorage();
		$storage2->attach($object1);

		return array(
			array('a', 'A', 0, false, true),
			array(array('a' => 1, 'b' => 2), array('b' => 2, 'a' => 1)),
			array(array(1), array('1')),
			array(array(3, 2, 1), array(2, 3, 1), 0, true),
			array(2.3, 2.5, 0.5),
			// Asserting on floats (with delta) in arrays is not supported
			//array(array(2.3), array(2.5), 0.5),
			// Asserting on floats (with delta) in nested arrays is not supported
			//array(array(array(2.3)), array(array(2.5)), 0.5),
			// Asserting on floats (with delta) on objects\' properties is not supported
			//array(new \struct(2.3), new \struct(2.5), 0.5),
			// Asserting on floats (with delta) on objects\' properties in arrays is not supported
			//array(array(new \struct(2.3)), array(new \struct(2.5)), 0.5),
			array(1, 2, 1),
			array($object1, $object2),
			// Asserting on objects with cyclic dependencies is not supported
			//array($book1, $book2),
			array($storage1, $storage2),
			array(
				new \dateTime('2013-03-29 04:13:35', new \dateTimeZone('America/New_York')),
				new \dateTime('2013-03-29 04:13:35', new \dateTimeZone('America/New_York')),
			),
			// Asserting on dates with delta is not supported
			//array(
			//	new \dateTime('2013-03-29 04:13:35', new \dateTimeZone('America/New_York')),
			//	new \dateTime('2013-03-29 04:13:25', new \dateTimeZone('America/New_York')),
			//	10
			//),
			//array(
			//	new \dateTime('2013-03-29 04:13:35', new \dateTimeZone('America/New_York')),
			//	new \dateTime('2013-03-29 04:14:40', new \dateTimeZone('America/New_York')),
			//	65
			//),
			array(
				new \dateTime('2013-03-29', new \dateTimeZone('America/New_York')),
				new \dateTime('2013-03-29', new \dateTimeZone('America/New_York')),
			),
			array(
				new \dateTime('2013-03-29 04:13:35', new \dateTimeZone('America/New_York')),
				new \dateTime('2013-03-29 03:13:35', new \dateTimeZone('America/Chicago')),
			),
			// Asserting on dates with delta is not supported
			//array(
			//	new \dateTime('2013-03-29 04:13:35', new \dateTimeZone('America/New_York')),
			//	new \dateTime('2013-03-29 03:13:49', new \dateTimeZone('America/Chicago')),
			//	15
			//),
			array(
				new \dateTime('2013-03-30', new \dateTimeZone('America/New_York')),
				new \dateTime('2013-03-29 23:00:00', new \dateTimeZone('America/Chicago')),
			),
			// Asserting on dates with delta is not supported
			//array(
			//	new \dateTime('2013-03-30', new \dateTimeZone('America/New_York')),
			//	new \dateTime('2013-03-29 23:01:30', new \dateTimeZone('America/Chicago')),
			//	100
			//),
			array(
				new \dateTime('@1364616000'),
				new \dateTime('2013-03-29 23:00:00', new \dateTimeZone('America/Chicago')),
			),
			array(
				new \dateTime('2013-03-29T05:13:35-0500'),
				new \dateTime('2013-03-29T04:13:35-0600'),
			),
			array(0, '0'),
			array('0', 0),
			array(2.3, '2.3'),
			array('2.3', 2.3),
			array((string) (1/3), 1 - 2/3),
			array(1/3, (string) (1 - 2/3)),
			array('string representation', new \classWithToString()),
			array(new \classWithToString(), 'string representation'),
		);
	}

	protected function sameValues()
	{
		$object = new \sampleClass(4, 8, 15);
		$resource = fopen(__FILE__, 'r');

		return array(
			array(null, null),
			array('a', 'a'),
			array(0, 0),
			array(2.3, 2.3),
			array(1/3, 1 - 2/3),
			array(log(0), log(0)),
			array(array(), array()),
			array(array(0 => 1), array(0 => 1)),
			array(array(0 => null), array(0 => null)),
			array(array('a', 'b' => array(1, 2)), array('a', 'b' => array(1, 2))),
			array($object, $object),
			array($resource, $resource),
		);
	}
}

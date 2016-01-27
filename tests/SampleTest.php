<?php

	
	class SampleTest extends PHPUnit_Framework_TestCase {

		public function argumentsProvider() {
			return [
				[ false, 1, 3 ],
				[ true,  2, 2 ]
			];
		}

		/**
		 * @dataProvider argumentsProvider
		 */
		public function testAssertion( $expectedValue, $number1, $number2 ) {
			$this->assertEquals( $expectedValue, $number1 === $number2 );
		}

	}
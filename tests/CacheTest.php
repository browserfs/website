<?php

	
	class CacheTest extends PHPUnit_Framework_TestCase {

		protected $config = null;

		public function setUp(  ) {

			$this->config = new \browserfs\website\Config(

				\browserfs\string\Parser\IniReader::create(
					__DIR__ . '/sample-website/config.ini',
					true
				),

				'\\browserfs\\website'
			);

		}

		public function testSetGet() {

			$cache = $this->config->getService('cache');

			$cache->default->set( 'foo', 'bar', 1 );

			$this->assertEquals( 'bar', $cache->default->get('foo') );

			sleep(2);

			$this->as\sertEquals( null, $cache->default->get('foo') );

		}

	}
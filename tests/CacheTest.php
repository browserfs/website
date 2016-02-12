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

		}

		public function testSetGetAfterCacheHasExpired() {

			$cache = $this->config->getService('cache');

			$cache->default->set('foo','willexpire', 1);

			sleep(2); // sleep two seconds in order to let the cache expire

			$this->assertEquals( null, $cache->default->get('foo') );

		}

		public function testFlushFunctionality() {

			$cache = $this->config->getService('cache');

			$cache->default->set('foo', 'bar', 1 );

			$cache->default->clear();

			$this->assertEquals( null, $cache->default->get('foo') );

		}

		public function testDeleteFunctionality() {

			$cache = $this->config->getService('cache');

			$cache->default->set('foo', 'willbedeleted', 1 );

			$cache->default->delete('foo');

			$this->assertEquals( null, $cache->default->get('foo') );
		}

	}
<?php

	
	class ConfigTest extends PHPUnit_Framework_TestCase {

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

		public function testIfConfigurationHasModules() {

			$this->assertEquals( true, count( $this->config->getRegisteredServicesNames() ) > 0 );

		}

		public function testIfConfigurationHasLoadedItsServices() {

			$this->assertEquals( true, $this->config->serviceLoaded('staging' ) );
			$this->assertEquals( true, $this->config->serviceLoaded('website' ) );

			$this->assertEquals( false, $this->config->serviceLoaded( 'aservice that does not exist' ) );

		}

		public function testWebsiteService() {

			$website = $this->config->getService('website');

			$this->assertEquals( true, is_dir( $website->htdocs() ) );
			$this->assertEquals( 'www.example.com', $website->name() );
			$this->assertEquals( 'nginx', $website->webserver() );

		}

		public function testStagingService() {

			$staging = $this->config->getService('staging');

			$this->assertEquals( 'all',          $staging->getPhpIni('error_reporting') );
			$this->assertEquals( '256M',         $staging->getPhpIni( 'memory_limit' ) );
			$this->assertEquals( 'on',           $staging->getPhpIni( 'display_errors' ) );

			$this->assertEquals( '10.200.203.2', $staging->getPhpDefine('SERVER_IP') );
			$this->assertEquals( './templates/', $staging->getPhpDefine('TEMPLATE_FOLDER') );

		}

		public function testDatabaseService() {

			$db = $this->config->getService('database');

			$results = $db
				->primary          // Database source name is accessed via the getter
				->table( 'test' )
				->select( null )
				->where( [ 
				   		'id' => [ 
				   			'$gt' => 0 
				   		] 
				   	])
				->skip( 2 )
				->limit( 3 )
				->run();

		}

	}
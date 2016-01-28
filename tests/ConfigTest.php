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

			$queries = [];

			try {
				$db->primary->connect();
			} catch ( \Exception $e ) {
				echo "\n\nDATABASE SERVICE SKIPPED: The testing environment does not provide required database support";
				$this->markTestSkipped( 'The testing environment does not provide require database support' );
				return;
			}

			$db->primary->on( 'query', function( \browserfs\Event $e ) use ( &$queries ) {
				
				$tableName = $e->getArg(0);
				$query = $e->getArg(1);
				
				$queries[] = [
					'table' => $tableName,
					'query' => $query
				];
			
			} );

			echo "\n\nshould return all: \n";

			$db
				->primary          // Database source name is accessed via the getter
				->test             // Table name is accessed via the getter
				->select()
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould return all excepting first: \n";

			$db
				->primary
				->test
				->select()
				->skip(1)
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould return first ( manually specifying fields ): \n";

			$db
				->primary
				->test
				->select([ 'name' ])
				->limit(1)
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould return the second person: \n";

			$db
				->primary
				->test
				->select()
				->skip(1)
				->limit(1)
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould return names > 'clarissa': \n";

			$db
				->primary
				->test
				->select([
					'id' => FALSE
				])
				->where([
					'id' => [
						'$ne' => null,
						'$gt' => 0
					],
					'name' => [
						'$gt' => 'clarissa'
					]
				])
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould return id > 4:\n";

			$db
				->primary
				->test
				->select()
				->where([
					'id' => [
						'$ne' => null,
						'$gt' => 3
					]
				])
				->skip(1)
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould return only bill or jack: \n";

			$db
				->primary
				->test
				->select()
				->where([
					'id' => [
						'$ne' => null,
						'$gt' => 0
					],
					'name' => [
						'$in' => [ 'bill', 'jack' ]
					]
				])
				->skip(1)
				->limit(1)
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\nshould not return id: \n";

			$db
				->primary
				->test
				->select([
					'id' => false
				])
				->where([
					'id' => [
						'$ne' => null,
						'$gt' => 0
					],
					'name' => [
						'$in' => [ 'bill', 'jack' ]
					]
				])
				->skip(1)
				->limit(1)
				->run()
				->each( function( $row ) {
					echo json_encode( $row ), "\n";
				});

			echo "\n\n===\n\nexecuted queries: ", json_encode( $queries, JSON_PRETTY_PRINT );

			$this->assertEquals( 8 , count( $queries ) );

		}

	}
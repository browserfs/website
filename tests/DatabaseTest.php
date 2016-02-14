<?php

	
	class DatabaseTest extends PHPUnit_Framework_TestCase {

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

		public function testIfTablePrimaryKeysIsWorking() {
			
			$db = $this->config->getService('database');

			$injector = $db->getDIInjector();

			$this->assertEquals( false, empty( $injector ) );

			$keys = $db->primary->test->schema();

			$this->assertEquals( true, is_array( $keys ) );
			$this->assertEquals( true, count( $keys ) > 0 );

		}

		public function testSelect() {

			$db = $this->config->getService('database');

			$queries = [];

			try {
				$db->primary->connect();
			} catch ( \Exception $e ) {
				echo "\n\nDATABASE SERVICE SKIPPED: The testing environment does not provide required database support\n\n" . $e->getMessage();
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

			// echo "\n\nshould return all: \n";

			$db
				->primary          // Database source name is accessed via the getter
				->test             // Table name is accessed via the getter
				->select()
				->run()
				->each( function( $row ) {
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould return all excepting first: \n";

			$db
				->primary
				->test
				->select()
				->skip(1)
				->run()
				->each( function( $row ) {
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould return first ( manually specifying fields ): \n";

			$db
				->primary
				->test
				->select([ 'name' ])
				->limit(1)
				->run()
				->each( function( $row ) {
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould return the second person: \n";

			$db
				->primary
				->test
				->select()
				->skip(1)
				->limit(1)
				->run()
				->each( function( $row ) {
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould return names > 'clarissa': \n";

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
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould return id > 4:\n";

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
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould return only bill or jack: \n";

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
					//echo json_encode( $row ), "\n";
				});

			//echo "\n\nshould not return id: \n";

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
					//echo json_encode( $row ), "\n";

				});

			//echo "\n\n===\n\nexecuted queries: ", json_encode( $queries, JSON_PRETTY_PRINT );

			$this->assertEquals( 8 , count( $queries ) );

			$db->primary->off( 'query' );

		}

		public function testInsert() {

			$db = $this->config->getService('database');

			$queries = [];

			try {
				$db->primary->connect();
			} catch ( \Exception $e ) {
				echo "\n\nDATABASE SERVICE SKIPPED: The testing environment does not provide required database support\n\n" . $e->getMessage();
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

			/** BEGIN TEST **/

			$record = $db->primary->test->insert([])->run();

			//echo json_encode( $record, JSON_PRETTY_PRINT );

			$db->primary->off( 'query' );

			//print_r( $queries );

		}

		public function testDatabaseCacheDependency() {

			$this->assertEquals( true, $this->config->getService('database')->primary->getCache() instanceof \browserfs\website\Cache );

		}

	}
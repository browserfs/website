<?php

	namespace browserfs\website\Service;

	class Database extends \browserfs\website\Service {

		protected $databases = [];

		protected function __construct( $serviceConfig ) {

			parent::__construct( $serviceConfig );

			$this->init();

		}

		private function init() {

			try {

				$dbNames = $this->getConfigPropertyNames();

				foreach ( $dbNames as $dbName ) {

					$dbURI = $this->getConfigProperty( $dbName, null );

					if ( $dbURI === null ) {
						throw new \browserfs\Exception('Failed to load source: ' . $dbName );
					}

					$this->addDatabaseSource( $dbName, $dbURI );

				}

			} catch ( \browserfs\Exception $e ) {

				throw new \browserfs\Exception( 'Failed to initialize the Database service: ' . $e->getMessage(), 1, $e );

			}

		}

		protected function addDatabaseSource( $databaseName, $databaseURI ) {

			$info = @parse_url( $databaseURI );

			if ( $info === false || !is_array( $info ) || !isset( $info['scheme'] ) ) {
				throw new \browserfs\Exception('URL "' . $databaseURI . '" is not a valid database URI ( source = "' . $databaseName . '" )' );
			}

			$dbType = $info[ 'scheme' ];

			$db = \browserfs\website\Database::factory( $dbType, $info, $databaseName );

			$this->databases[ $databaseName ] = $db;

		}

		public function get( $databaseSourceName ) {

			if ( is_string( $databaseSourceName ) ) {

				if ( isset( $this->databases[ $databaseSourceName ] ) ) {

					return $this->databases[ $databaseSourceName ];
				
				} else {

					throw new \browserfs\Exception( $databaseSourceName === '' ? 'Invalid argument $databaseSourceName: Expected non-empty string' : 'Database source named "' . $databaseSourceName . '" was not found' );

				}

			} else {

				throw new \browserfs\Exception( 'Invalid argument $databaseSourceName: expected non-empty string!' );

			}

		}

		public function __get( $propertyName ) {

			return $this->get( $propertyName );

		}

	}
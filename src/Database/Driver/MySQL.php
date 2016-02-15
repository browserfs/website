<?php

	namespace browserfs\website\Database\Driver;

	class MySQL extends \browserfs\website\Database {

		protected $driver = null;

		// Singleton tables patter. This is to allow attaching events
		// on the tables.
		protected $tables = [];

		protected function __construct( $config, $dsn ) {

			// THIS SHOULD BE THE FIRST LINE OF CODE FOR A DATABASE DRIVER IMPLEMENTATION
			parent::__construct( $config, $dsn );


		}

		public function table( $tableName ) {

			if ( $this->isTableName( $tableName ) ) {

				$normalizedTableName = $this->normalizeTableName( $tableName );

				if ( isset( $this->tables[ $normalizedTableName ] ) ) {
					return $this->tables[ $normalizedTableName ];
				}

				return (
					$this->tables[ $normalizedTableName ] 
						= new \browserfs\website\Database\Driver\MySQL\Table( $this, $normalizedTableName ) 
				);

			} else {

				throw new \brwoserfs\Exception('Invalid argument $tableName: ' . json_encode( $tableName ) . ' is not a valid table name!'  );

			}

		}

		public function isEscapable( $data ) {
			return $data === null 
				|| is_int( $data ) 
				|| is_float( $data ) 
				|| is_bool( $data ) 
				|| is_string( $data );
		}

		public function escape( $data ) {
			
			if ( $data === null ) {
			
				return 'NULL';
			
			} else
			
			if ( is_string( $data ) ) {
	
				if ( !$this->isConnected() ) {

					return '"' . addslashes( $data ) . '"';
				
				} else {
				
					return $this->driver->quote( $data );
				
				}

			} else
			
			if ( is_int( $data ) || is_float( $data ) ) {
				return (string)$data;
			} else
			
			if ( is_bool( $data ) ) {
				return ( (int)$data ) . '';
			} else 

			{
				throw new \browserfs\Exception('Don\'t know how to escape data!' );
			}
		}

		public function getNativeDriver() {
			return $this->driver;
		}

		public function __get( $tableName ) {

			if ( self::isTableName( $tableName ) ) {
				return $this->table( $tableName );
			} else {
				throw new \browserfs\Exception( 'Invalid table name: ' . json_encode( $tableName ) );
			}

		}

		private static function isTableName( $tableName ) {
			
			if ( !is_string( $tableName ) || $tableName == '' ) {
				return false;
			}

			$segments = explode( '.', $tableName );

			if ( count( $segments ) > 2 ) {
				return false;
			}

			foreach ( $segments as $segment ) {
				if ( !preg_match( '/^[a-zA-Z_]([a-zA-Z_0-9]+)?$/', $segment ) ) {
					return false;
				}
			}

			return true;

		}

		public function isIdentifier( $identifierName, $maxSegments = -1 ) {

			if ( !is_string( $identifierName ) || $identifierName == '' ) {
				return false;
			}

			$maxSegments = (int)$maxSegments;

			$segments = explode( '.', $identifierName );

			$numSegments = count( $segments );

			if ( $numSegments > 3 ) {
				return false;
			}

			if ( ($maxSegments > -1) && ($numSegments > $maxSegments) ) {
				return false;
			}

			foreach ( $segments as $segment ) {
				if ( !preg_match( '/^[a-zA-Z_]([a-zA-Z_0-9]+)?$/', $segment ) ) {
					return false;
				}
			}

			return true;

		}

		public function escapeIdentifier( $identifierName ) {

			if ( !$this->isIdentifier( $identifierName ) ) {
				throw new \browserfs\Exception('Invalid identifier name: ' . json_encode( $identifierName ) );
			}

			return '`' . implode( '`.`', explode('.', $identifierName ) ) . '`';

		}

		public function isConnected() {

			return $this->driver !== null;
		
		}

		public function connect() {

			if ( $this->isConnected() ) {
				return true;
			}

			$result = null;

			try {

				if ( !class_exists( '\PDO' ) ) {
					throw new \browserfs\Exception( 'The MySQL driver requires the PDO extension!' );
				}

				$uri = 'mysql:';

				$args = [];

				$args[] = 'host=' . ( $this->host === null ? 'localhost' : $this->host );
				
				if ( $this->port !== null )
					$args[] = 'port=' . $this->port;

				$args[] = 'dbname=' . $this->database;
				$args[] = 'charset=utf8';

				$uri .= implode( ';', $args );

				$result = new \PDO( $uri, $this->user, $this->password );

				$result->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			} catch ( \Exception $e ) {

				throw new \browserfs\Exception( 'Failed to connect to data source "' . $this->dsn . '" using protocol "mysql": ' . $e->getMessage(), 1, $e );

			}

			$this->driver = $result;

		}

		private function normalizeTableName( $tableName ) {

			$tableNameParts = explode( '.', $tableName );

			if ( count( $tableNameParts ) == 1 ) {

				return strtolower( $this->database ) . '.' . $tableName;
			
			} else {
			
				if ( count( $tableNameParts ) == 2 ) {
					// table is in format [database name].[table name]
					// make database to lower case...

					$tableNameParts[0] = strtolower( $tableNameParts[0] );
					
					return $tableNameParts[0] . '.' . $tableNameParts[1];
				
				} else {
					throw new \browserfs\Exception('Un-normalizable table name: ' . strtlower( $tableName ) );
				}
			}

		}

		/**
		 * On MYSQL driver, there are some restrictions on the dump file format:
		 * - each command should be on a single line
		 * - a line can be escaped via the # comment command
		 * - empty lines are skipped.
		 * This method is implemented for unit testing, not for production usage!!!
		 */
		public function execDumpFile( $fileName ) {

			$fullFileName = @realPath( $fileName );

			if ( !$fullFileName ) {
				throw new \browserfs\Exception('File: ' . $fileName . ' does not exist!' );
			}

			$buffer = file_get_contents( $fullFileName );

			if ( empty( $buffer ) ) {
				throw new \browserfs\Exception('File: ' . $fileName . ' could not be read ( or empty )!' );
			}

			$commands = explode( "\n", $buffer );

			$lineNo = 0;

			$this->connect();

			try {

				foreach ( $commands as $command ) {

					$lineNo++;

					$command = trim( $command );

					if ( $command == '' ) {
						continue;
					}

					if ( !preg_match( '/^([\s]+)?#/', $command ) ) {
						$this->getNativeDriver()->query( $command );
					}

				}

			} catch ( \Exception $e ) {

				throw new \browserfs\Exception('Error running dump file: ' . $fileName . ' @line: ' . $lineNo, 1, $e );

			}

		}

	}
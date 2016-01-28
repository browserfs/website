<?php

	namespace browserfs\website\Database\Driver;

	class MySQL extends \browserfs\website\Database {

		protected function __construct( $config, $dsn ) {

			// THIS SHOULD BE THE FIRST LINE OF CODE FOR A DATABASE DRIVER IMPLEMENTATION
			parent::__construct( $config, $dsn );


		}

		public function table( $tableName ) {

			if ( is_string( $tableName ) && $tableName != '' ) {

				return new \browserfs\website\Database\Driver\MySQL\Table( $this, $tableName );

			} else {

				throw new \brwoserfs\Exception('Invalid argument $tableName: non-empty string expected!' );

			}

		}

		public function escape( $data ) {

			return $data;

		}

		public function getNativeDriver() {

			return null;

		}

		public function __get( $tableName ) {

			if ( is_string( $tableName ) && $tableName != '' ) {
				return $this->table( $tableName );
			}

		}

	}
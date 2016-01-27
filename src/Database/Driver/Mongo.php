<?php

	namespace browserfs\website\Database\Driver;

	class Mongo extends \browserfs\website\Database {

		protected function __construct( $config, $dsn ) {

			// THIS SHOULD BE THE FIRST LINE OF CODE FOR A DATABASE DRIVER IMPLEMENTATION
			parent::__construct( $config, $dsn );


		}

		public function table( $tableName ) {

			return null;

		}

		public function escape( $data ) {

			return $data;

		}

		public function getNativeDriver() {

			return null;

		}

	}
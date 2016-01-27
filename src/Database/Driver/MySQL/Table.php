<?php
	
	namespace browserfs\website\Database\Driver\MySQL;

	class Table extends \browserfs\website\Database\Table {

		public function __construct( \browserfs\website\Database\Driver\MySQL $db, $tableName ) {

			parent::__construct( $db, $tableName );
		
		}

		public function select( $fields ) {
			return null;
		}

		public function update( $fields ) {
			return null;
		}

		public function delete( $fields ) {
			return null;
		}

		public function insert( $fields ) {
			return null;
		}

	}
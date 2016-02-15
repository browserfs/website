<?php
	
	namespace browserfs\website\Database\Driver\MySQL\Select;

	class Where extends \browserfs\website\Database\Select\Where {

		public function __construct( 
			$filter, 
			\browserfs\website\Database\Driver\MySQL\Table  $table,
			\browserfs\website\Database\Driver\MySQL\Select $select 
		) {

			parent::__construct( $filter, $table, $select );

		}

		public function limit( $many ) {
			throw new \browserfs\Exception('Implementation limit in where()' );
		}

		public function skip( $many ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Skip(
				$many,
				$this->table,
				$this->select,
				$this
			);

		}

		public function run() {
			$result = new \browserfs\website\Database\Driver\MySQL\Select\Run(
				$this->table,
				$this->select,
				$this
			);

			return $result->exec();
		}

		public function toString() {

			$filter = $this->value();

			if ( $filter === null ) {
				return 'TRUE';
			}

			return \browserfs\website\Database\Driver\MySQL\SQL::encodeFilterExpression( $filter, $this->table->db() );

		}

	}
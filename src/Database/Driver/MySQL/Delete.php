<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class Delete extends \browserfs\website\Database\Delete {

		public function __construct(
			$filter,
			\browserfs\website\Database\Driver\MySQL\Table $table
		) {
			parent::__construct( $filter, $table );
		}

		public function skip( $many ) {

			return new \browserfs\website\Database\Driver\MySQL\Delete\Skip( 
				$many,
				$this->table,
				$this
			);

		}

		public function limit( $many ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Delete\Limit(
				$many,
				$this->table,
				$this
			);

		}

		/**
		 * Executes the delete statement
		 * @return int - the number of deleted rows
		 */
		public function run() {

			$result = new \browserfs\website\Database\Driver\MySQL\Delete\Run(
				$this->table,
				$this
			);

			return $result->exec();

		}

		public function toString() {

			$filter = $this->value();

			if ( $filter === null ) {
				return 'TRUE';
			}

			return 'WHERE ' . SQL::encodeFilterExpression( $filter, $this->table->db() );

		}



	}
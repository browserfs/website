<?php
	
	namespace browserfs\website\Database\Driver\MySQL\Update;

	class Where extends \browserfs\website\Database\Update\Where {

		public function __construct(
			$filter,
			\browserfs\website\Database\Table $table,
			\browserfs\website\Database\Update $update
		) {
			parent::__construct( $filter, $table, $update );
		}

		public function limit( $count ) {
			
			return new Limit(
				$count,
				$this->table,
				$this->getUpdate(),
				$this
			);

		}

		public function run() {
			
			$statement = new Run(
				$this->table,
				$this->getUpdate(),
				$this,
				null
			);

			return $statement->exec();

		}

		public function toString() {

			$value = $this->value();

			if ( $value === null ) {
				return 'TRUE';
			}

			return \browserfs\website\Database\Driver\MySQL\SQL::encodeFilterExpression( $value, $this->table->db() );

		}

	}
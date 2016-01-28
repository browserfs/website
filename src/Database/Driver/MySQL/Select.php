<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class Select extends \browserfs\website\Database\Select {

		public function __construct( 

			$fields, 

			\browserfs\website\Database\Driver\MySQL\Table $table 

		) {

			parent::__construct( $fields, $table );
		
			// TODO! If !$this->isAllFields() Then Check if $this->fields(): string[] is a valid mysql field list

		}

		public function where( $filterCondition ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Where(
				$filterCondition,
				$this->table,
				$this
			);

		}

		public function limit( $many ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Limit(
				$many,
				$this->table,
				$this,
				null,
				null
			);

		}

		public function skip( $many ) {

			return new \browserfs\website\Database\Driver\MySQL\Select\Skip(
				$many,
				$this->table,
				$this,
				null
			);

		}

		public function run() {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Run(
				$this->table,
				$this,
				null,
				null,
				null
			);

		}

		public function toString() {

			if ( $this->isAllFields() ) {

				return '*';
			
			} else
			if ( $this->isSomeFields() ) {
			
				return '`' . implode( '`, `', $this->value() ) . '`';
			
			} else
			if ( $this->isExceptFields() ) {
			
				return '*'; // Filtering will be made upon query execution
			
			} else {
			
				throw new \browserfs\Exception('Invalid query fields list (bug)?');
			
			}

		}

	}
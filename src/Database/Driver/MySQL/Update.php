<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class Update extends \browserfs\website\Database\Update {

		public function __construct(
			$fieldsToUpdate,
			\browserfs\website\Database\Driver\MySQL\Table $table
		) {

			parent::__construct( $fieldsToUpdate, $table );

			$this->checkFields();		

		}

		/**
		 * Checks if the fields provided during the constructor phase
		 * are valid mysql fields ( as name, and also as value )
		 */
		protected function checkFields() {

			// Check $this->fields

			$db = $this->table->db();

			foreach ( $this->fields as $fieldName => $value ) {

				if ( !$db->isIdentifier( $fieldName, 1 ) ) {
					throw new \browserfs\Exception('Invalid argument $fieldsToUpdate: Encountered an invalid field name ' . $fieldName );
				}

				if ( !$db->isEscapable( $value ) ) {
					throw new \browserfs\Exception('Invalid argument $fieldsToUpdate: Value provided for field named ' . json_encode( $fieldName ) . ' is invalid (got ' . json_encode( $value ) . ')' );
				}

			}

		}

		public function where( $filter ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Update\Where(
				$filter,
				$this->table,
				$this
			);

		}

		public function limit( $count ) {
			return new Update\Limit( 
				$count,
				$this->table,
				$this
			);
		}

		public function run() {
			
			$statement = new \browserfs\website\Database\Driver\MySQL\Update\Run( 
				$this->table,
				$this,
				$this->getWhere()
			);

			return $statement->exec();

		}

		public function toString() {

			$value = $this->value();

			$result = [];

			foreach ( $value as $field => $value ) {
				$result[] = $this->table->db()->escapeIdentifier( $field ) . ' = ' . $this->table->db()->escape( $value );
			}

			return implode( $result, ', ' );

		}

	}
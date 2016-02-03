<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class Insert extends \browserfs\website\Database\Insert {

		public function __construct( 
			$fieldsToInsert = null, 
			\browserfs\website\Database\Driver\MySQL\Table $table 
		) {

			parent::__construct( $fieldsToInsert, $table );
		
			// Check if all the keys of the $this->fields are valid
			// mysql column names.

			$this->checkFields();

		}

		/**
		 * Check the correctness of the names / values of the fields.
		 */
		private function checkFields()
		{

			if ( null === $this->fields ) {
				return;
			}

			if ( !is_array( $this->fields ) )
			{
				throw new \browserfs\Exception('Invalid argument: $fieldsToInsert: Expected array | null' );
			}

			if ( 0 === count( $this->fields ) ) {
				$this->fields = null;
				return;
			}

			$db = $this->getTable()->db();

			foreach ( $this->fields as $fieldName => $fieldValue ) {

				if ( !$db->isIdentifier( $fieldName, 1 ) ) {
					throw new \browserfs\Exception('Invalid key in insert statement: ' . json_encode( $fieldName ) );
				}

				if ( !$db->isEscapable( $fieldValue ) ) {
					throw new \browserfs\Exception('Invalid key value for field "' . $fieldName . '": ' . json_encode( $fieldValue ) );
				}

			}

		}

		public function run()
		{

			$stmt = new \browserfs\website\Database\Driver\MySQL\Insert\Run( $this );

			return $stmt->exec();

		}

	}
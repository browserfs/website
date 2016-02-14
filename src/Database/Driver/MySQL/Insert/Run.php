<?php

	namespace browserfs\website\Database\Driver\MySQL\Insert;

	class Run extends \browserfs\website\Database\Insert\Run {

		public function __construct( 
			$table, 
			$insertStatement 
		) {

			parent::__construct( $table, $insertStatement );
		
		}

		public function exec() {

			$fieldNames = $this->insert()->value();

			if ( $fieldNames === null ) {
				$fieldNames = [];
			}

			$requiredFieldNames = $this->table()->getMandatoryPrimaryKeyNames();
			$primaryKeyName = $this->table()->getAutoIncrementKeyName();

			if ( $primaryKeyName !== null && array_key_exists($primaryKeyName, $fieldNames ) ) {
				if ( !is_int( $fieldNames[ $primaryKeyName ] ) || ( $fieldNames[ $primaryKeyName ] < 1 ) ) {
					throw new \browserfs\Exception( "The key " . $primaryKeyName . " is of type auto_increment, and must be INT > 0 ( but we've got " . json_encode( $fieldNames[ $primaryKeyName ] ) . ")" );
				}
			}

			foreach ( $requiredFieldNames as $fieldName ) {
				if ( !array_key_exists($fieldName, $fieldNames ) ) {
					throw new \browserfs\Exception('Cannot execute insert: The primary key field named "' . $fieldName . '" is not present as key in the insert statement!' );
				}
			}

			$sql = [ "INSERT INTO " . $this->table()->db()->escapeIdentifier( $this->table()->name() ) ];

			if ( count( $fieldNames ) ) {

				$keyClause = [];
				$valueCaluse = [];

				foreach ( $fieldNames as $fieldName => $fieldValue ) {

					$keyClause[] = $this->table()->db()->escapeIdentifier( $fieldName );
					$valueClause[] = $this->table()->db()->escape( $fieldValue );

				}

				$sql[] = '(' . implode( ', ', $keyClause ) . ') VALUES (' . implode( ', ', $valueClause ) . ');';

			} else {
				$sql[] = '() VALUES ();';
			}

			$sql = implode( ' ', $sql );

			$this->table()->fire('query', $sql );

			$pdo = $this->table()->db()->getNativeDriver();

			try {
				
				$pdo->query( $sql );

			} catch ( \Exception $e ) {

				throw new \browserfs\Exception('Insert failed!', 1, $e );
			}

			// if the primaryKeyName is not present into the $fieldNames, fetch it from server.
			if ( ( $primaryKeyName !== null ) && !array_key_exists( $primaryKeyName, $fieldNames ) ) {

				$fieldNames[ $primaryKeyName ] = (int)$pdo->lastInsertId();

			}

			/*
			// If the number of the columns from the schema differs
			// from the number of the columns from the insert, we
			// re-fetch data from server, in order to retrieve all it's
			// values.

			if ( count( $fieldNames ) != count( $schema = $this->table()->schema() ) ) {

				foreach ( $schema as $schemaProp ) {
					if ( !array_key_exists($schemaProp['name'], $fieldNames ) ) {
						$fieldNames[ $schemaProp['name'] ] = $schemaProp[ 'default' ];
					}
				}

			}
			*/

			return $fieldNames;
		}

	}
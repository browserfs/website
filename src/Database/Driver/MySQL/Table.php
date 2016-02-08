<?php
	
	namespace browserfs\website\Database\Driver\MySQL;

	class Table extends \browserfs\website\Database\Table {

		public function __construct( \browserfs\website\Database\Driver\MySQL $db, $tableName )
		{

			parent::__construct( $db, $tableName );
		
		}

		public function select( $fields = null )
		{
			return new Select( $fields, $this );
		}

		public function update( $fields )
		{
			return null;
		}

		public function delete( $fields )
		{
			return null;
		}

		public function insert( $fields )
		{
			return new Insert( $fields, $this );
		}

		public function getPrimaryKeyFields()
		{

			// TODO!: RETRIEVE THE PRIMARY KEYS FROM CACHE

			$this->db()->connect();

			$result = $this->db()->getNativeDriver()->query( 
				$sql = "SHOW columns FROM " 
					. $this->db()->escapeIdentifier( $this->name ) 
					. " WHERE `Key` = 'PRI'"
			);
			
			$fields = [];

			foreach ( $result as $row ) {
				$fields[] = [
					'name' => $row['Field'],
					'autoIncrement' => strtolower( $row['Extra'] ) == 'auto_increment',
					'default' => $row['Default']
				];
			}

			return $fields;
		}

	}
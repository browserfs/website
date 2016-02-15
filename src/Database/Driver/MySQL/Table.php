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

		public function delete( $filter )
		{
			return new Delete( $filter, $this );
		}

		public function insert( $fields )
		{
			return new Insert( $fields, $this );
		}

		/**
		 * Returns the table schema.
		 * @return [ index: string ]: [ "name": string, "type": string, "autoIncrement": boolean, "primary": boolean, "allowNull": boolean ]
		 */
		public function schema()
		{

			$cache = $this->db()->getCache();

			$cachedSchema = $cache->get('_table_' . $this->name() );

			if ( $cachedSchema !== null && is_string( $cachedSchema ) ) {
				return json_decode( $cachedSchema, TRUE );
			}

			$this->db()->connect();

			$result = $this->db()->getNativeDriver()->query( 
				$sql = "SHOW columns FROM " 
					. $this->db()->escapeIdentifier( $this->name )
			);
			
			$fields = [];

			foreach ( $result as $row ) {

				$fields[] = [
					'name' => $row['Field'],
					'autoIncrement' => strtolower( $row['Extra'] ) == 'auto_increment',
					'default' => $row['Default'] === 'NULL' ? null : $row['Default'],
					'primary' => strtolower( $row['Key'] ) == 'pri'
				];
			}

			$cache->set( '_table_' . $this->name, json_encode( $fields ), -1 );

			return $fields;
		}

	}
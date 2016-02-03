<?php
	
	namespace browserfs\website\Database;

	abstract class Insert implements Insert\IInsert {

		/**
		 * @var array | null
		 */
		protected $fields = null;

		/**
		 * @var \browserfs\website\Database\Table
		 */
		protected $table  = null;

		/**
		 * Constructor. Creates a new insert statement on a table.
		 */
		public function __construct( 
			$fieldsToInsert = null, 
			\browserfs\website\Database\Table $table 
		) {

			if ( null !== $fieldsToInsert ) {

				if ( !is_array( $fieldsToInsert ) ) {
					throw new \browserfs\Exception('Invalid argument $fieldsToInsert: Expected array | null' );
				}

				$this->fields = $fieldsToInsert;
			}

			if ( null === $table ) {
				
				throw new \browserfs\Exception('Invalid argument $table: Expected \\browserfs\\website\\Database\\Table but got null!' );
			
			} else {

				if ( ! ($table instanceof Table) ) {
					throw new \browserfs\Exception('Invalid argument $table: Expected \\browserfs\\website\\Database\\Table!' );
				}
				
				$this->table = $table;
			}

		}

		/**
		 * Returns the originating table, in which the insert
		 * operation is made.
		 * @return \browserfs\website\Database\Table
		 */
		public function getTable()
		{
			return $this->table;
		}

		/**
		 * Returns the originating insert statement.
		 * @return \browserfs\website\Database\Insert
		 */
		public function getInsert() 
		{
			return $this;
		}

		/**
		 * Returns the object which needs to be inserted
		 * @return array | null
		 */
		public function value()
		{
			return $this->fields;
		}

		/**
		 * Returns a collection with the inserted object.
		 * @return \browserfs\base\Collection
		 */
		abstract public function run();

	}
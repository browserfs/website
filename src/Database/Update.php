<?php

	namespace browserfs\website\Database;

	abstract class Update implements Update\IUpdate {

		/**
		 * @var array() | null
		 */
		protected $fields = null;

		/**
		 * @var \browserfs\website\Database\Table
		 */
		protected $table = null;

		/**
		 * Constructor. Creates a new UPDATE statement on the table.
		 */
		public function __construct(
			$fieldsToUpdate,
			\browserfs\website\Database\Table $table
		) {

			if ( !( is_array( $fieldsToUpdate) ) ) {
				throw new \browserfs\Exception('Invalid argument: $fieldsToUpdate: Expected array!');
			}

			if ( count( $fieldsToUpdate ) == 0 ) {
				throw new \browserfs\Exception('Invalid argument: $fieldsToUpdate: Expected at least one field in the $fieldsToUpdate argument!');
			}

			$this->fields = $fieldsToUpdate;

			if ( null === $table ) {
				
				throw new \browserfs\Exception('Invalid argument: $table: Expected a instance of \browserfs\website\Database\Table not null');
			
			} else {

				if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
					throw new \browserfs\Exception('Invalid argument: $table: Expected a instance of \browserfs\website\Database\Table' );
				}

				$this->table = $table;

			}

		}

		/**
		 * Returns a WHERE statement for this UPDATE operation.
		 * @return \browserfs\website\Database\Update\Where
		 */
		abstract public function where( $filter );

		/**
		 * Returns a LIMIT statement for this UPDATE operation
		 * @return \browserfs\website\Database\Update\LIMIT
		 */
		abstract public function limit( $count );

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
		 * Return this UPDATE statement
		 * @return \browserfs\website\Database\Update
		 */
		public function getUpdate() {
			return $this;
		}

		/**
		 * Return the WHERE statement of this UPDATE operation
		 * @return \browserfs\website\Database\Update\Where
		 */
		public function getWhere() {
			return null;
		}

		/**
		 * Returns this LIMIT clause.
		 * @return \browserfs\website\Database\Update\Limit
		 */
		public function getLimit() {
			return null;
		}

		/**
		 * Returns the fields to be updated of this UPDATE statement
		 * @return array
		 */
		public function value() {
			return $this->fields;
		}

		/**
		 * Executes the update operation, and returns the number of affected
		 * rows, or 0 if no rows were affected.
		 * @return int >= 0
		 */
		abstract public function run();

	}
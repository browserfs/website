<?php

	namespace browserfs\website\Database;

	abstract class Delete implements Delete\IDelete {

		/**
		 * @var array() // [ index: string ] => any
		 */
		protected $filter = null;

		/**
		 * The table on which the delete statement is executed
		 * @var \browserfs\website\Database\Table
		 */
		protected $table = null;

		/**
		 * Constructor. Creates a new DELETE statement
		 */
		public function __construct( 
			$filter,  
			\browserfs\website\Database\Table $table
		) {

			if ( null !== $filter ) {
				$this->filter = new FilterList( $filter );
			}

			if ( ! ( $table instanceof Table ) ) {
				throw new \browserfs\Exception( 'Invalid argument: $table must be a instance of \browserfs\website\Database\Table' );
			}

			$this->table = $table;

		}

		/**
		 * Returns the owner table on which this DELETE statement will be executed
		 * @return \browserfs\website\Database\Table
		 */
		public function getTable() {
			return $this->table;
		}

		/**
		 * Return this delete statement.
		 */
		public function getWhere() {
			return $this;
		}

		/**
		 * Returns the limit clause of this DELETE statement
		 * @return \browserfs\website\Database\Delete\Limit | null
		 */
		public function getLimit() {
			return null;
		}

		/**
		 * Return the value of the filter list for this DELETE statement, in format [ index: string ]: any
		 * @return array() | null
		 */
		public function value() {
			return $this->filter === null
				? null
				: $this->filter->value();
		}

		/**
		 * Executes the DELETE statement, and returns the number of affected rows
		 * @return int
		 */
		abstract public function run();


	}
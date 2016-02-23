<?php
	
	namespace browserfs\website\Database\Update;

	/**
	 * This class is implementing the WHERE statement on a Database UPDATE operation.
	 * All \browserfs\website\Database\Driver\<DriverName>\Update\Where statements, should extend this class.
	 */
	abstract class Where implements IUpdate {

		/**
		 * The table on which the UPDATE operation runs
		 * @var \browserfs\website\Database\Table
		 */
		protected $table = null;

		/**
		 * The UPDATE statement
		 * @var \browserfs\website\Database\Update
		 */
		protected $update = null;

		/**
		 * The filter that is applied by this WHERE statement of an UPDATE operation.
		 * @var \browserfs\website\Database\FilterList | null
		 */
		protected $filter = null;

		/**
		 * Constructor. Binds a WHERE clause on current UPDATE statement.
		 */
		public function __construct( 
			
			$filter,
			\browserfs\website\Database\Table $table,
			\browserfs\website\Database\Update $update
		
		) {

			if ( null !== $filter ) {

				if ( !is_array( $filter ) ) {
					throw new \browserfs\Exception('Invalid argument $filter: Expected array!' );
				}

				$this->filter = new \browserfs\website\Database\FilterList( $filter );

			}

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected a instanceof \browserfs\website\Database\Table');
			}

			$this->table = $table;

			if ( !( $update instanceof \browserfs\website\Database\Update ) ) {
				throw new \browserfs\Exception('Invalid argument $update: Expected a instanceof \browserfs\website\Database\Update');
			}

			$this->update = $update;

		}

		/**
		 * Returns the originating table where this WHERE clause of the current UPDATE operation is executed
		 * @return \browserfs\website\Database\Table
		 */
		public function getTable() {
			return $this->table;
		}

		/**
		 * Returns the originating UPDATE operation
		 * @return \browserfs\website\Database\Update
		 */
		public function getUpdate() {
			return $this->update;
		}

		/**
		 * Returns the WHERE statement of this UPDATE operation
		 * @return \browserfs\website\Database\Update\Where
		 */
		public function getWhere() {
			return $this;
		}

		/**
		 * Returns a LIMIT statement for this UPDATE statement
		 * @return \browserfs\website\Database\Update\Limit
		 */
		abstract public function limit( $count );

		/**
		 * Returns the current LIMIT clause ( should be NULL for this class ).
		 * @return \browserfs\website\Database\Update\Limit
		 */
		public function getLimit() {
			return null;
		}

		/**
		 * Returns the value of the WHERE filter of the UPDATE statement
		 * @return array | null
		 */
		public function value() {
			return $this->filter === null
				? null
				: $this->filter->value();
		}

	}
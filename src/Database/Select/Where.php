<?php
	
	namespace browserfs\website\Database\Select;

	abstract class Where implements ISelect {

		protected $select = null;
		protected $table  = null;
		protected $filter = null;

		public function __construct( 
		
			$filter, 
		
			\browserfs\website\Database\Table $table, 
			\browserfs\website\Database\Select $select 
		) {

			if ( $filter !== null ) {
				$this->filter = new \browserfs\website\Database\FilterList( $filter );
			}

			if ( $select !== null ) {

				if ( !( $select instanceof \browserfs\website\Database\Select ) ) {
					throw new \browserfs\Exception( 'Invalid argument $select: Expected instance of \\browserfs\\website\\Database\\Select' );
				}

				$this->select = $select;

			}

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected instance of \\browserfs\\website\\Database\\Table' );
			}

			$this->table = $table;

		}

		public function getTable() {
			return $this->table();
		}

		public function getSelect() {
			return $this->select;
		}

		public function getWhere() {
			return $this;
		}

		public function getSkip() {
			return null;
		}

		public function getLimit() {
			return null;
		}

		public function value() {
			return $this->filter === null
				? null
				: $this->filter->value();
		}

		abstract public function limit( $many );

		abstract public function skip( $many );

		abstract public function run();

	}
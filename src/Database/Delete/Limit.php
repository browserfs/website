<?php
	
	namespace browserfs\website\Database\Delete;

	class Limit implements \browserfs\website\Database\IDelete {

		protected $table = null;
		protected $where = null;
		protected $skip  = null;

		protected $value = null;

		public function __construct( 
			$count,
			\browserfs\website\Database\Table $table,
			\browserfs\website\Database\Delete $where,
			\browserfs\website\Database\Skip $skip = null
		) {

			if ( !is_int( $count) || $count < 1 ) {
				throw new \browserfs\Exception('Invalid argument: $count: Expected int > 0' );
			}

			$this->value = $count;

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument: $table: Expected a \browserfs\website\Database\Table')
			}

			$this->table = $table;

			if ( !( $where instanceof \browserfs\website\Database\Delete ) ) {
				throw new \browserfs\Exception('Invalid argument: $where: Expected a \browserfs\website\Database\Delete')
			}

			$this->where = $where;

			if ( null !== $skip ) {
				if ( !( $skip instanceof \browserfs\website\Database\Delete\Skip ) ) {
					throw new \browserfs\Exception('Invalid argument: $skip: Expected a \browserfs\website\Database\Delete\Skip');
				}
			}

			$this->skip = $skip;

		}

		public function getTable() {
			return $this->table;
		}

		public function getWhere() {
			return $this->where;
		}

		public function getSkip() {
			return $this->skip;
		}

		public function getLimit() {
			return $this;
		}

		public function value() {
			return $this->value;
		}

		abstract public function run();

	}
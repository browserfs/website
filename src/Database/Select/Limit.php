<?php

	namespace browserfs\website\Database\Select;

	abstract class Limit implements \browserfs\website\Database\Select\ISelect {

		protected $table  = null;
		protected $select = null;
		protected $where  = null;
		protected $skip   = null;

		protected $count = null;

		public function __construct( 
			$count, 
			\browserfs\website\Database\Table        $table, 
			\browserfs\website\Database\Select       $select = null, 
			\browserfs\website\Database\Select\Where $where  = null,
			\browserfs\website\Database\Select\Skip  $skip   = null
		) {

			if ( !is_int( $count ) ) {

				throw new \browserfs\Exception('Invalid argument $count: expected int >= 0' );
			
			} else {

				if ( $count < 0 ) {
					throw new \browserfs\Exception('Invalid argument $count: expected int >= 0' );
				}

				$this->count = $count;

			}

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected instance of \\browserfs\\website\\Database\\Table' );
			}

			$this->table = $table;

			if ( null !== $select ) {

				if ( !( $select instanceof \browserfs\website\Database\Select ) ) {
					throw new \browserfs\Exception('Invalid argument $select: Expected instance of \\browserfs\\website\\Database\\Select' );
				}

				$this->select = $select;

			}

			if ( null !== $where ) {

				if ( !( $where instanceof \browserfs\website\Database\Select\Where ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Select\\Where' );
				}

				$this->where = $where;

			}

			if ( null !== $skip ) {

				if ( !( $skip instanceof \browserfs\website\Database\Select\Skip ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Select\\Skip' );
				}

				$this->skip = $skip;
				
			}

		}

		public function getTable() {
			return $this->table;
		}

		public function getSelect() {
			return $this->select;
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
			return $this->count;
		}

		abstract public function run();

	}
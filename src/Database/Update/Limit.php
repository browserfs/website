<?php

	namespace browserfs\website\Database\Update;

	abstract class Limit implements IUpdate {

		protected $table  = null;
		protected $update = null;
		protected $where  = null;

		protected $count  = null;

		/**
		 * Constructor. Creates a LIMIT statement for a UPDATE command.
		 * @param int                                     $count  - The number of records to limit the UPDATE command
		 * @param \browserfs\website\Database\Table       $table  - The table on which the update operation is made
		 * @param \browserfs\website\Database\Update      $update - The UPDATE command
		 * @param \browserfs\website\Database\Table\Where $where  - The WHERE clause of the UPDATE command ( if any )
		 */
		public function __construct( 
			$count,
			\browserfs\website\Database\Table        $table,
			\browserfs\website\Database\Update       $update,
			\browserfs\website\Database\Update\Where $where  = null
		) {

			if ( !is_int( $count ) ) {

				throw new \browserfs\Exception('Invalid argument $count: Integer greater than 0 expected!' );
			
			} else {
			
				if ( $count <= 0 ) {
					
					throw new \browserfs\Exception('Invalid argument $count: Integer greater than 0 expected!' );
				
				}

				$this->count = $count;
			}

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
			
				throw new \browserfs\Exception('Invalid argument $table: Expected instanceof \browserfs\website\Database\Table' );
			
			} else {

				$this->table = $table;
			
			}

			if ( !( $update instanceof \browserfs\website\Database\Update ) ) {

				throw new \browserfs\Exception('Invalid argument: $update: Expected instanceof \browserfs\website\Database\Update' );

			} else {

				$this->update = $update;
			
			}

			if ( null !== $where ) {

				if ( !( $where instanceof \browserfs\website\Database\Update\Where ) ) {
					throw new \browserfs\Exception('Invalid argument: $where: Expected instanceof \browserfs\website\Database\Update\Where' );
				}

				$this->where = $where;

			}

		}

		public function value() {
			return $this->count;
		}

		public function toString() {
			return (string)$this->count;
		}

		public function getTable() {
			return $this->table;
		}

		public function getUpdate() {
			return $this->update;
		}

		public function getWhere() {
			return $this->where;
		}

		public function getLimit() {
			return $this;
		}

		public abstract function run();

	}
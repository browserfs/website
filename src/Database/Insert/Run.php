<?php

	namespace browserfs\website\Database\Insert;

	abstract class Run {

		protected $insert = null;
		protected $table  = null;

		public function __construct( 

			\browserfs\website\Database\Table  $table,
			\browserfs\website\Database\Insert $insertStatement 

		) {

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected instanceof \\browserfs\\website\\Database\\Table !' );
			}

			if ( !( $insertStatement instanceof \browserfs\website\Database\Insert ) ) {
				throw new \browserfs\Exception('Invalid argument: $insertStament: Expected instanceof \\browserfs\\website\\Database\\Insert !');
			}

			$this->table  = $table;
			$this->insert = $insertStatement;

		}

		/**
		 * Returns the original insert statement.
		 * @return \browserfs\website\Database\Insert
		 */
		public function insert() {
			return $this->insert;
		}

		/**
		 * Return the table of the insert operation
		 */
		public function table() {
			return $this->table;
		}

		abstract public function exec();

	}
<?php

	namespace browserfs\website\Database\Driver\MySQL\Select;

	class Skip extends \browserfs\website\Database\Select\Skip {

		public function __construct(
			$count, 
			\browserfs\website\Database\Driver\MySQL\Table        $table, 
			\browserfs\website\Database\Driver\MySQL\Select       $select = null, 
			\browserfs\website\Database\Driver\MySQL\Select\Where $where  = null
		) {

			parent::__construct( $count, $table, $select, $where );

		}

		public function limit( $many ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Limit( 
				$many, 
				$this->table, 
				$this->select, 
				$this->where, 
				$this
			);

		}

		public function run() {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Run(
				$this->table,
				$this->select,
				$this->where,
				$this
			);

		}

	}
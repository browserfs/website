<?php

	namespace browserfs\website\Database\Driver\MySQL\Select;

	class Limit extends \browserfs\website\Database\Select\Limit {

		public function __construct(
			$count, 
			\browserfs\website\Database\Driver\MySQL\Table        $table, 
			\browserfs\website\Database\Driver\MySQL\Select       $select = null, 
			\browserfs\website\Database\Driver\MySQL\Select\Where $where  = null,
			\browserfs\website\Database\Driver\MySQL\Select\Skip  $skip   = null
		) {

			parent::__construct( $count, $table, $select, $where, $skip );

		}

		public function run() {
			
			$result = new \browserfs\website\Database\Driver\MySQL\Select\Run(
				$this->table,
				$this->select,
				$this->where,
				$this->skip,
				$this
			);

			return $result->exec();

		}

	}
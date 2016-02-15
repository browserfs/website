<?php

	namespace browserfs\website\Database\Driver\MySQL\Delete;

	class Limit extends \browserfs\website\Database\Delete\Limit {

		public function __construct(
			$count, 
			\browserfs\website\Database\Driver\MySQL\Table         $table, 
			\browserfs\website\Database\Driver\MySQL\Delete        $where  = null
		) {

			parent::__construct( $count, $table, $where);

		}

		public function run() {
			
			$result = new \browserfs\website\Database\Driver\MySQL\Delete\Run(
				$this->table,
				$this->where,
				$this
			);

			return $result->exec();

		}

	}
<?php

	namespace browserfs\website\Database\Driver\MySQL\Update;

	class Limit extends \browserfs\website\Database\Update\Limit {

		public function __construct(
			$count, 

			\browserfs\website\Database\Driver\MySQL\Table        $table, 
			\browserfs\website\Database\Driver\MySQL\Update       $update = null, 
			\browserfs\website\Database\Driver\MySQL\Update\Where $where  = null
		) {

			parent::__construct( $count, $table, $update, $where );

		}

		public function run() {
			
			$result = new \browserfs\website\Database\Driver\MySQL\Update\Run(
				$this->table,
				$this->update,
				$this->where,
				$this
			);

			return $result->exec();

		}

	}
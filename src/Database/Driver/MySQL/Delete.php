<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class Delete extends \browserfs\website\Database\Delete {

		public function __construct(
			$filter,
			\browserfs\website\Database\Driver\MySQL\Table $table
		) {
			parent::__construct( $filter, $table );
		}

		public function skip( $many ) {
			
		}

		public function limit( $many ) {

		}

	}
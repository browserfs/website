<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class Select extends \browserfs\website\Database\Select {

		public function __construct( $fields ) {

			parent::__construct( $fields );
		
			// TODO! If !$this->isAllFields() Then Check if $this->fields(): string[] is a valid mysql field list

		}

		public function where( $filterCondition ) {
			throw new \browserfs\Exception('Implementation has not reached so far' );
		}

		public function run() {
			throw new \browserfs\Exception('Implementation has not reached so far' );
		}

	}
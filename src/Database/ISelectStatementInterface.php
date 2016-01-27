<?php
	
	namespace browserfs\website\Database;

	interface ISelectStatementInterface {

		public function table();

		public function db();

		public function fields();

		public function isAllFields();
		public function isSomeFields();
		public function isExceptFields();

		public function where( $filterCondition );

		public function run();

	}
<?php
	
	namespace browserfs\website\Database;

	interface ISelectStatementInterface {

		public function table();

		public function db();

		public function fields();

		public function where();

		public function run();

	}
<?php
	
	namespace browserfs\website\Database\Delete;

	interface IDelete {
		
		public function getTable();

		public function getWhere();

		public function getLimit();

		public function value();

		public function run();

	}
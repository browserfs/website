<?php

	namespace browserfs\website\Database\Select;

	interface ISelect {

		public function getTable();

		public function getSelect();

		public function getWhere();

		public function getSkip();

		public function getLimit();

		public function value();

		public function run();

	}
<?php

	namespace browserfs\website\Database;

	interface ITableInterface {

		public function select( $what );

		public function update( $fields );

		public function delete( $where );

		public function insert( $fields );

		public function name();

		public function db();

	}
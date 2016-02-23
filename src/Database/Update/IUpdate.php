<?php

	namespace browserfs\website\Database\Update;

	/**
	 * Interface for defining an UPDATE database service statement.
	 * All drivers that are implementing the UPDATE command, should implement this interface.
	 */
	interface IUpdate {

		/**
		 * Returns the table on which the UPDATE statement operation runs
		 * @return \browserfs\website\Database\Table
		 */
		public function getTable();

		/**
		 * Returns the UPDATE clause
		 * @return \browserfs\website\Database\Update
		 */
		public function getUpdate();

		/**
		 * Returns the WHERE clause of the UPDATE statement
		 * @return \browserfs\website\Database\Update\Where
		 */
		public function getWhere();

		/**
		 * Returns the LIMIT clause of the UPDATE statement
		 * @return \browserfs\website\Database\Update\Limit
		 */
		public function getLimit();

		/**
		 * Returns the current statement value ( mixed )
		 * @return mixed
		 */
		public function value();

		/**
		 * Executes the UPDATE statement, and returns the number of affected
		 * rows by the statement.
		 * @return int
		 */
		public function run();

	}
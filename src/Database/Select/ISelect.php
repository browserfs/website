<?php

	namespace browserfs\website\Database\Select;

	/**
	 * This interface is implementing the SELECT \browserfs\website\Database statement driver implementation.
	 * All drivers that are implementing the \browserfs\website\Database\ITableInterface should return
	 * an implementation of this interface ( extend the \browserfs\website\Database\Select class ).
	 *
	 * In the terminology of this interface, there are method names inspired from SQL language, but
	 * the programmer should think in a abstract way ( e.g. on mongodb, the equivalent of the sql SELECT
	 * statement is called "find", and is a method ). This SELECT interface should make an abstraction of the
	 * naming of a specific database vendor implementation.
	 */
	interface ISelect {

		/**
		 * Returns the table on which the select operation is made.
		 * @return \browserfs\website\Database\Table
		 */
		public function getTable();

		/**
		 * Returns the originating "SELECT" ( on sql terminology ) statement on this operation.
		 * We need this in order to obtain from this statement the fields
		 * list ( SELECT ... )
		 * @return \browserfs\website\Database\Select
		 */
		public function getSelect();

		/**
		 * Returns the WHERE ( on sql terminology ) clause of the current SELECT statement.
		 * We need this in order to obtain from this statement the fields
		 * which are filtering data ( WHERE ... )
		 * @return \browserfs\website\Database\Select\Where
		 */
		public function getWhere();

		/**
		 * Returns the statement which specifies the number of skip results ( LIMIT <max_resultset>, <skip_results> )
		 * on this select statement.
		 * @return \browserfs\website\Database\Select\Skip
		 */
		public function getSkip();

		/**
		 * Returns the number of <max_statement_select_results> from this SELECT statement.
		 * @return \browserfs\website\Database\Select\Limit
		 */
		public function getLimit();

		/**
		 * Returns the value of this statement that is implementing this interface.
		 * @return any
		 */
		public function value();

		/**
		 * Returns a traversible collections, containing the resulting rows
		 * for this select statement.
		 * @return \browserfs\base\Collection
		 */
		public function run();

	}
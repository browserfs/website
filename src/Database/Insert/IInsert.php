<?php

	namespace browserfs\website\Database\Insert;

	/**
	 * This interface is implementing the INSERT \browserfs\website\Database statement driver implementation.
	 * All drivers that are implementing the \browserfs\website\Database\ITableInterface should return
	 * an implementation of this interface ( extend the \browserfs\website\Database\Insert class ).
	 *
	 * In the terminology of this interface, there are method names inspired from SQL language, but
	 * the programmer should think in a abstract way. This INSERT interface should make an abstraction of the
	 * naming of a specific database vendor implementation.
	 */
	interface IInsert {

		/**
		 * Returns the table on which the insert operation is made.
		 * @return \browserfs\website\Database\Table
		 */
		public function getTable();

		/**
		 * Returns the originating "INSERT" ( on sql terminology ) statement for this operation.
		 * We need this in order to obtain from this statement the fields
		 * list ( INSERT INTO <table_or_collection_name> ( ... ) )
		 * @return \browserfs\website\Database\Insert
		 */
		public function getInsert();

		/**
		 * Returns the value of this statement that is implementing this interface.
		 * @return any
		 */
		public function value();

		/**
		 * Returns a traversible collections, containing the resulting rows
		 * for this select statement. The primary keys are fetched from the server
		 * on the objects of the collection, and merged into the original inserted object.
		 * @return \browserfs\base\Collection
		 */
		public function run();

	}
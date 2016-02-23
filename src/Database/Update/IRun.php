<?php
	
	namespace browserfs\website\Database\Update;

	/**
	 * Interface for defining an UPDATE execution statement.
	 */
	interface IRun {

		/**
		 * Executes the RUN statement. Returns the number of affected rows
		 * @return int
		 */
		public function exec();

	}
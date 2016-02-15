<?php

	namespace browserfs\website\Database\Delete;

	interface IRun {
		
		/**
		 * @return int - The number of deleted rows
		 */
		public function exec();
	}
<?php
	
	namespace browserfs\website\Database\Driver\MySQL\Statement;

	class Test extends \browserfs\website\Database\Driver\MySQL\Statement {

		public function __construct( $database, $statement ) {
			parent::__construct( $database, $statement );
		}

		/**
		 * @return string - The parsed query.
		 */
		public final function run() {

			throw new \browserfs\Exception('The run method is not implemented on the test class ' . self::class );

		}

	}
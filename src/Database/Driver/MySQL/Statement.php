<?php

	namespace browserfs\website\Database\Driver\MySQL;

	abstract class Statement extends \browserfs\website\Database\Statement {

		public function __construct( $database, $statement ) {

			parent::__construct( $database, $statement );

		}

		public function toString() {
			return $this->statement;
		}

		/**
		 * Creates a statement. Use this as a factory.
		 * @param \browserfs\website\Database\Driver\MySQL $db
		 * @param string $statement
		 */
		public static function create( $db, $statement ) {

			if ( !is_string( $statement ) ) {
				throw new \browserfs\Exception('Invalid argument $statement: Expected string!' );
			}

			if ( !( $db instanceof \browserfs\website\Database\Driver\MySQL ) ) {
				throw new \browserfs\Exception('Invalid argument $db: Expected instanceof \browserfs\website\Databatase\Driver\MySQL' );
			}

			// Create a SQL parser, which parses the query.
			// With the help of the parser, we can determine what kind of query this is.
			$parser = new SQL\Parser( $statement );

			switch ( $parser->getQueryType() ) {

				// TODO! switch ( $parser->getQueryType() ) { case ... return ... }
				// ...

				
				default:
					return new Statement\Test( $db, $parser->getParsedQuery() );
					break;
			}
			

		}

	}
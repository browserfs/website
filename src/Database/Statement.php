<?php
	
	namespace browserfs\website\Database;

	abstract class Statement {


		const STATEMENT_UNKNOWN = 0;
		const STATEMENT_SELECT  = 1;
		const STATEMENT_INSERT  = 2;
		const STATEMENT_UPDATE  = 3;
		const STATEMENT_DELETE  = 4;

		/**
		 * The database instance where this statement is executed
		 * @var \browserfs\website\Database
		 */
		protected $db  = null;

		/**
		 * The statement, in it's string format
		 * @var string
		 */
		protected $sql = null;

		/**
		 * The statement type. The statement type should be determined by the
		 * sub classes constructor.
		 * @var int
		 */
		protected $type = self::STATEMENT_UNKNOWN; // Equal with self::STATEMENT_UNKNOWN

		/**
		 * Constructor. Creates a new statement
		 */
		public function __construct(
			\browserfs\website\Database $db,
			$statement
		) {

			if ( !( $db instanceof \browserfs\website\Database ) ) {
				throw new \browserfs\Exception( 'Invalid argument $db: Expected instanceof \browserfs\website\Database!' );
			} else {
				$this->db = $db;
			}

			if ( !is_string( $statement ) ) {
				throw new \browserfs\Exception( 'Invalid argument $statement: string expected (got: ' . json_encode( $statement ) . ')!' );
			} else {
				$this->statement = $statement;
			}

			$this->type = self::STATEMENT_UNKNOWN;

		}

		/**
		 * Returns the query, without comments or multi-line.
		 * @return string
		 */
		public abstract function toString();

		/**
		 * Executes the statement. Depending on the statement type,
		 * it returns different kind of data.
		 */
		public abstract function run();

	}
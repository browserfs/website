<?php

	namespace browserfs\website\Database\Driver\MySQL\SQL;

	class Parser extends \browserfs\string\Parser {

		protected static $tokens = [
			'WHITESPACE'    => '/^([\s]+)/',
			'COMMENT'       => '/^(([\-]{2,}|[#]{1,})[^\n\r$]+|\/\*[\s\S\r\n$]+?(\*\/|$))/',
			'TOK_STATEMENT' => '/^(SELECT|INSERT|UPDATE|DELETE)/i'
		];

		/**
		 * @var int. One of \browserfs\website\Database\Statement::STATEMENT_* constants
		 */
		protected $queryType = \browserfs\website\Database\Statement::STATEMENT_UNKNOWN;

		/**
		 * @var string. The parsed query without arguments.
		 */
		protected $parsedQuery = null;

		public function __construct( $sqlQuery ) {

			parent::__construct( $sqlQuery );

			$this->setFileName( 'sql://' . base64_encode( $sqlQuery ) );

			$this->parse();

		}


		/**
		 * Returns the type of query this MySQL is about.
		 */
		public function getQueryType() {
			$qt = $this->queryType;
			return $qt;
		}

		/**
		 * Returns the parsed query without comments.
		 * @return string
		 */
		public function getParsedQuery() {
			return $this->parsedQuery;
		}


		/**
		 * If the $reader can read token $tokName, consumes it's content and
		 * returns true. Otherwise, returns false.
		 */
		protected static function read( $tokName, \browserfs\string\Parser $reader ) {
			
			if ( !is_string( $tokName ) ) {
				throw new \browserfs\runtime\Exception('Invalid argument $tokName: expected string');
			}

			if ( array_key_exists( $tokName, self::$tokens ) ) {

				$consume = null;

				$matches = $reader->canReadExpression( self::$tokens[ $tokName ] );
				
				if ( $matches ) {
					$reader->consume( $consume === null ? strlen( $matches[0] ) : $consume );
					return true;
				} else {
					return false;
				}
			
			} else {
				throw new \browserfs\runtime\Exception('Unknown parser token name: ' . $tokName );
			}

		}

		/**
		 * If the $reader can read token $tokName, returns it's contents, otherwise
		 * returns false.
		 */
		protected static function readString( $tokName, \browserfs\string\Parser $reader ) {
			
			if ( !is_string( $tokName ) ) {
				throw new \browserfs\runtime\Exception('Invalid argument $tokName: expected string');
			}

			if ( array_key_exists( $tokName, self::$tokens ) ) {

				$matches = $reader->canReadExpression( self::$tokens[ $tokName ] );
				
				if ( $matches ) {
					$reader->consume( strlen( $matches[0] ) );
					return $matches[0];
				} else {
					return false;
				}
			
			} else {
				throw new \browserfs\runtime\Exception('Unknown parser token name: ' . $tokName );
			}

		}

		/**
		 * Reads any successive white spaces or comments. Returns true if at least one whitespace
		 * or one comment was read.
		 */
		protected static function readWhiteSpaceOrComment( \browserfs\string\Parser $reader ) {
			
			$matches = 0;

			do {

				$result = false;

				if ( $reader->eof() ) {
					break;
				}

				if ( self::read( 'WHITESPACE', $reader ) ) {
					$result = true;
					$matches++;
					continue;
				}

				if ( self::read( 'COMMENT', $reader ) ) {
					$result = true;
					$matches++;
					continue;
				}

				if ( $reader->eof() ) {
					$result = true;
				}

			} while ( $result == true );

			return $matches > 0;

		}


		protected function parseSelect() {
			return ' /* select */';
		}

		protected function parseInsert() {
			return ' /* insert */';
		}

		protected function parseUpdate() {
			return ' /* update */';
		}

		protected function parseDelete() {
			return ' /* delete */';
		}


		/**
		 * Parses the SQL query
		 */
		protected function parse() {

			self::readWhiteSpaceOrComment( $this );

			if ( $this->eof() ) {
				throw new \browserfs\Exception('Empty SQL!');
			}

			$result = "";

			$stmt = self::readString( 'TOK_STATEMENT', $this );

			if ( $stmt === FALSE ) {
				throw new \browserfs\Exception('Expected supported SQL_STATEMENT (INSERT|DELETE|UPDATE|SELECT)' );
			}

			if ( !self::readWhiteSpaceOrComment( $this ) ) {
				throw new \browserfs\Exception('Expected WHITE_SPACE|COMMENT after SQL_STATEMENT' );
			}

			$result = strtoupper( $stmt );

			switch ( $result ) {
				case 'SELECT':
					$this->queryType = \browserfs\website\Database\Statement::STATEMENT_SELECT;
					$result .= $this->parseSelect();
					break;
				case 'INSERT':
					$this->queryType = \browserfs\website\Database\Statement::STATEMENT_INSERT;
					$result .= $this->parseInsert();
					break;
				case 'UPDATE':
					$this->queryType = \browserfs\website\Database\Statement::STATEMENT_UPDATE;
					$result .= $this->parseUpdate();
					break;
				case 'DELETE':
					$this->queryType = \browserfs\website\Database\Statement::STATEMENT_DELETE;
					$result .= $this->parseDelete();
					break;
				default:
					throw new \browserfs\Exception('Unhandled query verb: ' . $result );
					break;
			}

			$this->parsedQuery = $result;

		}

	}
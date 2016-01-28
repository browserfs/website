<?php
	
	namespace browserfs\website\Database;

	abstract class Table extends \browserfs\EventEmitter implements ITableInterface  {

		protected $name = null;
		protected $db   = null;

		public function __construct( \browserfs\website\Database $db, $tableName ) {

			if ( !( $db instanceof \browserfs\website\Database ) ) {
				throw new \browserfs\Exception('Invalid argument $db: Expected a \\browserfs\\website\\Database instance' );
			}

			if ( !static::isTableName( $tableName ) ) {
				throw new \browserfs\Exception('Invalid argument $tableName: Expected a string representing a valid table name!' );
			}

			$this->name = $tableName;
			$this->db   = $db;

			$self = $this;

			// Audit feature: Fire an event call "query" on the database!
			$this->on( 'query', function( \browserfs\Event $e ) use ( &$self ) {
				
				$theQuery = $e->getArg(0);
				$myTableName = $self->name();

				// bubble-up
				$self->db()->fire( 'query', $myTableName, $theQuery );
			});

		}

		abstract public function select( $what = null );

		abstract public function update( $fields );

		abstract public function delete( $where );

		abstract public function insert( $fields );

		public function name() {
			return $this->name;
		}

		public function db() {
			return $this->db;
		}

		protected static function isTableName( $str ) {
			return is_string( $str ) && preg_match( '/^[a-zA-Z0-9_]([a-zA-Z0-9_]+)$/', $str );
		}

	}
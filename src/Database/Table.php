<?php
	
	namespace browserfs\website\Database;

	/**
	 * This class implements an abstract table on a database "driver", and is instantiated
	 * by the \browserfs\website\Database class.
	 *
	 * Database driver implementations should extend this class.
	 */

	abstract class Table extends \browserfs\EventEmitter implements ITableInterface  {

		/**
		 * @var string
		 */
		protected $name = null;

		/**
		 * @var \browserfs\website\Database
		 */
		protected $db   = null;

		/**
		 * Constructor. Instantiates a new table.
		 *
		 * @param \browserfs\website\Database $db - the database in which this table is located.
		 * @param string $tableName - the name of the table
		 */
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

		/**
		 * Executes a "SELECT" or a query on this table.
		 *
		 * @param array $what - An associative or string[] array with the fields.
		 *        There are three types of fields lists that can be
		 *        specified:
		 *
		 *        // select columns "foo", "bar", "car"
		 *        ->select( [ 'foo', 'bar', 'car' ] )
		 *
		 *        // select columns "foo", "bar"
		 *        ->select( [ 'foo' => true, 'bar' => true ] )
		 *
		 *        // select all columns excepting "foo", "bar"
		 *        ->select( [ 'foo' => false, 'bar' => false ] )
		 *
		 * @return browserfs\website\Database\Select\ISelect (interface) - a chainable object
		 *        where we can specify additional select arguments.
		 *
		 */
		abstract public function select( $what = null );

		/**
		 * Executes a "UPDATE" statement on this table.
		 *
		 * @param array $fields - A hash array with indexes representing the field names,
		 *        and values representing the new values.
		 */
		abstract public function update( $fields );

		/**
		 * Executes a "DELETE" statement on this table ( or a REMOVE operation ).
		 *
		 * @param array $where - A hash array representing the "WHERE" statement.
		 *        // delete where ( id > 3 ) and ( disabled = true )
		 *		  ->delete( [ 'id' => [ '$gt' => 3 ], 'disabled' => true ] )
		 */
		abstract public function delete( $where );

		/**
		 * Executes an "INSERT" operation on this table.
		 *
		 * @param array $fields - Fields to be inserted, in the format of an array:
		 *        ->insert( [ 'foo' => 3, 'bar' => null, 'car' => 'mercedes' ] )
		 * @return a copy of the $fields argument, where the last insert id keys
		 *        are merged. For example -> insert( [ "name" => "Foo" ] ) would
		 *        return a [ "name" => "Foo", "id" => 23 ] result.
		 */
		abstract public function insert( $fields );

		/**
		 * Returns the name of this table.
		 * @return string
		 */
		public function name() {
			return $this->name;
		}

		/**
		 * Returns the parent database of this table.
		 * @return \brwoserfs\website\Database
		 */
		public function db() {
			return $this->db;
		}

		/**
		 * Returns true if a string is a valid table name.
		 * @param string $str - the name of a table
		 * @return boolean - TRUE if string is valid, FALSE otherwise.
		 */
		protected static function isTableName( $str ) {
			return is_string( $str ) && preg_match( '/^[a-zA-Z0-9_]([a-zA-Z0-9_]+)((\\.[a-zA-Z0-9_]([a-zA-Z0-9_]+))+)?$/', $str );
		}

	}
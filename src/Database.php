<?php
	
	namespace browserfs\website;

	/**
	 * The database class is an abstract class representing a driver implementation
	 * of a specific database protocol ( e.g. "mysql", "mongodb", "firebase", etc. );
	 *
	 * Database drivers should extend this class, and do their best effort to implement the
	 * following features:
	 *
	 * { get } [ index: string ]: \browserfs\website\Database\Table
	 *    -> register a getter on each table on the database.
	 *
	 * Each table obtained via the ->table() method or via a the driver database getter,
	 * should support the basic 4 CRUD operations on a table:
	 *     -> select
	 *     -> update
	 *     -> delete
	 *     -> insert
	 *
	 * Each driver class, should register itself, during the class require phase,
	 * by using the following method call:
	 *
	 *     \browserfs\website\Database::registerDriver( 
	 *         "driver-scheme-name", 
	 *         "full-namespace-upto-driver-class-notation"
	 *     );
	 *
	 * eg: If you want to implement a mssql driver, you should use the command:
	 *
	 *     \browserfs\website\Database::registerDriver( "mssql", "\\foovendor\\foonamespace\\MsSQL" );
	 *
	 * Each operation executed on a driver table, should fire an event on a table:
	 *
	 *    "query" ( string $executedQuery ),
	 *
	 * which should bubble up to it's database object, who would fire by itself:
	 *
	 *    "query" ( string $tableName, string $query )
	 *
	 * This is to ensure audit and benchmark is available on that driver type.
	 *
	 * Also, the driver should be able to instantiate it's tables on a sigleton patter,
	 * this allowing us to register events on a driver collection or table:
	 *
	 *     $driver->persons->on( 'query', function( $queryString ) { ... } );
	 *
	 */

	abstract class Database extends \browserfs\EventEmitter {

		protected $host     = null;
		protected $port     = null;
		protected $user     = null;
		protected $password = null;

		// database name
		protected $database = null;

		// extra database initialization arguments.
		protected $initArgs = null;
		
		// dsn stands for Data Source Name.
		protected $dsn      = null;

		private static $factories = [];

		protected function __construct( $config, $databaseSourceName ) {

			if ( !is_string( $databaseSourceName ) || strlen( $databaseSourceName ) == 0 ) {
				throw new \browserfs\Exception('Invalid argument $databaseSourceName: expected string | null' );
			}

			$this->dsn = $databaseSourceName;

			if ( isset( $config['host'] ) ) {
				$this->host = $config['host'];
			}

			if ( isset( $config['port'] ) ) {
				$this->port = (int)$config['port'];
			}

			if ( isset( $config['user'] ) ) {
				$this->user = $config['user'];
			}

			if ( isset( $config['pass'] ) ) {
				$this->password = $config['pass'];
			}

			if ( isset( $config['path'] ) ) {

				if ( preg_match( '/^(\\/)([a-zA-Z]([a-zA-Z0-9]+)?)$/', $config['path'], $matches ) ) {

					$this->database = $matches[2]; 

				} else {

					throw \browserfs\Exception( '"' . $config['path'] . '" is not a valid database name!' );

				}

			} else {

				throw new \browserfs\Exception( 'Failed to determine database name' );

			}

			if ( isset( $config['query'] ) ) {
				$this->initArgs = $config['query'];
			}

		}

		/**
		 * Returns a Database driver specific table implementation.
		 * @param  $from: string
		 * @return \browserfs\Database\Table
		 */

		abstract public function table( $from );

		/**
		 * Escapes a value according to specific database engine implementation
		 * @param $mixed: any
		 * @return any
		 */
		abstract public function escape( $mixed );

		/**
		 * Returns a value to the database driver specific implementation.
		 * Use the returned value of this method in order to perform advanced queries,
		 *    straight on the PDO for example.
		 * @return any - the native driver ( usually implemented by the php extension )
		 */
		abstract public function getNativeDriver();

		/**
		 * Static constructor.
		 */
		public static function factory( $databaseType, $databaseConfig, $databaseSourceName ) {

			if ( !is_string( $databaseType ) || $databaseType == '' ) {
				throw new \browserfs\Exception('Invalid argument $databaseType: non-empty string expected' );
			}

			if ( !is_array( $databaseConfig ) ) {
				throw new \browserfs\Exception('Invalid argument $databaseConfig: array expected!' );
			}

			if ( !is_string( $databaseSourceName ) || $databaseSourceName == '' ) {
				throw new \browserfs\Exception('Invalid argument $databaseSourceName: non-empty string expected' );
			}

			if ( !isset( self::$factories[ $databaseType ] ) ) {

				throw new \browserfs\Exception('Failed to instantiate database of type "' . $databaseType . '": No driver provider registered!' );

			}

			try {

				$className = self::$factories[ $databaseType ];

				if ( !class_exists( $className ) ) {
					throw new \browserfs\Exception('Class "' . $className . '" implementing database driver "' . $databaseType . '" was not found!' );
				}

				$result = new $className( $databaseConfig, $databaseSourceName );

				if ( !( $result instanceof \browserfs\website\Database ) ) {

					throw new \browserfs\Exception('Although a declared database driver for protocol "' . $databaseType . '" was found in class "' . $className . '", it could not be used because it does not extends class \\browserfs\\website\\Database' );

				}

				return $result;

			} catch ( \Exception $e ) {

				throw new \browserfs\Exception('Failed to initialize database source "' . $databaseSourceName . '": ' . $e->getMessage(), 1, $e );

			}

		}

		/**
		 * Registers a database driver-specific implementation provider driver.
		 * @param $databaseSchemeName: string ( e.g: "mysql", "mongo", "firebase", etc. )
		 * @param $implementingClassWithFullNamespace: string ( e.g: "\vendor\namespace\MySQLDriver" )
		 * @return void
		 * @throws \browserfs\Exception on invalid arguments.
		 */
		public static function registerDriver( $databaseSchemeName, $implementingClassWithFullNamespace ) {

			if ( !is_string( $databaseSchemeName ) || $databaseSchemeName == '' ) {
				throw new \browserfs\Exception('Invalid argument $databaseSchemeName: non-empty string expected!' );
			}

			if ( !is_string( $implementingClassWithFullNamespace ) || $implementingClassWithFullNamespace === '' ) {
				throw new \browserfs\Exception('Invalid argument $implementingClassWithFullNamespace: non-empty string expected' );
			}

			if ( isset( self::$factories[ $databaseSchemeName ] ) ) {
				throw new \browserfs\Exception('A driver imlpementing "' . $databaseSchemeName . '" is allready registered!' );
			}

			self::$factories[ $databaseSchemeName ] = $implementingClassWithFullNamespace;

		}

		/**
		 * Returns TRUE if a specific driver implementation is supported.
		 * @param $databaseSchemeName: string ( e.g.: "mysql", "mongo", "firebase", etc. )
		 * @return boolean - true if specific implementation is supported
		 */
		public static function supportsDriverType( $databaseSchemeName ) {
			
			return is_string( $databaseSchemeName )
				? isset( self::$factories[ $databaseSchemeName ] )
				: false;

		}

		/**
		 * Returns TRUE if a connection to the database is allready made, or FALSE otherwise.
		 * @return boolean
		 */
		abstract public function isConnected();

		/**
		 * Returns TRUE if a connection to the database is allready made, or
		 * tries to connect to the database, or FALSE otherwise.
		 * @return boolean
		 */
		abstract public function connect();

	}

\browserfs\website\Database::registerDriver( 'mysql', '\\' . __NAMESPACE__ . '\\Database\Driver\MySQL' );
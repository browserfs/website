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

	abstract class Database extends \browserfs\EventEmitter implements \browserfs\website\Service\IServiceInterface
	{

		private   $instantiator = null;

		protected $host        = null;
		protected $port        = null;
		protected $user        = null;
		protected $password    = null;

		/**
		 * Database name
		 * @var string
		 */
		protected $database = null;

		/**
		 * extra database initialization arguments. These arguments are
		 * passed as query string to the ini section of the databse dsn.
		 * e.g. in ini file: 
		 *   default = mysql://localhost?[foo=bar&car=moo] // the segment between [...] this is the init args
		 * @var string[]
		 */
		protected $initArgs = [];

		/**
		 * DSN stands for Data Source Name. A unique identifier of type string
		 * for allowing us to identify this database connection.
		 * @var string
		 */
		protected $dsn = null;

		/**
		 * A list with registered database driver names ( uri scheme ) which
		 * points to the fully qualified namespaces implementing drivers for
		 * those driver implementation classes.
		 * @var [ $index: string ]: string
		 */
		private static $factories = [];

		/**
		 * Returns the cache service this database is using. The cache service is
		 * instantiated by the \browserfs\website\IServiceInterface interface.
		 */
		private $cache = null;

		/**
		 * Creates a new database. Drivers should extend this class.
		 *
		 * @param array $config [ "host"?: <string>, "port"?: <int>, "user"?: <string>, "pass"?: <string>, "path"?: <string>, "query"?: <string>  ]
		 * @param string $databaseSourceName - a unique identifier for this database connection
		 */
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
			
				parse_str( $config['query'], $this->initArgs );

				//$this->initArgs = $config['query'];
			
			}

		}

		/**
		 * Returns a Database driver specific table implementation.
		 * @param  string $from
		 * @return \browserfs\Database\Table
		 */

		abstract public function table( $from );

		/**
		 * Escapes a value according to specific database engine implementation
		 *
		 * @param  any $mixed
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
		 * Static constructor. Creates a database driver based on a specific implementation,
		 * and a specific configuration. Use this for the class constructor, do not use the
		 * new operator.
		 *
		 * @param string $databaseType - the database type ( e.g.: "mysql", "mongodb", etc. )
		 * @param array  $databaseConfig - the connection configuration settings ( see __construct )
		 * @param string $databaseSourceName - the name of the connection ( see __construct )
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
		 *
		 * @param string $databaseSchemeName - ( e.g: "mysql", "mongo", "firebase", etc. )
		 * @param string $implementingClassWithFullNamespace - e.g: "\vendor\namespace\MySQLDriver"
		 * @return void
		 * @throws \browserfs\Exception - on invalid arguments.
		 */
		public static function registerDriver( $databaseSchemeName, $implementingClassWithFullNamespace ) {

			if ( !is_string( $databaseSchemeName ) || $databaseSchemeName == '' ) {
				throw new \browserfs\Exception('Invalid argument $databaseSchemeName: non-empty string expected!' );
			}

			if ( !is_string( $implementingClassWithFullNamespace ) || $implementingClassWithFullNamespace === '' ) {
				throw new \browserfs\Exception('Invalid argument $implementingClassWithFullNamespace: non-empty string expected' );
			}

			if ( isset( self::$factories[ $databaseSchemeName ] ) ) {
				throw new \browserfs\Exception('A driver imlpementing "' . $databaseSchemeName . '" database is allready registered!' );
			}

			self::$factories[ $databaseSchemeName ] = $implementingClassWithFullNamespace;

		}

		/**
		 * Returns TRUE if a specific driver implementation is supported.
		 * @param string $databaseSchemeName - the name of the driver implementation ( e.g.: "mysql", "mongo", "firebase", etc. )
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

		/**
		 * interface \browserfs\website\IServiceInterface.setDIInjector implementation
		 */
		public function setDIInjector( \browserfs\website\Config $injector ) {
			
			if ( !( $injector instanceof \browserfs\website\Config ) ) {
				throw new \browserfs\Exception('The injector must be a \browserfs\website\Config' );
			}
			
			$this->instantiator = $injector;
		}

		/**
		 * interface \browserfs\website\IServiceInterface.getDIInjector implementation
		 */
		public function getDIInjector() {
			
			if ( $this->instantiator === null ) {
				throw new \browserfs\Exception('getDIInjector must be called after setDIInjector!');
			}
			
			return $this->instantiator;
		}

		/**
		 * Returns the default caching service of the database.
		 * The default caching service of the database is storing database
		 * schema, etc.
		 *
		 * @return \browserfs\website\Cache
		 */
		public function getCache() {

			if ( $this->cache === null ) {

				if ( array_key_exists('cacheSourceName', $this->initArgs ) ) {
					$cacheSourceName = $this->initArgs['cacheSourceName'];
					$this->cache = $this->getDIInjector()->getService('cache')->{$cacheSourceName}->createNamespace('db_schema_' . $this->dsn );
				} else {
					try {
						$this->cache = $this->getDIInjector()->getService('cache')->default->createNamespace('db_schema_' . $this->dsn );
					} catch ( \browserfs\Exception $e ) {
						$this->cache = new \browserfs\website\Cache\Driver\Memory(null, 'memory');
					}
					
				}
			}

			return $this->cache;

		}

		/**
		 * Executes any arbitrary SQL statement
		 * @param   $statement: string
		 * @return  \browserfs\website\Database\Statement
		 */
		abstract public function SQL( $statement );

		abstract public function execDumpFile( $filePath );

	}

\browserfs\website\Database::registerDriver( 'mysql', '\\' . __NAMESPACE__ . '\\Database\Driver\MySQL' );
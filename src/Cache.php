<?php

	namespace browserfs\website;

	/**
	 * The browserfs\website\Cache implements a generic cache driver. All cache driver
	 * implementations should extend this class.
	 */
	abstract class Cache extends \browserfs\EventEmitter
	{

		/**
		 * @var string -> the cache source name
		 */
		protected $name = null;

		/**
		 * @var string -> a prefix used by the 'get' and the 'set' methods
		 */
		protected $namespaces = [];

		/**
		 * @var string[] -> a mapping between each cache scheme name,
		 *      and it's implementing full qualified namespace classes.
		 */
		private static $factories = [];

		/**
		 * Creates a new cache instance. Protected, do not use the constructor,
		 * use the "factory" method instead.
		 * @param $config -> [ "host"?: <string>, "port"?: <int>, "user"?: <string>, "pass"?: <string>, "path"?: <string>, "query"?: <string> ]
		 * @param string $cacheSourceName - the name of the cache.
		 */
		protected function __construct( $config, $cacheSourceName ) {
			
			if ( !is_string( $cacheSourceName ) || strlen( $cacheSourceName ) === 0 ) {
				throw new \browserfs\Exception('Invalid argument $cacheSourceName: Expected non-empty string' );
			}
			
			$this->name = $cacheSourceName;

		}

		/**
		 * Returns the value of a property called "$key" from the cache.
		 * @param string $key - the name of the property to be retrieved.
		 */
		abstract public function get( $key );

		/**
		 * Sets the value of a property called "$key" in the cache.
		 * @param string $key - the name of the property to be set
		 * @param string $value - the serialized value of the property.
		 *     A cache driver implementation should check if the value
		 *     is of type string, or throw an exception.
		 * @param int $timeToLive - the time ( expressed in seconds ) that the
		 *     cache is valid. After this time, the cache expires.
		 *     -1 - the cache will never expire. For cache engines which are not persistent,
		 *          this is equal with the "0" value.
		 *      0 - the cache will expire upon script termination
		 *     >0 - the cache will expire in $timeToLive seconds
		 *     
		 */
		abstract public function set( $key, $value, $timeToLive );

		/**
		 * Eliminates from the cache store the property with the name
		 * called $key
		 * @param string $key - the name of the property which will be eliminated.
		 */
		abstract public function delete( $key );

		/**
		 * Resets the cache (delete all it's properties)
		 */
		abstract public function clear();

		/**
		 * Creates a Cache namespace warpper. A Cache namespace wrapper
		 * @param string $nsName
		 * @return \browserfs\website\Cache
		 */
		public function createNamespace( $nsName ) {
			if ( !is_string( $namespace ) || !$nsName === '' ) {
				throw new \browserfs\Exception('Invalid argument $nsName: Expected non-empty string' );
			}

			return isset( $this->namespaces[ $nsName ] )
				? $this->namespaces[ $nsName ]
				: $this->namespaces[ $nsName ] = \browserfs\website\Cache\NamespaceWrapper::create( $this, $nsName );
		}

		/**
		 * Returns the name of the cache object
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}

		/**
		 * Returns TRUE if the cache engine has a registered driver implementation for a
		 * specific URL scheme name, and false otherwise.
		 * @param string $cacheSchemeName - the name of the scheme ( lowercased )
		 * @return boolean
		 */
		public static function supportsDriverType( $cacheSchemeName )
		{
			return is_string( $cacheSchemeName )
				? isset( self::$factories[ $cacheSchemeName ] )
				: false;
		}

		/**
		 * Registers a cache driver for a specific scheme name.
		 * @param string $cacheSchemeName: the scheme ( e.g: "memcache", "apc", "memory", etc. )
		 * @param string $implementingClassWithFullNamespace - the name of the class that is
		 *     implementing this cache driver. The class must extend \browserfs\website\Cache
		 *     class ( this class ).
		 * @return void
		 * @throws \browserfs\Exception on invalid arguments
		 */
		public static function registerDriver( $cacheSchemeName, $implementingClassWithFullNamespace )
		{
			if ( !is_string( $cacheSchemeName ) || $cacheSchemeName === '' ) {
				throw new \browserfs\Exception('Invalid argument $cacheSchemeName: Expected non-empty string');
			}

			if ( !is_string( $implementingClassWithFullNamespace ) || $implementingClassWithFullNamespace === '' ) {
				throw new \browserfs\Exception('Invalid argument $implementingClassWithFullNamespace: Expecting non-empty string' );
			}

			if ( isset( self::$factories[ $cacheSchemeName ] ) ) {
				throw new \browserfs\Exception('A driver implementing "' . $cacheSchemeName . '" cache is allready registered!' );
			}

			self::$factories[ $cacheSchemeName ] = $implementingClassWithFullNamespace;
		}

		/**
		 * Static factory constructor. Use this constructor instead of the "new ..." syntax.
		 */
		public static function factory( $cacheTypeSchemaName, $cacheDriverConnectionConfig, $cacheSourceName )
		{
			if ( !is_string( $cacheTypeSchemaName ) || $cacheTypeSchemaName === '' ) {
				throw new \browserfs\Exception('Invalid argument $cacheTypeSchemaName: non-empty string expected!');
			}
			if ( !is_array( $cacheDriverConnectionConfig ) ) {
				throw new \browserfs\Exception('Invalid argument $cacheDriverConnectionConfig: array expected!' );
			}
			if ( !is_string( $cacheSourceName ) || $cacheSourceName === '' ) {
				throw new \browserfs\Exception('Invalid argument $cacheSourceName: Expected non-empty string!' );
			}
			if ( !isset( self::$factories[ $cacheTypeSchemaName ] ) ) {
				throw new \browserfs\Exception('Failed to instantiate cache of type "' . $cacheTypeSchemaName . '": No driver provider registered!' );
			}
			try {
				
				$className = self::$factories[ $cacheTypeSchemaName ];

				if ( !class_exists( $className ) ) {
					throw new \browserfs\Exception('Class "' . $className . '" implementing cache of type "' . $cacheTypeSchemaName . '" was not found!' );
				}

				$result = new $className( $cacheDriverConnectionConfig, $cacheSourceName );

				if ( !( $result instanceof \browserfs\website\Cache ) ) {
					throw new \browserfs\Exception('Although a declared cache driver for scheme "' . $cacheTypeSchemaName . '" was found in class "' . $className . '", it could not be used because it does not extend \\browserfs\\website\\Cache');
				}

				return $result;

			} catch ( \Exception $e ) {

				throw new \browserfs\Exception('Failed to initialize cache source "' . $cacheSourceName . '": ' . $e->getMessage(), 1, $e );

			}
		}

	}

\browserfs\website\Cache::registerDriver('memory', '\\' . __NAMESPACE__ . '\\Cache\\Driver\\Memory' );
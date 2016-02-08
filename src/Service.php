<?php

	namespace browserfs\website;

	class Service {

		// A list with built in factories for services. A developer can register classes for
		// services here, via the static method registerService();
		public static $factories = [];

		// The name of the service
		protected $name       = null;

		// The properties of the webservice. Can be accessed by ancestors via the "getConfigProperty" and
		// "getConfigPropertyMulti" methods.
		private   $properties = [];


		/**
		 * Creates a new Service. Should be called by all the children that are implementing this class.
		 * The constructor is protected, in order to force the programmer to instantiate a service only via the
		 * static "factory" method.
		 */
		protected function __construct( $serviceProperties = null ) {
			
			if ( null !== $serviceProperties ) {
				
				if ( !is_array( $serviceProperties ) ) {
					throw new \browserfs\Exception( 'Invalid argument: $serviceProperties: Expected array' );
				}

				$this->properties = $serviceProperties;

			}

		}

		/**
		 * Return the value of the service configuration property $propertyName
		 * @param $propertyName: string - the name of the config property name
		 * @param $defaultValue: string | null - the default value of the property
		 * @return string | null
		 */
		public final function getConfigProperty( $propertyName, $defaultValue = null ) {

			$result = $defaultValue;

			if ( is_string( $propertyName ) ) {

				if ( isset( $this->properties[ $propertyName ] ) ) {

					$result = $this->properties[ $propertyName ][ count( $this->properties[ $propertyName ] ) - 1 ];

				}

			}

			return $result;

		}

		/**
		 * Return the value of the configuration service property $propertyName, as integer.
		 * @param $propertyName: string
		 * @param $defaultValue: int | null
		 * @return int | null
		 * @throws \browserfs\Exception on invalid arguments.
		 */
		public final function getConfigPropertyInt( $propertyName, $defaultValue = null ) {

			if ( null !== $defaultValue ) {
				
				if ( !is_int( $defaultValue ) ) {
					throw new \browserfs\Exception('Invalid argument: Expected int | null' );
				}

			}

			$result = $this->getConfigProperty( $propertyName, null );

			if ( is_string( $result ) ) {

				$result = preg_match( '/^(\\-)?[\\d]([\\d]+)?$/', $result )
					? (int)$result
					: $defaultValue;

			} else

			if ( $result === null ) {
			
				$result = $defaultValue;
			
			}

			return $result;

		}

		/**
		 * Return the value of the configuration service property $propertyName, as boolean.
		 * @param $propertyName: string
		 * @param $defaultValue: boolean | null
		 * @return boolean | null
		 * @throws \browserfs\Exception on invalid arguments.
		 */
		public final function getConfigPropertyBool( $propertyName, $defaultValue = null ) {

			if ( null !== $defaultValue ) {
				
				if ( !is_bool( $defaultValue ) ) {
					throw new \browserfs\Exception('Invalid argument: Expected boolean | null' );
				}

			}

			$result = $this->getConfigProperty( $propertyName, null );

			if ( is_string( $result ) ) {

				$result = preg_match( '/^(yes|true|y|1|on)$/i', $result )
					
					? true
					
					: (

						preg_match( '/^(no|false|n|0|off)$/i', $result )
							? false
							: $defaultValue

					);

			} else

			if ( $result === null ) {
			
				$result = $defaultValue;
			
			}

			return $result;

		}

		/**
		 * Returns all the values of a service configuration property.
		 * @return any[]
		 * @throws \browserfs\Exception on invalid argument
		 */
		public final function getConfigPropertyMulti( $propertyName, $defaultValue = null ) {

			if ( null !== $defaultValue && !is_array( $defaultValue ) ) {
				throw new \browserfs\Exception('Invalid argument $defaultValue: any[] | null expected!' );
			}

			$result = $defaultValue === null
				? []
				: $defaultValue;

			if ( is_string( $propertyName ) ) {

				if ( isset( $this->properties[ $propertyName ] ) ) {

					$result = $this->properties[ $propertyName ];

				}

			}

			return $result;

		}

		/**
		 * Returns all the names of this service configuration properties
		 * @return string[]
		 */
		public final function getConfigPropertyNames() {
		
			return array_keys( $this->properties );
		
		}

		/**
		 * Service Factory loader
		 * @param $serviceName:  string - the name of service ( e.g.: Database )
		 * @param $serviceType:  string - the type of the serviceName ( e.g.: Mongo )
		 * @param $appNamespace: string - the namespace in which this website is publishing it's classes
		 * @param $serviceProperties: array[ key: string ] => any - the initialization properties of this service
		 * @return \browserfs\website\Service
		 * @throws \browserfs\Exception - on invalid arguments
		 */
		public static function factory( $serviceName, $serviceType, $appNamespace, $serviceProperties = null ) {

			if ( !is_string( $serviceName ) ) {
				throw new \browserfs\Exception('Invalid argument $serviceName: expected string' );
			}

			if ( !is_string( $serviceType ) ) {
				throw new \browserfs\Exception('Invalid argument $serviceType: expected string' );
			}

			if ( !preg_match( '/^[a-zA-Z]([a-zA-Z\\d_]+)?$/', $serviceName ) ) {
				throw new \browserfs\Exception('Invalid service name "' . $serviceName . '"' );
			}

			if ( !preg_match( '/^[a-zA-Z]([a-zA-Z\\d_]+)?$/', $serviceType ) ) {
				throw new \browserfs\Exception('Invalid service type "' . $serviceType . '"' );
			}

			if ( null !== $serviceProperties && !is_array( $serviceProperties ) ) {
				throw new \browserfs\Exception('Invalid argument $serviceProperties: expected array | null' );
			}

			if ( !is_string( $appNamespace ) || strlen( $appNamespace ) === 0 ) {
				throw new \browserfs\Exception('Invalid argument: $appNamespace: non-empty string expected!' );
			}

			// test if we have a built-in factory for that service
			if ( isset( self::$factories[ $serviceName ] ) && isset( self::$factories[ $serviceName ][ $serviceType ] ) ) {
				
				$serviceClass = self::$factories[ $serviceName ][ $serviceType ];
				
				return new $serviceClass( $serviceProperties );
			
			} else {

				// try to guess service, based on application namespace argument

				$ucServiceName = ucfirst( $serviceName );
				$ucServiceType = ucfirst( $serviceType );

				$appNamespace = preg_match( '/\\$/', $appNamespace )
					? $appNamespace
					: $appNamespace . '\\';

				$searchClass  = $appNamespace . 'Service\\' . $ucServiceName . '\\' . $ucServiceType;

				if ( class_exists( $searchClass ) ) {

					$returnValue = new $searchClass( $serviceProperties );
				
					if ( !( $returnValue instanceof \browserfs\website\Service ) ) {
						throw new \browserfs\Exception( 'Although a class called "' . $searchClass . '" is implemented, it cannot be instantiated as a service, because it does not extend \\browserfs\\website\\Service class' );
					}

					return $returnValue;

				} else {

					$searchClassNative = '\\' . __NAMESPACE__  . '\\Service\\' . $ucServiceName . '\\' . $ucServiceType;

					if ( $searchClassNative != $searchClass && class_exists( $searchClassNative ) ) {

						return new $searchClassNative( $serviceProperties );

					} else {

						throw new \browserfs\Exception('Failed to initialize service "' . $serviceName . '" of type "' . $serviceType . '": Unknown implementation ( tried "' . implode( '" and "', array_unique( [ $searchClass, $searchClassNative ] ) ) . '" )');

					}

				}

			}

		}

		public static function registerService( $serviceName, $serviceType, $fullServiceNamespacedClass ) {


			if ( !is_string( $serviceName ) || $serviceName === '' ) {
				throw new \browserfs\Exception( 'Invalid argument $serviceName: Expected non-empty string' );
			}

			if ( !is_string( $serviceType ) || $serviceType === '' ) {
				throw new \browserfs\Exception( 'Invalid argument $serviceType: Expected non-empty string' );
			}

			if ( !is_string( $fullServiceNamespacedClass ) || $fullServiceNamespacedClass === '' ) {
				throw new \browserfs\Exception( 'Invalid argument $fullServiceNamespacedClass: Expected non-empty string' );
			}

			if ( isset( self::$factories[ $serviceName ] ) ) {

				if ( isset( self::$factories[ $serviceName ][ $serviceType ] ) ) {

					throw new \browserfs\Exception('Failed to register service "' . $serviceName . '" type "' . $serviceType . '": Allready registered!' );

				} else {

					self::$factories[ $serviceName ][ $serviceType ] = $fullServiceNamespacedClass;

				}

			} else {

				self::$factories[ $serviceName ] = [];

				self::$factories[ $serviceName ][ $serviceType ] = $fullServiceNamespacedClass;
			
			}

		}

	}

// Initialization part. Init some default services.
Service::registerService( 'staging',  'development', '\\' . __NAMESPACE__ . '\\Service\\Staging\\Development' );
Service::registerService( 'staging',  'staging',     '\\' . __NAMESPACE__ . '\\Service\\Staging\\Staging' );
Service::registerService( 'staging',  'production',  '\\' . __NAMESPACE__ . '\\Service\\Staging\\Production' );
Service::registerService( 'website',  'default',     '\\' . __NAMESPACE__ . '\\Service\\Website' );
Service::registerService( 'database', 'default',     '\\' . __NAMESPACE__ . '\\Service\\Database' );
Service::registerService( 'cache',    'default',     '\\' . __NAMESPACE__ . '\\Service\\Cache' );
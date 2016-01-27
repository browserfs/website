<?php

	namespace browserfs\website;

	class Config {

		protected $ini       = null;
		protected $services  = [];
		protected $staging   = 'development';
		protected $namespace = null;

		public function __construct( \browserfs\string\Parser\IniReader $reader, $applicationNamespace = null ) {
			
			if ( !( $reader instanceof \browserfs\string\Parser\IniReader ) ) {
				throw new \browserfs\Exception('Invalid argument $reader: Expected a instance of \\browserfs\\string\\Parser\\IniReader' );
			}

			$this->ini = $reader;

			if ( null !== $applicationNamespace && !is_string( $applicationNamespace ) ) {
				throw new \browserfs\Exception('Invalid argument $applicationNamespace: string | null expected');
			}

			if ( $applicationNamespace === null || $applicationNamespace === '' ) {
				$this->namespace = __NAMESPACE__;
			} else {
				$this->namespace = $applicationNamespace;
			}

			$this->initialize();

		}

		/**
		 * Initializes the website services, based on ini file configuration
		 */
		protected function initialize() {

			$sections = $this->ini->getSections();

			if ( !in_array( 'main', $sections ) ) {
				throw new \browserfs\Exception('A [main] section is required in configuration file.');
			}

			$services = $this->ini->getSectionProperties( 'main' );

			if ( in_array( 'staging', $services ) ) {
				
				$this->staging = $this->ini->getProperty('main', 'staging', 'development' );
				
				if ( !in_array( $this->staging, [ 'development', 'staging', 'production' ] ) ) {
					throw new \browserfs\Exception('Bad staging value inside of [main]: allowed "development", "staging", "production"' );
				}
			
			}

			foreach ( $services as $serviceName ) {

				if ( !in_array( $sectionName = ( $serviceName . '.' . $this->staging ), $sections ) ) {
					$sectionName = $serviceName;
				}

				if ( !in_array( $sectionName, $sections ) ) {
					throw new \browserfs\Exception('Failed to load service "' . $sectionName . '": An appropriated section for it does not exist in config file!' );
				}

				$serviceProperties = [];

				foreach ( $this->ini->getSectionProperties( $sectionName ) as $sectionPropertyName ) {
					$serviceProperties[ $sectionPropertyName ] = $this->ini->getPropertyMulti( $sectionName, $sectionPropertyName );
				}

				$this->services[ $serviceName ] = \browserfs\website\Service::factory( $serviceName, $this->ini->getProperty( 'main', $serviceName, '' ), $this->namespace, $serviceProperties );
			}

		}

		/**
		 * Returns the list with the names of all initialized services.
		 * @return string[]
		 */
		public function getRegisteredServicesNames() {
			return array_keys( $this->services );
		}

		/**
		 * Returns the service with the name $serviceName. The service must be configured
		 * during the initialization, or added later, with addService method.
		 * @param $serviceName: string - the name of the service
		 * @return \browserfs\website\Service
		 * @throws \browserfs\Exception on invalid argument, or service not found
		 */
		public function getService( $serviceName ) {

			if ( !is_string( $serviceName ) || ( strlen( $serviceName ) == 0 ) ) {
				throw new \browserfs\Exception('Invalid argument $serviceName. Expected non-empty string' );
			}

			if ( !isset( $this->services[ $serviceName ] ) ) {
				throw new \browserfs\Exception('Service "' . $serviceName . '" is not loaded!' );
			}

			return $this->services[ $serviceName ];

		}

		/**
		 * Returns true if a service with the name $serviceName has been loaded!
		 * @param $serviceName: string - the name of the service
		 */
		public function serviceLoaded( $serviceName ) {
			return is_string( $serviceName )
				? isset( $this->services[ $serviceName ] )
				: false;
		}

	}
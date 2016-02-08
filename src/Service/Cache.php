<?php

	namespace browserfs\website\Service;

	class Cache extends \browserfs\website\Service {

		protected $caches = [];

		protected function __construct( $serviceConfig ) {

			parent::__construct( $serviceConfig );

			$this->init();

		}

		private function init() {

			try {

				$cacheNames = $this->getConfigPropertyNames();

				foreach ( $cacheNames as $cacheName ) {

					$cacheURI = $this->getConfigProperty( $cacheName, null );

					if ( $cacheURI === null ) {
						throw new \browserfs\Exception('Failed to load cache source: ' . $cacheName );
					}

					$this->addCacheSource( $cacheName, $cacheURI );

				}

			} catch ( \browserfs\Exception $e ) {

				throw new \browserfs\Exception( 'Failed to initialize the Cache service: ' . $e->getMessage(), 1, $e );

			}

		}

		protected function addCacheSource( $cacheName, $cacheURI ) {

			$info = @parse_url( $cacheURI );

			if ( $info === false || !is_array( $info ) || !isset( $info['scheme'] ) ) {
				throw new \browserfs\Exception('URL "' . $cacheURI . '" is not a valid cache URI ( source = "' . $cacheName . '" )' );
			}

			$cacheType = $info[ 'scheme' ];

			$cache = \browserfs\website\Cache::factory( $cacheType, $info, $cacheName );

			$this->caches[ $cacheName ] = $cache;

		}

		public function get( $cacheSourceName ) {

			if ( is_string( $cacheSourceName ) ) {

				if ( isset( $this->caches[ $cacheSourceName ] ) ) {

					return $this->caches[ $cacheSourceName ];
				
				} else {

					throw new \browserfs\Exception( 
						$cacheSourceName === '' 
							? 'Invalid argument $cacheSourceName: Expected non-empty string' 
							: 'Cache source named "' . $cacheSourceName . '" was not found'
					);

				}

			} else {

				throw new \browserfs\Exception( 'Invalid argument $cacheSourceName: expected non-empty string!' );

			}

		}

		public function __get( $propertyName ) {

			return $this->get( $propertyName );

		}

	}
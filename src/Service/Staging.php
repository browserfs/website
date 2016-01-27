<?php

	namespace browserfs\website\Service;

	class Staging extends \browserfs\website\Service {
		
		protected $phpIniSet = [];
		protected $phpDefines= [];


		protected function __construct( $serviceProperties ) {

			parent::__construct( $serviceProperties );

			$this->init();

		}

		private function init() {

			try {

				$configPropertyNames = $this->getConfigPropertyNames();

				foreach ( $configPropertyNames as $propertyName ) {

					switch ( true ) {

						case preg_match( '/^php\\.ini_set\\.(.*)$/', $propertyName, $matches ) ? true : false:

							$varName = $matches[1];
							$value   = $this->getConfigProperty( $propertyName, null );
							$this->phpIniSet[ $varName ] = $value;
							break;

						case preg_match( '/^php\\.define\\.(.*)$/', $propertyName, $matches ) ? true : false:

							$varName = $matches[1];
							$value   = $this->getConfigProperty( $propertyName, null );
							$this->phpDefines[ $varName ] = $value;
							break;

						default:
							throw new \browserfs\Exception('Invalid property name "' . $propertyName . '" in [staging] section' );
							break;

					}

				}

			} catch ( \browserfs\Exception $e ) {

				throw new \browserfs\Exception('Failed to initialize service Staging: ' . $e->getMessage(), 1, $e );

			}

		}

		public function staging() {
			return 'development';
		}

		public final function getPhpIni( $iniSetPropertyName ) {
			return is_string( $iniSetPropertyName )
				? (
					isset( $this->phpIniSet[ $iniSetPropertyName ] )
						? $this->phpIniSet[ $iniSetPropertyName ]
						: null
				)
				: null;
		}

		public final function getPhpDefine( $defineName ) {

			return is_string( $defineName )
				? (
					isset( $this->phpDefines[ $defineName ] )
						? $this->phpDefines[ $defineName ]
						: null
				)
				: null;

		}

	}
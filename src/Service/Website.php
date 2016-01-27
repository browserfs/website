<?php

	namespace browserfs\website\Service;

	class Website extends \browserfs\website\Service {
		
		// path to htdocs folder ( real path )
		protected $htdocs    = null;

		// the name of the website ( www.example.com )
		protected $name      = null;

		// the name of the webserver software ( nginx, apache, etc. )
		protected $webserver = null;

		/**
		 * The service "Website" provides us with information about the
		 * website we're developing.
		 */
		protected function __construct( $properties ) {

			// THIS SHOULD BE THE FIRST INSTRUCTION
			parent::__construct( $properties );

			$this->init();

		}

		private function init() {

			try {

				// RESOLVE HTDOCS

				$htdocsPath = $this->getConfigProperty( 'htdocs', null );

				if ( $htdocsPath === null ) {
					throw new \browserfs\Exception('missing configuration property ( [website]/htdocs )!' );
				}

				$htdocsExpanded = @realpath( $htdocsPath );

				if ( $htdocsExpanded === FALSE ) {
					throw new \browserfs\Exception('the path "' . $htdocsPath . '" does not exist ( [website]/htdocs )!' );
				}

				if ( !is_dir( $htdocsExpanded ) ) {
					throw new \browserfs\Exception('the path "' . $htdocsPath . '" is not a directory ( [website]/htdocs )!' );
				}

				$this->htdocs = $htdocsExpanded;

				// GET SITE NAME

				$siteName = $this->getConfigProperty( 'name', null );

				if ( $siteName === null ) {
					throw new \browserfs\Exception('missing property ( [website]/name )!' );
				}

				$this->name = $siteName;

				// GET webserver software name. Default: "unknown"

				$this->webserver = $this->getConfigProperty( 'webserver', 'unknown' );

			} catch ( \browserfs\Exception $e ) {

				throw new \browserfs\Exception( 'Failed to initialize service "' . $this->name . '": ' . $e->getMessage(), 1, $e );

			}

		}

		public final function htdocs() {
			return $this->htdocs;
		}

		public final function name() {
			return $this->name;
		}

		public final function webserver() {
			return $this->webserver;
		}

	}
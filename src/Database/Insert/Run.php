<?php

	namespace browserfs\website\Database\Insert;

	abstract class Run {

		protected $insert = null;

		public function __construct( 

			$insertStatement 

		) {

			if ( !( $insertStatement instanceof \browserfs\website\Database\Insert ) ) {
				throw new \browserfs\Exception('Invalid argument: $insertStament: Expected instanceof \\browserfs\\website\\Database\\Insert !');
			}

			$this->insert = $insertStatement;

		}

		abstract public function exec();

	}
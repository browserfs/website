<?php

	namespace browserfs\website\Database\Driver\MySQL\Insert;

	class Run extends \browserfs\website\Database\Insert\Run {

		public function __construct( $insertStatement ) {

			parent::__construct( $insertStatement );
		}

		public function exec() {
			throw new \browserfs\Exception('Implement ' . self::class . '::run()' );

			return new \browserfs\Collection([]);
		}

	}
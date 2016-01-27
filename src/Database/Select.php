<?php
	
	namespace browserfs\website\Database;

	abstract class Select implements ISelectStatementInterface {

		protected $fields = null;

		public function __construct( $fields, \browserfs\website\Database\Table $table ) {

			if ( null !== $fields ) {

			}

		}

	}
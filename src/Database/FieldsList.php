<?php
	
	namespace browserfs\website\Database;

	/**
	 * This class is managing a Database fields list that will be extracted
	 * in a select method.
	 */

	class FieldsList {

		const FIELDS_INCLUDE_ALL  = 0;
		const FIELDS_INCLUDE_SOME = 1;
		const FIELDS_EXCLUDE_SOME = 2;

		protected $fields = [];
		protected $policy = null; // Can be one of self::FIELDS_* constants.

		public function __construct( $fields ) {

			if ( !is_array( $fields ) ) {
				throw new \browserfs\Exception( 'Invalid argument $fields: Expected array' );
			}

			if ( !count( $fields ) ) {

				$this->policy = self::FIELDS_INCLUDE_ALL;

			} else {

				// check if array is hash or not

				$isHash = false;

				foreach ( $fields as $k => $v ) {

					if ( !is_int( $k ) ) {
						$isHash = true;
						break;
					}

				}

				if ( !$isHash ) {

					$this->policy = self::FIELDS_INCLUDE_SOME;

					foreach ( $fields as $field ) {

						if ( !is_string( $field ) ) {
							throw new \browserfs\Exception( 'Invalid argument $fields: Expected string[]' );
						}

						$this->fields[] = $field;

					}

				} else {

					$policy = null;

					foreach ( $fields as $fieldName => $fieldValue ) {

						if ( !is_bool( $fieldValue ) ) {
							throw new \browserfs\Exception('Invalid argument $fields: Expected [ key: string ]: boolean' );
						}

						if ( $policy === null ) {
							
							$policy = $fieldValue;

						} else {

							if ( $policy !== $fieldValue ) {
								throw new \browserfs\Exception( 'Invalid argument $fields: Expected @all = true | @all = false');
							}

						}

						$this->fields[] = $fieldName;

					}

					if ( $policy === null ) {
						$this->policy = self::FIELDS_INCLUDE_ALL;
					} else
					if ( $policy === false ) {
						$this->policy = self::FIELDS_EXCLUDE_SOME;
					} else
					{
						$this->policy = self::FIELDS_INCLUDE_SOME;
					}

				}
			}

		}

		/**
		 * Returns TRUE if this fields list INCLUDES_ALL fields, or FALSE otherwhise
		 */
		public function allFields() {
			return $this->policy === self::FIELDS_INCLUDE_ALL;
		}

		/**
		 * Returns TRUE if this fields list CONTAINS_ONLY this fields, or FALSE otherwise
		 */
		public function someFields() {
			return $this->policy === self::FIELDS_INCLUDE_SOME;
		}

		/**
		 * Returns TRUE if this fields list EXCLUDES_ONLY this fields, or FALSE otherwise
		 */
		public function exceptFields() {
			return $this->policy === self::FIELDS_EXCLUDE_SOME;
		}

		/**
		 * If this fields list INCLUDES_ALL fields, returns NULL.
		 * Otherwise, returns a string[], containing the name of the fields
		 */
		public function fields() {
			return $this->policy === self::FIELDS_INCLUDE_ALL
				? null
				: $this->fields;
		}

	}
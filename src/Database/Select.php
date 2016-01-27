<?php
	
	namespace browserfs\website\Database;

	abstract class Select implements ISelectStatementInterface {

		protected $fields = null;
		protected $table  = null;

		public function __construct( $fields, \browserfs\website\Database\Table $table ) {

			if ( null !== $fields ) {
				$this->fields = new \browserfs\website\Database\FieldsList( $fields );
			}

			if ( ! ( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument: Expected \\browserfs\\website\\Database\\Table instance!' );
			}

			$this->table = $table;
		}

		public function table() {
			return $this->table;
		}

		public function db() {
			return $this->table->db();
		}

		public function fields() {
			return $this->fields === null
				? null
				: $this->fields->fields();
		}

		public function isAllFields() {
			return $this->fields === null || $this->fields->allFields();
		}

		public function isSomeFields() {
			return $this->fields !== null && $this->fields->someFields();
		}

		public function isExceptFields() {
			return $this->fields !== null && $this->fields->exceptFields();
		}

		abstract public function where( $filterCondition );

		abstract public function run();

	}
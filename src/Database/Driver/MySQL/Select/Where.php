<?php
	
	namespace browserfs\website\Database\Driver\MySQL\Select;

	class Where extends \browserfs\website\Database\Select\Where {

		public function __construct( 
			$filter, 
			\browserfs\website\Database\Driver\MySQL\Table  $table,
			\browserfs\website\Database\Driver\MySQL\Select $select 
		) {

			parent::__construct( $filter, $table, $select );

		}

		public function limit( $many ) {
			throw new \browserfs\Exception('Implementation limit in where()' );
		}

		public function skip( $many ) {
			
			return new \browserfs\website\Database\Driver\MySQL\Select\Skip(
				$many,
				$this->table,
				$this->select,
				$this
			);

		}

		public function run() {
			return new \browserfs\website\Database\Driver\MySQL\Select\Run(
				$this->table,
				$this->select,
				$this
			);
		}

		public function toString() {

			$filter = $this->value();

			if ( $filter === null ) {
				return 'TRUE';
			}

			return $this->encodeExpression( $filter );

		}

		protected function encodeExpression( $expression ) {

			if ( !is_array( $expression ) ) {
				throw new \browserfs\Exception( json_encode( $expression ) . ' is not a expression' );
			}

			$tokens = [];

			// An expression must have only properties in it's root.
			foreach ( $expression as $key => $value ) {

				switch ( true ) {

					case substr( $key, 0, 1 ) == '$':

						// only $not, $and, and $or operators can be located into the root.
						if ( !in_array( $key, [ '$or', '$and', '$not' ] ) ) {
							throw new \browserfs\Exception( 'Invalid expression: The ' . $key . ' operator cannot be located in the root of an expression' );
						}
						
						if ( $key == '$or' || $key == '$and' ) {
							$tokens[] = $this->encodeLogicalExpression( $value, $key == '$or' ? 'OR' : 'AND' );
						} else {
							$tokens[] = $this->encodeNegateExpression( $value );
						}

						break;

					default:
						$tokens[] = $this->encodePropertyValue( $value, $key );
						break;

				}

			}

			return '( ' . implode( ' ) AND ( ', $tokens ) . ' )';

		}

		protected function encodeLogicalExpression( $value, $logicalOperator ) {

			$tokens = [];

			if ( !is_array( $value ) || count( $value ) < 2 ) {
				throw new \browserfs\Exception('Invalid $' . strtolower( $logicalOperator ) . ' operator value: Expected non-empty array of expressions of minimum length = 2!' );
			}

			foreach ( $value as $expression ) {
				$tokens[] = $this->encodeExpression( $expression );
			}

			return '( ' . implode( ' ' . $logicalOperator . ' ', $tokens ) . ' )';

		}

		protected function encodeNegateExpression( $value ) {

			return 'NOT(' . $this->encodeExpression( $value ) . ')';

		}

		protected function encodePropertyValue( $value, $key ) {

			if ( !is_string( $key ) || !strlen( $key ) || strpos( $key, '.' ) !== false ) {
				throw new \browserfs\Exception('Invalid key name: ' . $key );
			}

			if ( $value === null ) {
				return '`' . $key . '` IS NULL';
			} else
			if ( is_int( $value ) || is_float( $value ) ) {
				return '`' . $key . '` = ' . (string)$value;
			} else
			if ( is_string( $value ) ) {
				return '`' . $key . '` = "' . addslashes( $value ) . '"';
			} else
			if ( is_bool( $value ) ) {
				return '`' . $key . '` = ' . (int)$value;
			} else
			{

				if ( !is_array( $value ) || !count( $value ) ) {
					throw new \browserfs\Exception( 'Invalid value for key ' . $key . ' provided' );
				}

				$tokens = [];

				// check that the value contains only operators
				foreach ( array_keys( $value ) as $valueKey ) {

					if ( substr( $valueKey, 0, 1 ) != '$' ) {
						throw new \browserfs\Exception( 'Invalid value for key ' . $key . ' provided: Only operators allowed in the root object!' );
					}

					if ( in_array( $valueKey, array( '$and', '$or', '$not' ) ) ) {
						throw new \browserfs\Exception( 'Logical operators not supported in the root object of property "' . $key . '"' );
					}

					switch ( $valueKey ) {

						case '$gt':
						case '$lt':
						case '$lte':
						case '$gte':
						case '$ne':
						case '$eq':
							$tokens[] = $this->encodeComparisionOperator( $key, $value[ $valueKey ], $valueKey );
							break;

						case '$in':
							$tokens[] = $this->encodeInOperator( $key, $value[ $valueKey ] );
							break;

						default:
							throw new \browserfs\Exception( 'Unknown operator ' . $valueKey );
							break;
					}

				}

				if ( count( $tokens ) > 1 ) {
				
					return '( ( ' . implode( ' ) AND ( ', $tokens ) . ' ) )';

				} else {

					return '( ' . $tokens[0] . ' )';

				}

			}

		}

		protected function encodeInOperator( $keyName, $dataSet ) {

			if ( !is_array( $dataSet ) ) {
				throw new \browserfs\Exception('$in operator expects a dataset of primitive values!' );
			}

			$out = '`' . $keyName . '` IN (';

			$sub = [];

			foreach ( $dataSet as $value ) {

				if ( $value === null ) {
					$sub[] = 'NULL';
				} else
				if ( is_string( $value ) ) {
					$sub[] = '"' . addslashes( $value ) . '"';
				} else
				if ( is_int( $value ) || is_float( $value ) ) {
					$sub[] = (string)$value;
				} else
				if ( is_bool( $value ) ) {
					$sub[] = (int)$value;
				} else
				{
					throw new \browserfs\Exception('Invalid data type inside $in operator!' );
				}

			}

			$out .= implode( ',', $sub );

			$out .= ')';

		}

		protected function encodeComparisionOperator( $keyName, $value, $operator ) {

			$result = '`' . $keyName . '` ';

			switch ( $operator ) {
				case '$gt':
					$result .= '> ';
					break;
				case '$gte':
					$result .= '>= ';
					break;
				case '$lt':
					$result .= '< ';
					break;
				case '$lte':
					$result .= '<= ';
					break;
				case '$eq':
					$result .= ( $value === null ? 'IS ' : '= ' );
					break;
				case '$ne':
				case '$neq':
					$result .= ( $value === null ? 'IS NOT ' : '<> ' );
					break;
				default:
					throw new \browserfs\Exception('Invalid comparision operator: ' . $operator );
					break;
			}

			if ( $value === NULL ) {
				$result .= 'NULL';
			} else
			if ( is_string( $value ) ) {
				$result .= '"' . addslashes( $value ) . '"';
			} else
			if ( is_int( $value ) || is_float( $value ) ) {
				$result .= (string)$value;
			} else
			if ( is_bool( $value ) ) {
				$result .= (int)$value;
			} else {
				throw new \browserfs\Exception( 'Invalid value type for operator ' . $operator );
			}

			return $result;

		}

	}
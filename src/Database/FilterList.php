<?php
	
	namespace browserfs\website\Database;

	class FilterList {

		const FLAG_SIMPLE_KEYS = 1;
		const FLAG_DOTTED_KEYS = 2;

		protected static $supportedOperators = [
			'in',
			'ne',
			'gt',
			'gte',
			'lt',
			'lte',
			'and',
			'or',
			'not'
		];

		protected $filter = [];

		public function __construct( $filter ) {

			if ( $filter === null ) {
				return;
			}

			if ( !is_array( $filter ) ) {
				throw new \browserfs\Exception('Invalid argument $filter: array expected' );
			}

			if ( count( $filter ) === 0 ) {
				return;
			}

			foreach ( $filter as $key => $value ) {
				$this->validate( $key, $value );
				$this->filter[ $key ] = $value;
			}

		}

		public function value() {
			return $this->filter;
		}

		protected function validate( $key, $value ) {

			switch ( true ) {

				case !is_string( $key ):
					throw new \browserfs\Exception('Invalid filter key ' . json_encode( $key ) . ': keys should be of type string!' );
					break;

				case $key === '':
					throw new \browserfs\Exception('Invalid filter key: empty filter keys are not allowed!' );
					break;

				case substr( $key, 0, 1 ) == '$':
					$this->testOperator( substr( $key, 1 ), $value );
					break;

				default:
					$this->testProperty( $key, $value );
					break;

			}

		}

		protected function testOperator( $operatorName, $operatorValue ) {

			if ( !is_string( $operatorName ) || !in_array( $operatorName, self::$supportedOperators ) ) {
				throw new \browserfs\Exception('Invalid operator ' . json_encode( $operatorName ) );
			}

			switch ( $operatorName ) {

				case 'in':
					$this->testOperatorIN( $operatorValue );
					break;

				case 'ne':
					$this->testOperatorNE( $operatorValue );
					break;

				case 'gt':
				case 'gte':
				case 'lt':
				case 'lte':
					$this->testComparisionOperator( $operatorValue, $operatorName );
					break;

				case 'or':
				case 'and':
					$this->testLogicalOperator( $operatorValue, $operatorName );
					break;

				case 'not':
					$this->testOperatorNOT( $operatorValue );
					break;

				default:
					throw new \browserfs\Exception( 'Unknown operator "' . $operatorName . '": Unimplemented' );
					break;

			}

		}

		protected function testOperatorIN( $operatorValue ) {
			
			if ( !$this->isIndexedArary( $operatorValue ) || !count( $operatorValue ) ) {
				throw new \browserfs\Exception('Invalid value supplied for $in operator: Expected indexed array with primitive values!' );
			}
			
			foreach ( $operatorValue as $value ) {
				if ( !$this->isPrimitive( $value ) ) {
					throw new \browserfs\Exception('Invalid value supplied for $in operator: Values must be primitives!' );
				}
			}

		}

		protected function testOperatorNE( $operatorValue ) {
			if ( !$this->isPrimitive( $operatorValue ) ) {
				throw new \browserfs\Exception('Invalid value supplied for $ne operator: Expected primitive value' );
			}
		}

		protected function testComparisionOperator( $operatorValue, $operatorName ) {
			if ( !$this->isPrimitive( $operatorValue ) || $operatorValue === null ) {
				throw new \browserfs\Exception('Invalid value supplied for $' . $operatorName . ' operator: Expected non-null primitive value' );
			}
		}

		protected function testLogicalOperator( $operatorValue, $operatorName ) {
			
			if ( !$this->isIndexedArary( $operatorValue ) || count( $operatorValue ) === 0 ) {
				throw new \browserfs\Exception('Invalid operator value $' . $operatorName . ': Expected array of expressions as value' );
			}

			foreach ( $operatorValue as $expression ) {
				if ( !$this->isExpression( $expression ) ) {
					throw new \browserfs\Exception('Invalid operator value $' . $operatorName . ': Expected array of expressions, and found a non-expression item' );
				}
			}

		}

		protected function testOperatorNOT( $operatorValue ) {

			if ( !$this->isExpression( $operatorValue ) ) {
				throw new \browsersf\Exception('Invalid $not operator value: Expected expression at value' );
			}

		}

		protected function testProperty( $propertyName, $propertyValue ) {

			if ( !$this->isPropertyName( $propertyName ) ) {
				throw new \browserfs\Exception( json_encode( $propertyName ) . ' is not a valid property name' );
			}

			if ( $this->isPrimitive( $propertyValue ) ) {
				return true;
			}

			// test if property value should contain only operators
			if ( $this->isObjectWithPropertyOperators( $propertyValue ) ) {
				return false;
			}

			foreach ( $propertyValue as $operatorName => $operatorValue ) {

				$this->testOperator( substr( $operatorName, 1 ), $operatorValue );

			} 

			return true;

		}

		protected function isPrimitive( $value ) {
			
			if ( $value === null || is_int( $value)  || is_string( $value ) || is_float( $value ) || is_bool( $value ) ) {
				
				return true;
			
			} else {
			
				return false;
			
			}
		}

		protected function isIndexedArray( $value ) {

			if ( is_array( $value ) ) {

				$len = count( $value );
				$key = 0;

				foreach ( $value as $k => $v ) {
					if ( $k != $key ) {
						return false;
					}
					$key++;
				}

				if ( $key != $len ) {
					return false;
				}

				return true;

			} else {

				return false;

			}

		}

		protected function isHashArray( $value ) {

			return is_array( $value ) && !$this->isIndexedArray( $value ) && count( $value ) > 0;

		}

		protected function isPropertyName( $value ) {

			return is_string( $value ) && preg_match( '/^[a-z_]([a-z_\\$0-9]+)?((\.[a-z_]([a-z_\\$0-9]+)?)+)?$/i', $value );

		}

		protected function isOperatorName( $propertyName ) {

			return is_string( $propertyName ) 
				&& strlen( $propertyName ) > 1 
				&& substr( $propertyName, 0, 1 ) == '$' 
				&& in_array( substr( $propertyName, 1 ), static::$supportedOperators );

		}

		protected function isObjectWithPropertyOperators( $value ) {
			if ( $this->isHashArray( $value ) ) {
				foreach ( $value as $operator => $value ) {
					if ( !$this->isOperatorName( $operator ) ) {
						return false;
					}
				}
				return true;
			} else {
				return false;
			}
		}

		protected function isExpression( $value ) {

			if ( !$this->isHashArray( $value ) ) {
				return false;
			}

			try {

				foreach ( $value as $key => $v ) {
					$this->validate( $key, $v );
				}

				return true;

			} catch ( \Exception $e ) {

				return false;
			
			}

		}

	}
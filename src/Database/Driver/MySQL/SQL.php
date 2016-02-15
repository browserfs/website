<?php

	namespace browserfs\website\Database\Driver\MySQL;

	class SQL {

		const SQL_MAX_LIMIT_ROWS = 4000000000; // 20m should be enough.

		public static function encodeFilterExpression( $expression, $database ) {

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
						
							$tokens[] = self::encodeLogicalExpression( $value, $key == '$or' ? 'OR' : 'AND', $database );
						
						} else {
						
							$tokens[] = self::encodeNegateExpression( $value, $database );
						
						}

						break;

					default:
						
						$tokens[] = self::encodePropertyValue( $value, $key, $database );
						
						break;

				}

			}

			return '( ' . implode( ' ) AND ( ', $tokens ) . ' )';

		}

		private static function encodeLogicalExpression( $value, $logicalOperator, $database ) {

			$tokens = [];

			if ( !is_array( $value ) || count( $value ) < 2 ) {
				throw new \browserfs\Exception('Invalid $' . strtolower( $logicalOperator ) . ' operator value: Expected non-empty array of expressions of minimum length = 2!' );
			}

			foreach ( $value as $expression ) {
				$tokens[] = self::encodeFilterExpression( $expression, $database );
			}

			return '( ' . implode( ' ' . $logicalOperator . ' ', $tokens ) . ' )';

		}

		private static function encodeNegateExpression( $value, $database ) {

			return 'NOT(' . self::encodeFilterExpression( $value, $database ) . ')';

		}

		private static function encodePropertyValue( $value, $key, $database ) {

			if ( $database->isEscapable( $value ) ) {

				return $database->escapeIdentifier( $key ) . ( $value === null ? ' IS NULL' : ' = ' . $database->escape( $value ) );
			
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
							$tokens[] = self::encodeComparisionOperator( $key, $value[ $valueKey ], $valueKey, $database );
							break;

						case '$in':
							$tokens[] = self::encodeInOperator( $key, $value[ $valueKey ], $database );
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

		private static function encodeInOperator( $keyName, $dataSet, $database ) {

			if ( !is_array( $dataSet ) ) {
				throw new \browserfs\Exception('$in operator expects a dataset of primitive values!' );
			}

			$out = $database->escapeIdentifier( $keyName ) . ' IN (';

			$sub = [];

			foreach ( $dataSet as $value ) {

				if ( $database->isEscapable( $value ) ) {
				
					$sub[] = $database->escape( $value );
				
				} else
				{
					throw new \browserfs\Exception('Invalid data type inside $in operator!' );
				}

			}

			$out .= implode( ',', $sub );

			$out .= ')';

			return $out;

		}

		private static function encodeComparisionOperator( $keyName, $value, $operator, $database ) {

			$result = $database->escapeIdentifier( $keyName ) . ' ';

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
					$result .= ( $value === null ? 'IS NOT ' : '<> ' );
					break;
				default:
					throw new \browserfs\Exception('Invalid comparision operator: ' . $operator );
					break;
			}

			if ( $database->isEscapable( $value ) ) {
			
				$result .= $database->escape( $value );
			
			} else {
			
				throw new \browserfs\Exception( 'Invalid value type for operator ' . $operator );
			
			}

			return $result;

		}

	}
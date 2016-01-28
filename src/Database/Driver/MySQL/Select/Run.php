<?php
	
	namespace browserfs\website\Database\Driver\MySQL\Select;

	class Run extends \browserfs\website\Database\Select\Run {
		
		public function __construct( 
			\browserfs\website\Database\Driver\MySQL\Table        $table, 
			\browserfs\website\Database\Driver\MySQL\Select       $select = null, 
			\browserfs\website\Database\Driver\MySQL\Select\Where $where  = null,
			\browserfs\website\Database\Driver\MySQL\Select\Skip  $skip   = null,
			\browserfs\website\Database\Driver\MySQL\Select\Limit $limit  = null
		) {

			parent::__construct( $table, $select, $where, $skip, $limit );
			
		}

		protected function exec() {

			// BUILD QUERY

			$parts = [ 'SELECT' ];

			if ( $this->select !== null ) {
				$parts[] = $this->select->toString();
			} else {
				$parts[] = '*';
			}

			$parts[] = 'FROM ' . $this->table->name();

			if ( $this->where !== null ) {
				$parts[] = 'WHERE ' . $this->where->toString();
			}

			// ORDER BY NOT IMPLEMENTED

			if ( $this->skip !== null ) {

				if ( $this->limit !== null ) {

					$parts[] = 'LIMIT ' . $this->skip->value() . ',' . $this->limit->value();

				} else {

					$parts[] = 'LIMIT ' . $this->skip->value() . ',1000000';

				}

			} else {

				if ( $this->limit !== null ) {

					$parts[] = 'LIMIT ' . $this->limit->value();

				}

			}

			$query = implode( ' ', $parts );

			echo "DEBUG: ", $query, "\n";

			return $query;

		}

	}
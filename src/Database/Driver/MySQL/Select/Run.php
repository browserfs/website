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

		public function exec() {

			// BUILD QUERY

			$parts = [ 'SELECT' ];

			if ( $this->select !== null ) {
				$parts[] = $this->select->toString();
			} else {
				$parts[] = '*';
			}

			$parts[] = 'FROM ' . $this->table->db()->escapeIdentifier( $this->table->name() );

			if ( $this->where !== null ) {
				$parts[] = 'WHERE ' . $this->where->toString();
			}

			// ORDER BY NOT IMPLEMENTED

			if ( $this->skip !== null ) {

				if ( $this->limit !== null ) {

					$parts[] = 'LIMIT ' . $this->skip->value() . ',' . $this->limit->value();

				} else {

					$parts[] = 'LIMIT ' . $this->skip->value() . ',1000000000';

				}

			} else {

				if ( $this->limit !== null ) {

					$parts[] = 'LIMIT ' . $this->limit->value();

				}

			}

			$query = implode( ' ', $parts ) . ';';

			// FIRES A NOTIFIER TO THE TABLE
			$this->table->fire( 'query', $query );

			// run query

			$this->table->db()->connect();

			$results = [];

			$stmt = $this->table->db()->getNativeDriver()->query( $query );

			if ( $this->select !== null && $this->select->isExceptFields() ) {

				while ( $row = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
					$this->select->removeFields( $row );
					$results[] = $row;
				}

			} else {

				while ( $row = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
					$results[] = $row;
				}

			}

			return new \browserfs\Collection( $results );

		}

	}
<?php

	namespace browserfs\website\Database\Driver\MySQL\Delete;

	class Run extends \browserfs\website\Database\Delete\Run {

		public function __construct(

			\browserfs\website\Database\Driver\MySQL\Table        $table, 
			\browserfs\website\Database\Driver\MySQL\Delete       $where  = null,
			\browserfs\website\Database\Driver\MySQL\Delete\Limit $limit  = null

		) {

			parent::__construct( $table, $where, $limit );

		}

		public function exec() {

			$parts = [ "DELETE FROM " . $this->table->db()->escapeIdentifier( $this->table->name() ) ];

			if ( $this->where !== null ) {
				$parts[] = $this->where->toString();
			}

			if ( $this->limit !== null ) {

				$parts[] = 'LIMIT ' . $this->limit->value();

			}

			$query = implode( ' ', $parts );

			// Fires a notifier to the table

			$this->table->fire( 'query', $query );

			$this->table->db()->connect();

			$pdo = $this->table->db()->getNativeDriver();

			try {

				$stmt = $pdo->query( $query );
			
			} catch ( \Exception $e ) {
			
				throw new \browserfs\Exception("Delete statement failed ( SQL = $query )!", 1, $e );
			
			}

			return $stmt->rowCount();

		}

	}
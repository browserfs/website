<?php
	
	namespace browserfs\website\Database\Driver\MySQL\Update;

	class Run extends \browserfs\website\Database\Update\Run {

		public function __construct(
			\browserfs\website\Database\Driver\MySQL\Table $table,
			\browserfs\website\Database\Driver\MySQL\Update $update,
			\browserfs\website\Database\Driver\MySQL\Update\Where $where = null,
			\browserfs\website\Database\Driver\MySQL\Update\Limit $limit = null
		) {
			parent::__construct( $table, $update, $where, $limit );
		}

		public function exec() {
			
			$sql = [ 
				"UPDATE " 
				. $this->table->db()->escapeIdentifier( $this->table->name() )
			];

			if ( $this->update !== null ) {

				$sql[] = 'SET ' . $this->update->toString();

			}

			if ( $this->where !== null ) {
				$sql[] = 'WHERE ' . $this->where->toString();
			}

			if ( $this->limit !== null ) {
				$sql[] = 'LIMIT ' . $this->limit->toString();
			}

			$query = implode( ' ', $sql );

			$this->table->fire( 'query', $query );

			$this->table->db()->connect();

			$pdo = $this->table->db()->getNativeDriver();

			try {

				$stmt = $pdo->query( $query );

			} catch ( \Exception $e ) {

				throw new \browserfs\Exception('Update statement failed ( SQL = ' . $query . ' ) ', 1, $e );

			}

			return $stmt->rowCount();


		}

	}
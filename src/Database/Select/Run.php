<?php

	namespace browserfs\website\Database\Select;

	abstract class Run implements \browserfs\website\Database\Select\IRun {

		protected $table  = null;
		protected $select = null;
		protected $where  = null;
		protected $limit  = null;
		protected $skip   = null;

		public function __construct(
			\browserfs\website\Database\Table        $table, 
			\browserfs\website\Database\Select       $select = null, 
			\browserfs\website\Database\Select\Where $where  = null,
			\browserfs\website\Database\Select\Skip  $skip   = null,
			\browserfs\website\Database\Select\Limit $limit  = null
		) {

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected instance of \\browserfs\\website\\Database\\Table' );
			}

			$this->table = $table;

			if ( null !== $select ) {

				if ( !( $select instanceof \browserfs\website\Database\Select ) ) {
					throw new \browserfs\Exception('Invalid argument $select: Expected instance of \\browserfs\\website\\Database\\Select' );
				}

				$this->select = $select;

			}

			if ( null !== $where ) {

				if ( !( $where instanceof \browserfs\website\Database\Select\Where ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Select\\Where' );
				}

				$this->where = $where;

			}

			if ( null !== $skip ) {

				if ( !( $skip instanceof \browserfs\website\Database\Select\Skip ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Select\\Skip' );
				}

				$this->skip = $skip;

			}

			if ( null !== $limit ) {

				if ( !( $limit instanceof \browserfs\website\Database\Select\Limit ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Select\\Limit' );
				}

				$this->limit = $limit;

			}

			$this->exec();
		
		}

		abstract protected function exec();

	}
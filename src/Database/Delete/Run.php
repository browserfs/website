<?php

	namespace browserfs\website\Database\Delete;

	abstract class Run implements \browserfs\website\Database\Delete\IRun {

		protected $table  = null;
		protected $where  = null;
		protected $limit  = null;
		protected $skip   = null;

		public function __construct(

			\browserfs\website\Database\Table        $table, 
			\browserfs\website\Database\Delete       $where  = null,
			\browserfs\website\Database\Delete\Limit $limit  = null

		) {

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected instance of \\browserfs\\website\\Database\\Table' );
			}

			$this->table = $table;

			if ( null !== $where ) {

				if ( !( $where instanceof \browserfs\website\Database\Delete ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Delete' );
				}

				$this->where = $where;

			}

			if ( null !== $limit ) {

				if ( !( $limit instanceof \browserfs\website\Database\Delete\Limit ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instance of \\browserfs\\website\\Database\\Delete\\Limit' );
				}

				$this->limit = $limit;

			}

		}

		abstract public function exec();

	}
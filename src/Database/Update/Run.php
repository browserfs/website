<?php

	namespace browserfs\website\Database\Update;

	abstract class Run implements IRun {

		/**
		 * @var \browserfs\website\Database\Table
		 */
		protected $table  = null;

		/**
		 * @var \browserfs\website\Database\Update
		 */
		protected $update = null;

		/**
		 * @var \browserfs\website\Database\Update\Where
		 */
		protected $where  = null;

		/**
		 * @var \browserfs\website\Database\Update\Limit
		 */
		protected $limit  = null;

		/**
		 * Constructor. Executes the logic of the UPDATE database statement.
		 */
		public function __construct(
			\browserfs\website\Database\Table $table,
			\browserfs\website\Database\Update $update,
			\browserfs\website\Database\Update\Where $where = null,
			\browserfs\website\Database\Update\Limit $limit = null
		) {

			if ( !( $table instanceof \browserfs\website\Database\Table ) ) {
				throw new \browserfs\Exception('Invalid argument $table: Expected instanceof \browserfs\website\Database\Table' );
			}

			$this->table = $table;

			if ( !( $update instanceof \browserfs\website\Database\Update ) ) {
				throw new \browserfs\Exception('Invalid argument $update: Expected instanceof \browserfs\website\Database\Update' );
			}

			$this->update = $update;

			if ( null !== $where ) {

				if ( !( $where instanceof \browserfs\website\Database\Update\Where ) ) {
					throw new \browserfs\Exception('Invalid argument $where: Expected instanceof \browserfs\website\Database\Update\Where | null' );
				}

				$this->where = $where;

			}

			if ( null !== $limit ) {

				if ( !( $limit instanceof \browserfs\website\Database\Update\Limit ) ) {
					throw new \browserfs\Exception('Invalid argument $limit: Expected instanceof \browserfs\website\Database\Update\Limit | null' );
				}

				$this->limit = $limit;

			}

		}

		abstract public function exec();

	}
<?php
	
	namespace browserfs\website\Cache;

	class NamespaceWrapper extends \browserfs\website\Cache
	{

		protected $namespace = null;
		protected $cache = null;

		public static function create( \browserfs\website\Cache &$cache, $namespaceName ) {

			if ( !is_string( $namespaceName ) ) {
				throw new \browserfs\Exception( 'Invalid argument: $namespaceName: string expected!' );
			}

			$result = new self( null, $cache->getName() . '_' . $namespaceName );

			$result->cache = $cache;
			$result->namespace = $namespaceName;

			return $result;

		}

		public function get( $key )
		{
			return $this->cache->get( $this->namespace . $key );
		}

		public function set( $key, $value, $ttl )
		{
			$this->cache->set( $this->namespace . $key, $value, $ttl );
		}

		public function delete( $key ) {
			$this->cache->delete( $this->namespace . $key );
		}

	}
<?php
	
	namespace browserfs\website\Cache\Driver;

	class Memory extends \browserfs\website\Cache
	{

		private $store = [];

		/**
		 * @param string $key
		 */
		public function get( $key )
		{
			
			if ( !is_string( $key ) || $key === '' ) {
				throw new \browserfs\Exception('Invalid argument $key - non-empty string expected!' );
			}

			if ( $this->has( $key ) ) {
				
				if ( !$this->expired( $key ) ) {
				
					return $this->store[ $key ]['value'];
				
				} else {
				
					unset( $this->store[ $key ] );
					
					return null;
				
				}

			} else {
				
				return null;
			
			}
		}

		/**
		 * @param string $key
		 * @param string $value
		 * @param int    $timeToLive
		 */
		public function set( $key, $value, $timeToLive )
		{
			if ( !is_int( $timeToLive ) ) {
				throw new \browserfs\Exception('Invalid argument $timeToLive: Int expected!' );
			}

			if ( !is_string( $key ) || $key === '' ) {
				throw new \browserfs\Exception('Invalid argument $key: non-empty string expected!' );
			}

			if ( !is_string( $value ) ) {
				throw new \browserfs\Exception('Invalid argument $value: string expected!' );
			}

			if ( $timeToLive < -1 ) {
				throw new \browserfs\Exception('Invalid argument $timeToLive: Expected values greater or equal with -1!' );
			}

			$this->store[ $key ] = [
				'time' => time(),
				'value' => $value,
				'ttl' => $timeToLive
			];
		}

		/**
		 * @param key: string
		 * @return void
		 * @throws \browserfs\Exception on invalid arguments
		 */
		public function delete( $key )
		{
			if ( !is_string( $key ) || $key === '' ) {
				throw new \browserfs\Exception('Invalid argument $key: Non-empty string expected!' );
			}

			if ( $this->has( $key ) ) {
				unset( $this->store[ $key ] );
			}
		}

		/**
		 * @return void
		 */
		public function clear() {
			$this->store = [];
		}

		/**
		 * @return boolean
		 */
		private function has( $key ) {
			return isset( $this->store[ $key ] );
		}

		/**
		 * @return boolean
		 */
		private function expired( $key ) {

			if ( isset( $this->store[ $key ] ) ) {

				if ( $this->store[ $key ]['ttl'] <= 0 ) {
					return false;
				}
				
				return ( time() - $this->store[ $key ][ 'time' ] ) >= 0;
			
			} else {

				return true;
			
			}

		}

	}
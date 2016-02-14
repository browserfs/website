<?php

	namespace browserfs\website\Service;

	interface IServiceInterface {
	
		/**
		 * A service is instantiated by a \browserfs\Config dependency injector. When
		 * the dependency injector is instantiatin the service, it automatically calls
		 * the setDIInjector method on the service, in order to make the service aware
		 * of it's instantiator.
		 *
		 * This is needed because the services which depends on other services, should
		 * be instantiated by the same dependency injector
		 */
		public function setDIInjector( \browserfs\website\Config $instantiator );
		
		/**
		 * Returns the dependency injector which instantiated this service. This is the
		 * same value passed by the setDIInjector as first argument.
		 */
		public function getDIInjector();
	}
<?php

	namespace browserfs\website\Service\Staging;

	class Production extends \browserfs\website\Service\Staging {

		public final function staging() {
			return 'production';
		}

	}
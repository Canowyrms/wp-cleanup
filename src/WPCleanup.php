<?php

namespace BK\WPCleanup;

use BK\WPCleanup\Modules\{
	CleanupModule,
	NiceSearchModule
};

class WPCleanup {
	/** @var WPCleanup Singleton instance */
	private static WPCleanup $instance;

	/** @var array Instances of modules */
	private array $modules = [];

	public function __construct () {
		if (isset(self::$instance)) {
			return;
		}

		self::$instance = $this;

		$this->loadModules();
	}

	private function loadModules () {
		$this->modules = [
			new CleanupModule(),
			new NiceSearchModule(),
		];
	}
}

<?php

namespace TariffFee;

use Jenssegers\Blade\Blade as Blade;

class Loader {
	protected $version;

	public $pluginBaseDir = null;
	public $pluginBaseUrl = null;

	public function __construct($pluginBaseDir, $pluginBaseUrl) {
		$this->pluginBaseDir = $pluginBaseDir;
		$this->pluginBaseUrl = $pluginBaseUrl;
		$this->version = '1.0.0';
	}

	public static function initBladeViews() {
		$views = __DIR__ . '/resources/pages';
		$cache = __DIR__ . '/cache';

		return new Blade($views, $cache);
	}

	public function init() {
		$this->initActions();
	}

	public function initActions() {
        //
	}

}
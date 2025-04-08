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
		add_action('woocommerce_product_options_advanced', [$this, 'displayCountryOfOriginField'], 10);
		add_action('woocommerce_process_product_meta', [$this, 'saveCountryOfOriginField'], 10, 1);
	}

	/**
	 * Display country of origin field on product
	 */
	public function displayCountryOfOriginField() {
		$blade = $GLOBALS['blade'];
		$post = $GLOBALS['post'];

		$selected_country = null;
		if (!empty($post->ID)) {
			$product = wc_get_product($post->ID);
			$selected_country = $product->get_meta('tariff_fee_country_of_origin');
		}

		$countries_obj = new \WC_Countries();
		$countries = $countries_obj->__get('countries');		

		echo $blade->render('components.country_dropdown', [
			'countries' => $countries,
			'selected_country' => $selected_country
		]);
	}

	/**
	 * Handle save country of origin field on products
	 */
	public function saveCountryOfOriginField($id) {
		if (isset($_POST['tariff_fee_country_of_origin'])) {
			update_post_meta($id, 'tariff_fee_country_of_origin', $_POST['tariff_fee_country_of_origin']);
		}
	}
}
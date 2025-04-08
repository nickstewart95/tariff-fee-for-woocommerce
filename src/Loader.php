<?php

namespace TariffFee;

use League\Csv\Reader;
use Jenssegers\Blade\Blade as Blade;

class Loader {
	protected $version;

	public $pluginBaseDir = null;
	public $pluginBaseUrl = null;
	public $tariffData = [];

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
		$this->initTariffData();
		$this->initActions();
	}

	public function initTariffData() {
		// Tariff fees are stored in a CSV file for easy updating
		try {
			$csv = Reader::createFromPath(__DIR__ . '/resources/data/tariff_fees.csv', 'r');
			$csv->setHeaderOffset(0);
			$records = $csv->getRecords();
			$this->tariffData = $records;
		} catch (\Exception $e) {
			//
		}
	}

	public function initActions() {
		add_action('woocommerce_product_options_advanced', [$this, 'displayCountryOfOriginField'], 10);
		add_action('woocommerce_process_product_meta', [$this, 'saveCountryOfOriginField'], 10, 1);
		add_action('woocommerce_cart_calculate_fees', [$this, 'addTariffFeeToCart'], 10, 1);
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

	/**
	 * Add tariff fee to the order
	 */
	public function addTariffFeeToCart($cart) {
		// Loop through cart items to calculate product tariff fees from country of origin
		$all_tarrif_fees = [];

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$product = $cart_item['data'];
			$country_of_origin = $product->get_meta('tariff_fee_country_of_origin', true);
			$tariff_percent = $this->findTariffFee($country_of_origin);			

			if ($tariff_percent == 0) {
				continue;
			}

			$tariff_percent = $tariff_percent / 100;			
			$tariff_fee = round($product->get_price() * $tariff_percent, 2);

			$all_tarrif_fees[$country_of_origin][] = $tariff_fee;
		}

		// Add all tariff fees to the cart
		foreach ($all_tarrif_fees as $country_of_origin => $tariff_fees) {
			$countries_obj = new \WC_Countries();
			$countries = $countries_obj->__get('countries');	
			$country_name = $countries[$country_of_origin];

			$cart->add_fee('Tariff Fee: ' . $country_name, array_sum($tariff_fees));
		}
	}

	/**
	 * Find tariff fee for a country
	 */
	public function findTariffFee($country_code): float {
		$tariffs = $this->tariffData;
		$tariff_fee = 0;

		if (empty($tariffs)) {
			return $tariff_fee;
		}

		foreach ($tariffs as $tariff) {
			if ($tariff['country'] === $country_code) {
				$tariff_fee = $tariff['fee'];
				break;
			}
		}

		return $tariff_fee;
	}
}
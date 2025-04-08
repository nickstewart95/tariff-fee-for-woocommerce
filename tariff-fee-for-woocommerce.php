<?php
/**
 * @wordpress-plugin
 * Plugin Name: Tariff Fee for WooCommerce
 * Description: Show a tariff fee for Woocommerce
 * Version: 1.0.0
 * Author: Nick Stewart
 * Author URI: https://nickstewart.me
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

if (!defined('WPINC')) {
	die();
}

require_once __DIR__ . '/vendor/autoload.php';

use TariffFee\Loader;

$plugin = new Loader(__DIR__, plugin_dir_url(__FILE__));
$plugin->init();

$GLOBALS['blade'] = Loader::initBladeViews();

/**
 * The tariff fee is included in the tax calculation by default
 * See https://www.wipfli.com/insights/articles/tax-taxes-and-tariffs-an-overview-of-sales-tax-implications
 **/
register_activation_hook( __FILE__, function() {
    update_option('tariff_fee_sales_tax', 'yes');
});

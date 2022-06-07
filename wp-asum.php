<?php
/*
Plugin Name: WP Another SpeedUp Module (ASUM)
Description: Полезные мелочи для ускорения wordpress-сайтов
Version: 1.0
Author: HDDen
Author URI: https://github.com/hdden
*/

//use function DOMProcessor\startProcessing as processHtml;

if(!is_admin()) {
    require_once WP_PLUGIN_DIR . '/wp-asum/vendor/autoload.php';
    require_once WP_PLUGIN_DIR . '/wp-asum/libs/logger/logger.php';
    require_once WP_PLUGIN_DIR . '/wp-asum/libs/domprocessor/domprocessor.php';
	add_action( 'setup_theme', 'wpasum_process_frontend' );
}

function wpasum_process_frontend() {
	// цепляемся к хуку
	if(!is_admin()) {
	    add_action('template_redirect', 'wpasum_min_html_compression_start', (PHP_INT_MAX - 10));
	}
}

// вызывается при сбросе или очистке
function wpasum_min_html_compression_finish($html) {
	return DOMProcessor\startProcessing($html);  // цепляемся к обработчику с DiDOM
}
// включаем буферизацию вывода
function wpasum_min_html_compression_start() {
	ob_start('wpasum_min_html_compression_finish');
}

?>
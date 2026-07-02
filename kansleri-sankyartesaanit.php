<?php
/**
 * Plugin Name: Kansleri & Sänkyartesaanit
 * Plugin URI: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat
 * Description: Kampanjat-laskeutumissivut Sänkyartesaanit-lohkoilla. Luo kampanja → kaikki lohkot latautuvat automaattisesti.
 * Version: 1.0.2
 * Author: Kansleri
 * Text Domain: kansleri-sankyartesaanit
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

define( 'KANSLERI_SA_VERSION', '1.0.2' );
define( 'KANSLERI_SA_PLUGIN_FILE', __FILE__ );
define( 'KANSLERI_SA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KANSLERI_SA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once KANSLERI_SA_PLUGIN_DIR . 'includes/helpers.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/landing-template.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/class-attribute-normalizer.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/class-blocks.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/class-cpt-kampanjat.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/class-prompt-schema.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/class-updater.php';
require_once KANSLERI_SA_PLUGIN_DIR . 'includes/patterns.php';

Kansleri_Sankyartesaanit_Blocks::init();
Kansleri_SA_CPT_Kampanjat::init();
Kansleri_SA_Updater::init();

register_activation_hook( KANSLERI_SA_PLUGIN_FILE, array( 'Kansleri_SA_CPT_Kampanjat', 'activate' ) );
register_deactivation_hook( KANSLERI_SA_PLUGIN_FILE, 'flush_rewrite_rules' );

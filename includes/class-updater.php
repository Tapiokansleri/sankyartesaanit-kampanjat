<?php
/**
 * GitHub release updates for the plugin.
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_SA_Updater {

	const GITHUB_REPO = 'Tapiokansleri/sankyartesaanit-kampanjat';

	/**
	 * Register the update checker.
	 */
	public static function init() {
		if ( ! is_admin() ) {
			return;
		}

		$bootstrap = KANSLERI_SA_PLUGIN_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';

		if ( ! file_exists( $bootstrap ) ) {
			return;
		}

		require_once $bootstrap;

		$checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			'https://github.com/' . self::GITHUB_REPO . '/',
			KANSLERI_SA_PLUGIN_FILE,
			'kansleri-sankyartesaanit'
		);

		$checker->setBranch( 'main' );
		$checker->getVcsApi()->enableReleaseAssets(
			'/^kansleri-sankyartesaanit\.zip$/i'
		);
	}
}

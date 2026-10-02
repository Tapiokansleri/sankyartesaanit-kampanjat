<?php
/**
 * WP-CLI commands.
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Manage the default landing page used for new Kampanjat.
 */
class Kansleri_SA_CLI {

	/**
	 * Save the blocks of a page as the default landing page template.
	 *
	 * Media in the page (images, video, logos) is replaced with tokens, so the template follows the
	 * media library of whichever site it is used on. Run this on the site where the page was built,
	 * then commit the written file (templates/landing-default.html).
	 *
	 * ## OPTIONS
	 *
	 * <post_id>
	 * : ID of the page whose blocks become the template.
	 *
	 * [--output=<file>]
	 * : Write to this file instead of the active plugin's templates/landing-default.html.
	 *
	 * [--dry-run]
	 * : Show what would be written without writing anything.
	 *
	 * ## EXAMPLES
	 *
	 *     wp kansleri-sa export-template 4020
	 *     wp kansleri-sa export-template 4020 --output=/path/to/repo/templates/landing-default.html
	 *
	 * @subcommand export-template
	 *
	 * @param string[]             $args       Positional arguments.
	 * @param array<string, mixed> $assoc_args Flags.
	 */
	public function export_template( $args, $assoc_args ) {
		$post = get_post( (int) $args[0] );

		if ( ! $post || '' === trim( $post->post_content ) ) {
			WP_CLI::error( 'That post does not exist or has no content.' );
		}

		$result = Kansleri_SA_Landing_Template::tokenize( $post->post_content );
		$markup = trim( str_replace( "\r\n", "\n", $result['markup'] ) ) . "\n";
		$file   = isset( $assoc_args['output'] ) ? $assoc_args['output'] : KANSLERI_SA_PLUGIN_DIR . Kansleri_SA_Landing_Template::TEMPLATE_FILE;

		WP_CLI::log( sprintf( '%d media files referenced:', count( $result['media'] ) ) );
		foreach ( $result['media'] as $rel ) {
			WP_CLI::log( '  ' . $rel );
		}

		foreach ( $result['warnings'] as $warning ) {
			WP_CLI::warning( $warning );
		}

		if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', false ) ) {
			WP_CLI::success( sprintf( 'Dry run: %d bytes would be written to %s', strlen( $markup ), $file ) );
			return;
		}

		if ( false === file_put_contents( $file, $markup ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			WP_CLI::error( 'Could not write ' . $file );
		}

		WP_CLI::success( sprintf( 'Wrote %d bytes to %s', strlen( $markup ), $file ) );
	}
}

WP_CLI::add_command( 'kansleri-sa', 'Kansleri_SA_CLI' );

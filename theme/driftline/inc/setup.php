<?php
/**
 * One-time demo installer: imports the bundled photos, creates the pages and menus,
 * and applies safe site settings. Runs once per DL_SETUP version.
 *
 * @package driftline
 */

defined( 'ABSPATH' ) || exit;

define( 'DL_SETUP', '8' );

add_action(
	'init',
	function () {
		if ( get_option( 'dl_setup' ) === DL_SETUP || get_transient( 'dl_setup_lock' ) || wp_doing_ajax() ) {
			return;
		}
		set_transient( 'dl_setup_lock', 1, 5 * MINUTE_IN_SECONDS );
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 300 ); // phpcs:ignore
		}
		dl_setup_media();
		dl_setup_pages();
		dl_setup_menus();
		dl_setup_options();
		update_option( 'dl_setup', DL_SETUP );
		delete_transient( 'dl_setup_lock' );
	},
	20
);

function dl_setup_media() {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	$map = get_option( 'dl_media', array() );
	foreach ( dl_images() as $key => $img ) {
		if ( ! empty( $map[ $key ] ) && get_post( $map[ $key ] ) ) {
			continue;
		}
		$src = DL_DIR . '/assets/img/' . $img[0];
		if ( ! file_exists( $src ) ) {
			continue;
		}
		$upload = wp_upload_bits( 'driftline-' . $img[0], null, file_get_contents( $src ) ); // phpcs:ignore
		if ( ! empty( $upload['error'] ) ) {
			continue;
		}
		$id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/webp',
				'post_title'     => $img[1],
				'post_status'    => 'inherit',
			),
			$upload['file']
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		update_post_meta( $id, '_wp_attachment_image_alt', $img[1] );
		$map[ $key ] = $id;
		update_option( 'dl_media', $map );
	}
	update_option( 'dl_og_image', dl_img_url( 'home-automotive', 'large' ) );
}

function dl_setup_pages() {
	foreach ( dl_pages() as $slug => $page ) {
		$content  = call_user_func( $page[3] );
		$existing = get_page_by_path( $slug );
		$data     = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_name'      => $slug,
			'post_title'     => $page[0],
			'post_content'   => wp_slash( $content ),
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		if ( $existing ) {
			// Leave pages alone once someone has edited them in the editor.
			$seed = get_post_meta( $existing->ID, '_dl_seed', true );
			if ( $seed && md5( $existing->post_content ) !== $seed ) {
				continue;
			}
			$data['ID'] = $existing->ID;
			$id         = wp_update_post( $data );
		} else {
			$id = wp_insert_post( $data );
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_dl_seed', md5( get_post( $id )->post_content ) );
			update_post_meta( $id, '_dl_title', $page[1] );
			update_post_meta( $id, '_dl_desc', $page[2] );
		}
	}
	$home = get_page_by_path( 'home' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}
	// Remove the default WordPress sample content.
	foreach ( array( 'sample-page' => 'page', 'hello-world' => 'post', 'privacy-policy' => 'page' ) as $slug => $type ) {
		$p = get_page_by_path( $slug, OBJECT, $type );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
		}
	}
}

function dl_setup_menus() {
	$menus = array(
		'header-left'  => array( 'Header left', array( array( 'Automotive', '/automotive/' ), array( 'Surfboards', '/surfboards/' ), array( 'Course', '/shaping-course/' ) ) ),
		'header-right' => array( 'Header right', array( array( 'Shop', '/shop/' ), array( 'About', '/about/' ), array( 'Contact/Order', '/about/#contact' ) ) ),
		'footer'       => array( 'Footer', array( array( 'Automotive', '/automotive/' ), array( 'Surfboards', '/surfboards/' ), array( 'Shaping course', '/shaping-course/' ), array( 'Shop', '/shop/' ), array( 'About', '/about/' ), array( 'Contact/Order', '/about/#contact' ) ) ),
	);
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $menu ) {
		$obj = wp_get_nav_menu_object( $menu[0] );
		if ( $obj ) {
			$locations[ $location ] = $obj->term_id;
			continue;
		}
		$menu_id = wp_create_nav_menu( $menu[0] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		foreach ( $menu[1] as $item ) {
			$args = array(
				'menu-item-title'  => $item[0],
				'menu-item-status' => 'publish',
			);
			$page = false === strpos( $item[1], '#' ) ? get_page_by_path( trim( $item[1], '/' ) ) : null;
			if ( $page ) {
				$args['menu-item-type']      = 'post_type';
				$args['menu-item-object']    = 'page';
				$args['menu-item-object-id'] = $page->ID;
			} else {
				$args['menu-item-type'] = 'custom';
				$args['menu-item-url']  = home_url( $item[1] );
			}
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}

function dl_setup_options() {
	update_option( 'blogname', 'Driftline Workshop' );
	update_option( 'blogdescription', 'Custom automotive builds and handmade surfboards. A placeholder brand for a concept site.' );
	update_option( 'blog_public', '0' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'users_can_register', 0 );

	if ( get_option( 'permalink_structure' ) !== '/%postname%/' ) {
		global $wp_rewrite;
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		flush_rewrite_rules( true );
	}

	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( 'localhost' !== $host ) {
		// Serve everything over HTTPS once the certificate is active.
		if ( is_ssl() ) {
			foreach ( array( 'home', 'siteurl' ) as $opt ) {
				$val = get_option( $opt );
				if ( 0 === strpos( $val, 'http://' ) ) {
					update_option( $opt, 'https://' . substr( $val, 7 ) );
				}
			}
		}
		// Replace the install-time admin password with a random one nobody has seen.
		// The site owner sets their own through the "Lost your password?" email link.
		if ( ! get_option( 'dl_pw_rotated' ) ) {
			foreach ( get_users( array( 'role' => 'administrator', 'fields' => 'ID' ) ) as $uid ) {
				wp_set_password( wp_generate_password( 40, true, true ), $uid );
			}
			update_option( 'dl_pw_rotated', gmdate( 'c' ), false );
		}
	}
}

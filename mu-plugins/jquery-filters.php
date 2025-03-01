<?php
/**
 * Plugin Name: jQuery Filters
 * Description: Default filters for all jQuery sites.
 */

if ( defined( 'WP_INSTALLING' ) )
	return;

$options = jquery_default_site_options();
$sites = jquery_sites();
$options = array_merge( $options, $sites[ JQUERY_LIVE_SITE ]['options'] );
foreach ( $options as $option => $value ) {
	if ( 'stylesheet' === $option || 'template' === $option )
		continue; // Don't mess with themes for now.
	add_filter( 'pre_option_' . $option, function( $null ) use ( $value, $blog_id ) {
		if ( $blog_id == get_current_blog_id() )
			return $value;
		return $null;
	} );
}
unset( $sites, $options, $option );

// Disable WordPress auto-paragraphing for posts.
remove_filter( 'the_content', 'wpautop' );

// Disable WordPress text transformations (smart quotes, etc.) for posts.
remove_filter( 'the_content', 'wptexturize' );

// Disable more restrictive multisite upload settings.
remove_filter( 'upload_mimes', 'check_upload_mimes' );
// Give unfiltered upload ability to super admins.
define( 'ALLOW_UNFILTERED_UPLOADS', true );
// Until unfiltered uploads make it into XML-RPC:
add_filter( 'upload_mimes', function( $mimes ) {
	$mimes['eot'] = 'application/vnd.ms-fontobject';
	$mimes['svg'] = 'image/svg+xml';
	$mimes['ttf'] = 'application/x-font-ttf';
	$mimes['woff'] = 'application/font-woff';
	$mimes['xml'] = 'text/xml';
	$mimes['php'] = 'application/x-php';
	$mimes['json'] = 'application/json';
	return $mimes;
} );

// Increase file size limit to 1GB
add_filter( 'pre_site_option_fileupload_maxk', function() {
	return 1024 * 1024;
} );

// Allow full HTML in term descriptions.
add_action( 'init', 'jquery_unfiltered_html_for_term_descriptions' );
add_action( 'set_current_user', 'jquery_unfiltered_html_for_term_descriptions' );
function jquery_unfiltered_html_for_term_descriptions() {
	remove_filter( 'pre_term_description', 'wp_filter_kses' );
	remove_filter( 'pre_term_description', 'wp_filter_post_kses' );
	if ( ! current_user_can( 'unfiltered_html' ) )
		add_filter( 'pre_term_description', 'wp_filter_post_kses' );
}

// Bypass multisite checks.
add_filter( 'ms_site_check', '__return_true' );

// Add body classes found in postmeta.
add_filter( 'body_class', function( $classes ) {
	$body_class_setting = get_option( 'jquery_body_class' );
	if ( $body_class_setting ) {
		array_unshift( $classes, $body_class_setting );
	}
	if ( strpos( JQUERY_LIVE_SITE, 'api.' ) === 0 ) {
		array_unshift( $classes, 'api' );
	}

	if ( is_page() ) {
		$classes[] = 'page-slug-' . sanitize_html_class( strtolower( get_queried_object()->post_name ) );
	}
	if ( is_singular() && $post_classes = get_post_meta( get_queried_object_id(), 'body_class', true ) ) {
		$classes = array_merge( $classes, explode( ' ', $post_classes ) );
	}
	if ( is_archive() || is_search() ) {
		$classes[] = 'listing';
	}

	return $classes;
});

add_filter( 'option_uploads_use_yearmonth_folders', '__return_false' );
add_filter( 'upload_dir', function( $upload_dir ) {
	if ( defined( 'UPLOADS' ) ) {
		$upload_dir['path'] = $upload_dir['basedir'] = UPLOADS;
	} else {
		$upload_dir['path'] = $upload_dir['basedir'] = WP_CONTENT_DIR . '/uploads';
	}

	return $upload_dir;
});

add_filter( 'get_terms', function( $terms, $taxonomies, $args ) {
	if ( !isset( $args[ 'orderby' ] ) || $args[ 'orderby' ] !== 'natural' ) {
		return $terms;
	}

	$sortedTerms = array();
	foreach( $terms as $term ) {
		$sortedTerms[ $term->name ] = $term;
	}
	uksort( $sortedTerms, 'strnatcasecmp' );

	if ( strtolower( $args[ 'order' ] ) === 'desc' ) {
		$sortedTerms = array_reverse( $sortedTerms );
	}

	return $sortedTerms;
}, 20, 3 );

// Strip protocol from urls making them protocol agnostic.
add_filter( 'theme_root_uri', 'strip_https', 10, 1 );
add_filter( 'clean_url', 'strip_https', 11, 1 );
function strip_https($url) {
	// WordPress core updates need a protocol.
	if ( 'downloads.wordpress.org' === parse_url( $url, PHP_URL_HOST ) ) {
		return $url;
	}

	return preg_replace( '/^https?:/', '', $url );
}

// Production databases set the home values in corresponding site options tables.
// However, sites that use jquery-static-index.php cause index pages
// to redirect to live sites in local development. This filter does not
// prevent the redirect, but changes the redirect to the local site.
if (JQUERY_STAGING && JQUERY_STAGING_PREFIX && JQUERY_LIVE_SITE) {
	add_filter( 'option_home', function( $value ) {
		return str_replace( '//' . JQUERY_LIVE_SITE, '//' . JQUERY_STAGING_PREFIX . JQUERY_LIVE_SITE, $value );
	} );
}

add_filter( 'xmlrpc_wp_insert_post_data', function ( $post_data, $content_struct ) {
	if ( $post_data['post_type'] !== 'page' ) {
		return $post_data;
	}

	if ( isset( $content_struct['page_template'] ) ) {
		$post_data['page_template'] = $content_struct['page_template'];
	}

	if ( isset( $content_struct['menu_order'] ) ) {
		$post_data['menu_order'] = $content_struct['menu_order'];
	}

	return $post_data;
}, /* priority */ 10, /* accepted args */ 2 );

<?php
/**
 * Plugin Name:       Block Banner Plugin
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-banner-plugin
 *
 * @package           block_banner_plugin
 */

namespace wpdev\block_banner_plugin;

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_block_banner_plugin_block_init() {
	register_block_type( __DIR__ . '/build/close-button' );
}
add_action( 'init', __NAMESPACE__ . '\create_block_block_banner_plugin_block_init' );





/**
 * Enqueue block editor script.
 *
 * @return void
 */
function enqueue_block_editor_script() {
	$asset_file = include plugin_dir_path( __FILE__ ) . 'build/editor/index.asset.php';

	wp_enqueue_script(
		'wpdev-banner-post-settings-field',
		plugins_url( 'build/editor/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_script' );




/**
 * Enqueue the frontend script.
 */
function register_frontend_script() {
	$asset_file = include plugin_dir_path( __FILE__ ) . 'build/frontend/index.asset.php';

	wp_register_script(
		'wpdev-banner-frontend',
		plugins_url( 'build/frontend/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_frontend_script' );



/**
 * Register the custom post type for the banner.
 */
function register_wpdev_banner_post_type() {

	$pattern_template = apply_filters(
		'block_banner_plugin_pattern_template',
		array(
			array(
				'core/group',
				array(
					'align'  => 'full',
					'layout' => array(
						'type'           => 'flex',
						'flexWrap'       => 'nowrap',
						'justifyContent' => 'space-between',
					),
					'style'  => array(
						'spacing' => array(
							'top'     => 'var:preset|spacing|xs',
							'bottom'  => 'var:preset|spacing|xs',
							'left'    => 'var:preset|spacing|xs',
							'right'   => 'var:preset|spacing|xs',
							'padding' => array(
								'right'  => 'var:preset|spacing|xs',
								'left'   => 'var:preset|spacing|xs',
								'top'    => 'var:preset|spacing|xs',
								'bottom' => 'var:preset|spacing|xs',
							),
						),
					),
				),
				array(
					array(
						'core/paragraph',
						array(
							'placeholder' => 'Add your banner content here',
						),
					),
					array( 'block-banner-plugin/close-button', array() ),
				),
			),
		),
	);

	$labels = array(
		'name'               => 'Banners',
		'singular_name'      => 'Banner',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Banner',
		'edit_item'          => 'Edit Banner',
		'new_item'           => 'New Banner',
		'all_items'          => 'All Banners',
		'view_item'          => 'View Banner',
		'search_items'       => 'Search Banners',
		'not_found'          => 'No Banners Found',
		'not_found_in_trash' => 'No Banners found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Banners',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'show_in_rest'       => true,
		'supports'           => array( 'title', 'editor', 'custom-fields' ),
		'menu_icon'          => 'dashicons-align-wide',
		'template_lock'      => 'all',
		'template'           => $pattern_template,
	);

	register_post_type( 'wpdev_banner', $args );

	// Register Post Meta.
	register_post_meta(
		'wpdev_banner',
		'wpdev_banner_show_on',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);

	register_post_meta(
		'wpdev_banner',
		'wpdev_banner_cookie_expiration',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);
}
add_action( 'init', __NAMESPACE__ . '\register_wpdev_banner_post_type' );



/**
 * Load the banner on the frontend.
 */
function load_banner_on_frontend() {

	$banner_query = wp_cache_get( 'wpdev_banner_query', 'wpdev_banner' );
	if ( ! $banner_query ) {
		$banner_query = new \WP_Query(
			array(
				'post_type'      => 'wpdev_banner',
				'posts_per_page' => 1,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => 'wpdev_banner_show_on',
						'value'   => 'all',
						'compare' => '=',
					),
				),
			)
		);
		wp_cache_set( 'wpdev_banner_query', $banner_query, 'wpdev_banner', 60 * 60 );
	}

	if ( $banner_query->have_posts() ) {
		while ( $banner_query->have_posts() ) {
			$banner_query->the_post();
			$banner            = get_the_content();
			$banner            = apply_filters( 'the_content', $banner );
			$banner            = str_replace( ']]>', ']]&gt;', $banner );
			$banner            = do_blocks( $banner );
			$cookie_name       = 'wpdev_banner_' . get_the_ID();
			$cookie_expiration = get_post_meta( get_the_ID(), 'wpdev_banner_cookie_expiration', true );
		}
		wp_reset_postdata();
	}

	if ( $banner ) {
			wp_enqueue_script( 'wpdev-banner-frontend' );
			echo '<aside class="block-banner-plugin" data-banner-cookie-name="' . esc_attr( $cookie_name ) . '" data-banner-cookie-expiration="' . esc_attr( $cookie_expiration ) . '" style="display:none">';
			echo $banner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</aside>';
	}
}
add_action( 'wp_footer', __NAMESPACE__ . '\load_banner_on_frontend' );


/**
 * Remove the close button block from the allowed block types for other post types.
 *
 * @param array  $allowed_blocks Array of allowed block types.
 * @param object $editor_context The editor context.
 * @return array
 */
function remove_button_allowed_block_types( $allowed_blocks, $editor_context ) {
	if ( 'wpdev_banner' !== $editor_context->post->post_type ) {
		unset( $allowed_blocks['block-banner-plugin/close-button'] );
	}
	return $allowed_blocks;
}
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\remove_button_allowed_block_types', 25, 2 );


/**
 * Add a column to the banner list table.
 *
 * @param array $columns Array of columns.
 * @return array
 */
function add_column_to_banner_list( $columns ) {
	$columns['show_on'] = 'Show On';
	return $columns;
}
add_filter( 'manage_wpdev_banner_posts_columns', __NAMESPACE__ . '\add_column_to_banner_list' );


/**
 * Show the content of the custom column.
 *
 * @param string $column_name Name of the column.
 * @param int    $post_id Post ID.
 */
function show_column_content( $column_name, $post_id ) {
	if ( 'show_on' === $column_name ) {
		$show_on = get_post_meta( $post_id, 'wpdev_banner_show_on', true );
		switch ( $show_on ) {
			case 'all':
				$show_on = 'All Pages';
				break;
		}
		echo esc_html( $show_on );
	}
}
add_action( 'manage_wpdev_banner_posts_custom_column', __NAMESPACE__ . '\show_column_content', 10, 2 );



/**
 * Flush the cache when the banner is saved.
 */
function flush_cache_on_save() {
	wp_cache_delete( 'wpdev_banner', 'wpdev_banner' );
}
add_action( 'save_post_wpdev_banner', __NAMESPACE__ . '\flush_cache_on_save' );

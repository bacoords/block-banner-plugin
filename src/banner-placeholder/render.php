<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */


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
?>
<div
<?php
echo get_block_wrapper_attributes(
	array(
		'data-banner-cookie-name'       => esc_attr( $cookie_name ),
		'data-banner-cookie-expiration' => esc_attr( $cookie_expiration ),
		'style'                         => 'display:none;',
	)
);
?>
>
	<?php echo $banner; // phpcs:ignore ?>
</div>

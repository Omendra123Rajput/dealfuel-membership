<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

@ini_set( 'upload_max_size', '164M' );
@ini_set( 'post_max_size', '164M' );
@ini_set( 'max_execution_time', '300' );

if (session_status() == PHP_SESSION_NONE) {
	session_start();
 }

if ( ! function_exists( 'chld_thm_cfg_locale_css' ) ) :
	function chld_thm_cfg_locale_css( $uri ) {
		if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) ) {
			$uri = get_template_directory_uri() . '/rtl.css';
		}
		return $uri;
	}
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( ! function_exists( 'child_theme_configurator_css' ) ) :
	function child_theme_configurator_css() {
		wp_enqueue_style( 'astra_child_css', get_stylesheet_directory_uri() . '/style.css', array() );

		wp_enqueue_style( 'slickcss', get_stylesheet_directory_uri() . '/css/slick.css', '1.6.0', 'all');
		wp_enqueue_style( 'slickcsstheme', get_stylesheet_directory_uri(). '/css/slick-theme.css', '1.6.0', 'all');

	}
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

function df_js_scripts(){

	wp_enqueue_script( 'slickjs', get_stylesheet_directory_uri() . '/js/slick.min.js', array( 'jquery' ), '1.6.0', true );
		wp_enqueue_script( 'slickjs-init', get_stylesheet_directory_uri(). '/js/slick.js', array( 'slickjs' ), '1.6.0', true );

}
add_action( 'wp_enqueue_scripts', 'df_js_scripts',15 );

// END ENQUEUE PARENT ACTION

add_filter( 'woocommerce_checkout_fields', 'woo_remove_billing_checkout_fields' );
/**
 * Remove unwanted checkout fields
 *
 * @return $fields array
 */
function woo_remove_billing_checkout_fields( $fields ) {
	// if( woo_cart_has_virtual_product() == true ) {
		unset( $fields['billing']['billing_company'] );
		unset( $fields['billing']['billing_address_1'] );
		unset( $fields['billing']['billing_address_2'] );
		unset( $fields['billing']['billing_city'] );
		unset( $fields['billing']['billing_postcode'] );
		unset( $fields['billing']['billing_state'] );
		unset( $fields['billing']['billing_phone'] );
		unset( $fields['order']['order_comments'] );
		unset( $fields['billing']['billing_address_2'] );
		unset( $fields['billing']['billing_postcode'] );
		unset( $fields['billing']['billing_company'] );
		unset( $fields['billing']['billing_city'] );

	// }
	return $fields;
}

// remove product description from single product pages
/*
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );          setting available in astra customizer settings
function woocommerce_template_single_excerpt() {
		return;
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );            setting available in astra customizer settings
function woocommerce_template_single_meta() {
		return;
}
*/


if ( ! function_exists( 'getCurrentPagePagination' ) ) {
	function getCurrentPagePagination( $args = '' ) {
		global $wp_query, $wp_rewrite;
		// Setting up default values based on the current URL.
		$array['pagenum_link'] = html_entity_decode( get_pagenum_link() );
		$array['url_parts']    = explode( '?', $array['pagenum_link'] );

		// Title argument
		$array['title'] = $args['title_arg'];

		// Get max pages and current page out of the current query, if available.
		$array['total']   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		$array['current'] = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

		return $array;
	}
}


if ( ! function_exists( 'custom_paginate_links' ) ) :
	function custom_paginate_links( $args = '' ) {
		global $wp_query, $wp_rewrite;

		// Setting up default values based on the current URL.
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$url_parts    = explode( '?', $pagenum_link );

		// Title argument
		$title = $args['title_arg'];

		// Get max pages and current page out of the current query, if available.
		$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

		// Append the format placeholder to the base URL.
		$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

		// URL base depends on permalink settings.
		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$defaults = array(
			'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
			'format'             => $format, // ?page=%#% : %#% is replaced by the page number
			'total'              => $total,
			'current'            => $current,
			'show_all'           => false,
			'prev_next'          => true,
			'prev_text'          => __( '&laquo; Previous' ),
			'next_text'          => __( 'Next &raquo;' ),
			'end_size'           => 1,
			'mid_size'           => 2,
			'type'               => 'plain',
			'add_args'           => array(), // array of query args to add
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! is_array( $args['add_args'] ) ) {
			$args['add_args'] = array();
		}

		// Merge additional query vars found in the original URL into 'add_args' array.
		if ( isset( $url_parts[1] ) ) {
			// Find the format argument.
			$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
			$format_query = isset( $format[1] ) ? $format[1] : '';
			wp_parse_str( $format_query, $format_args );

			// Find the query args of the requested URL.
			wp_parse_str( $url_parts[1], $url_query_args );

			// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
			foreach ( $format_args as $format_arg => $format_arg_value ) {
				unset( $url_query_args[ $format_arg ] );
			}

			$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
		}

		// Who knows what else people pass in $args
		$total = (int) $args['total'];
		if ( $total < 2 ) {
			return;
		}
		$current  = (int) $args['current'];
		$end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
		if ( $end_size < 1 ) {
			$end_size = 1;
		}
		$mid_size = (int) $args['mid_size'];
		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}
		$add_args   = $args['add_args'];
		$r          = '';
		$page_links = array();
		$dots       = false;

		if ( $args['prev_next'] && $current && 1 < $current ) :
			$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current - 1, $link );
			if ( $add_args ) {
				$link = add_query_arg( $add_args, $link );
			}
			$link .= $args['add_fragment'];

			/**
			 * Filters the paginated links for the given archive pages.
			 *
			 * @since 3.0.0
			 *
			 * @param string $link The paginated link URL.
			 */
			$page_no_prev = $current - 1;
			$page_links[] = '<a class="prev page-numbers" rel="prev" title="Previous Page: ' . $title . " Page $page_no_prev of $total" . '" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
		endif;
		for ( $n = 1; $n <= $total; $n++ ) :
			if ( $n == $current ) :
				$page_links[] = "<span class='page-numbers current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . '</span>';
				$dots         = true;
			else :
				if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
					$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
					$link = str_replace( '%#%', $n, $link );
					if ( $add_args ) {
						$link = add_query_arg( $add_args, $link );
					}
					$link .= $args['add_fragment'];

					/** This filter is documented in wp-includes/general-template.php */
					$page_no_link = number_format_i18n( $n );
					$page_links[] = "<a class='page-numbers' title='$title Page $page_no_link of $total' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . '</a>';
					$dots         = true;
				elseif ( $dots && ! $args['show_all'] ) :
					$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
					$dots         = false;
				endif;
			endif;
		endfor;
		if ( $args['prev_next'] && $current && ( $current < $total || -1 == $total ) ) :
			$link = str_replace( '%_%', $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current + 1, $link );
			if ( $add_args ) {
				$link = add_query_arg( $add_args, $link );
			}
			$link .= $args['add_fragment'];

			/** This filter is documented in wp-includes/general-template.php */
			$page_no_next = $current + 1;
			$page_links[] = '<a class="next page-numbers" rel="next" title="Next Page: ' . $title . " Page $page_no_next of $total" . '" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
		endif;
		switch ( $args['type'] ) {
			case 'array':
				return $page_links;

			case 'list':
				$r .= "<ul class='page-numbers'>\n\t<li>";
				$r .= join( "</li>\n\t<li>", $page_links );
				$r .= "</li>\n</ul>\n";
				break;

			default:
				$r = join( "\n", $page_links );
				break;
		}
		return $r;
	}
endif;

if ( ! function_exists( 'tt_paginate_links' ) ) :

	function tt_paginate_links( $query = null ) {
		global $wp_query;
		$current_query = $query != null ? $query : $wp_query;
		$pages         = $current_query->max_num_pages;
		$paged         = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		if ( is_front_page() ) {
			$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : $paged;
		}

		if ( empty( $pages ) ) {
			$pages = 1;
		}
		if ( 1 != $pages ) {
			if ( $paged > 1 ) {
				$prevlink = get_pagenum_link( $paged - 1 );
			}
			if ( $paged < $pages ) {
				$nextlink = get_pagenum_link( $paged + 1 );
			}

			$big = 9999; // need an unlikely integer
			echo "<div class='row'><div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><div class='tt-pager-pagination blog_pager clearfix'>";

			$args = array(
				'current'      => 0,
				'show_all'     => false,
				'prev_next'    => true,
				'add_args'     => false, // array of query args to add
				'add_fragment' => '',
				'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'end_size'     => 3,
				'mid_size'     => 1,
				'format'       => '?paged=%#%',
				'current'      => max( 1, get_query_var( 'paged' ) ),
				'total'        => $current_query->max_num_pages,
				'type'         => 'list',
				'prev_text'    => __( '<i class="icon-arrow-left"></i>', 'themeton' ),
				'next_text'    => __( '<i class="icon-arrow-right"></i>', 'themeton' ),
			);

			extract( $args, EXTR_SKIP );

			// Who knows what else people pass in $args
			$total = (int) $total;
			if ( $total < 2 ) {
				return;
			}
			$current    = (int) $current;
			$end_size   = 0 < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
			$mid_size   = 0 <= (int) $mid_size ? (int) $mid_size : 2;
			$add_args   = is_array( $add_args ) ? $add_args : false;
			$r          = '';
			$page_links = array();
			$next_link  = $prev_link = '';
			$n          = 0;
			$dots       = false;

			if ( $prev_next && $current && 1 < $current ) :
				$link = str_replace( '%_%', 2 == $current ? '' : $format, $base );
				$link = str_replace( '%#%', $current - 1, $link );
				if ( $add_args ) {
					$link = add_query_arg( $add_args, $link );
				}
				$link     .= $add_fragment;
				$next_link = '<a class="prev_page" rel="prev" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $prev_text . '</a>';
			endif;
			for ( $n = 1; $n <= $total; $n++ ) :
				$n_display = number_format_i18n( $n );
				if ( $n == $current ) :
					$page_links[] = "<span class='page-numbers current'>$n_display</span>";
					$dots         = true;
				else :
					if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
						$link = str_replace( '%_%', 1 == $n ? '' : $format, $base );
						$link = str_replace( '%#%', $n, $link );
						if ( $add_args ) {
							$link = add_query_arg( $add_args, $link );
						}
						$link        .= $add_fragment;
						$page_links[] = "<a class='page-numbers' data-hover='$n_display' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>$n_display</a>";
						$dots         = true;
					elseif ( $dots && ! $show_all ) :
						$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;', 'themeton' ) . '</span>';
						$dots         = false;
					endif;
				endif;
			endfor;
			if ( $prev_next && $current && ( $current < $total || -1 == $total ) ) :
				$link = str_replace( '%_%', $format, $base );
				$link = str_replace( '%#%', $current + 1, $link );
				if ( $add_args ) {
					$link = add_query_arg( $add_args, $link );
				}
				$link     .= $add_fragment;
				$prev_link = '<a class="next_page" rel="next" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $next_text . '</a>';
			endif;
			$r .= $next_link;
			$r .= "<ul>\n\t<li>";
			$r .= join( "</li>\n\t<li>", $page_links );
			$r .= "</li>\n</ul>\n";
			$r .= $prev_link;
			echo $r;
			echo '</div></div><!-- end pagination --></div>';
		}
	}

endif;

if ( ! function_exists( 'themeton_entry_meta' ) ) :

	function themeton_entry_meta() {
		if ( is_sticky() && is_home() && ! is_paged() ) {
			echo '<span class="featured-post">' . __( 'Sticky', 'themeton' ) . '</span>';
		}

		if ( ! has_post_format( 'aside' ) && ! has_post_format( 'link' ) && 'post' == get_post_type() ) {
			themeton_entry_date();
		}

		// Translators: used between list items, there is a space after the comma.
		$categories_list = get_the_category_list( __( ', ', 'themeton' ) );
		if ( $categories_list ) {
			echo '<span class="categories-links">' . $categories_list . '</span>';
		}

		// Translators: used between list items, there is a space after the comma.
		$tag_list = get_the_tag_list( '', __( ', ', 'themeton' ) );
		if ( $tag_list ) {
			echo '<span class="tags-links">' . $tag_list . '</span>';
		}

		// Post author
		if ( 'post' == get_post_type() ) {
			printf(
				'<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_attr( sprintf( __( 'View all posts by %s', 'themeton' ), get_the_author() ) ),
				get_the_author()
			);
		}
	}

endif;

if ( ! function_exists( 'themeton_entry_date' ) ) :

	function themeton_entry_date( $echo = true ) {
		$format_prefix = ( has_post_format( 'chat' ) || has_post_format( 'status' ) ) ? _x( '%1$s on %2$s', '1: post format name. 2: date', 'themeton' ) : '%2$s';

		$date = sprintf(
			'<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
			esc_url( get_permalink() ),
			esc_attr( sprintf( __( 'Permalink to %s', 'themeton' ), the_title_attribute( 'echo=0' ) ) ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
		);

		if ( $echo ) {
			echo $date;
		}

		return $date;
	}

endif;



function load_the_details() {
	global $post;
	$details = array();

	$details['sale_price']    = get_post_meta( $post->ID, '_sale_price', true );
	$details['regular_price'] = get_post_meta( $post->ID, '_regular_price', true );

	$details['club_price']    = get_post_meta( $post->ID, 'club_price', true );
	$details['current_price'] = get_post_meta( $post->ID, 'current_price', true );

	if ( is_numeric( $details['sale_price'] ) ) {
		$details['totdiff'] = $details['regular_price'] - $details['sale_price'];
	} else {
		$details['totdiff'] = $details['regular_price'];
	}

	$details['percent'] = (float) $details['totdiff'] * 100;

	return $details;
}


add_action('wp_footer','get_dynamic_price_annual_monthly');

function get_dynamic_price_annual_monthly () {


	?>
		<script>

		var ajaxurl="<?php echo admin_url( 'admin-ajax.php' ); ?>";


		jQuery(document).ready(function(){

			jQuery('input[name="radiodealclub"]').click(function() {


				var value = jQuery(this).val();
				var productID = jQuery(this).data('product-id');
				var memebrshipPrice = jQuery(this).data('price');

				jQuery.ajax(
				{
					type: 'POST',
					url: ajaxurl,
					data:
					{
						action: 'dynamic_price_check',
						value,
						product_id: productID,
						memebrship_price: memebrshipPrice,

					},
					success: function success( data )
					{
						console.log(data);
					},
					error: function (error) {
						console.log(error);
					}
				});

			});


		});

		</script>

	<?php


}



add_action( 'wp_ajax_dynamic_price_check', 'get_dynamic_price' );
add_action( 'wp_ajax_nopriv_dynamic_price_check', 'get_dynamic_price' );


function get_dynamic_price( $postid ) {


	error_log('TEJA TEJA ');

	// getting pricing from radio button selection

	if(isset($_POST["memebrship_price"])){

		$price= $_POST["memebrship_price"];
		$_SESSION['dynamic_membership_price'] = $_POST["memebrship_price"];

	}

	// getting product id

	if(isset($_POST["product_id"])){

		$postid = $_POST["product_id"];

	}

	$pricing_rule = get_post_meta( $postid, '_pricing_rules', 'true' );

	$min_price = array();
	$i         = 0;


	$dynamic_price_array = array();


	if ( is_array( $pricing_rule ) || is_object( $pricing_rule ) ) {
		foreach ( $pricing_rule as $key => $item ) {


			$membership_type = $pricing_rule[ $key]['conditions']['1']['args']['memberships']['0'];

			$min_price[ $i ] = $pricing_rule[ $key ]['rules']['1']['amount'];


			if($membership_type==174761 || $membership_type==174765){


				if( $membership_type==174765 ){

					$dynamic_price_array [0] = $pricing_rule[ $key ]['rules']['1']['amount'];

					//round the value of price upto 2 decimal points
					$dynamic_price_array [0] = round($dynamic_price_array [0], 2);

				}else {

					$dynamic_price_array [1] = $pricing_rule[ $key ]['rules']['1']['amount'];
					//round the value of price upto 2 decimal points
					$dynamic_price_array [1] = round($dynamic_price_array [1], 2);

				}

			}

			$i++;

		}
	}

		if ( empty( $dynamic_price_array ) ) {
		$dynamic_price = false;
		} else {
		$dynamic_price = $dynamic_price_array;
		}

		$both_dynamic_values = $dynamic_price ;

		$value_of_radio_box = 'withdealclub';

		if(isset($_POST["value"])) {
			$value_of_radio_box = $_POST["value"];
			$_SESSION['radio_button_value'] = $_POST["value"];
		}

		return $both_dynamic_values;

}

// Thumbnail three column design
function df_get_thumbnail_header( $shmink, $regularprice, $saleprice, $dealclub_price ) {
	if ( $regularprice > 0 ) {
		$percentage_off = ( ( $regularprice - $saleprice ) / $regularprice ) * 100;
	} else {
		$percentage_off = 100;
	}
	$df_thumbnail_header = ' <div class="thumbnailmainhd elementor-container">
                                <div class="thumbnailhead elementor-row">
                                    <div class="thumbnormal elementor-element elementor-column elementor-col-33 elementor-top-column">
                                        <a href="' . $shmink . '">
                                            Normal<br><span class="nmtxt"><strike>$' . $regularprice . '</strike></span>
                                        </a>
                                    </div>';
	if ( is_user_logged_in() && is_active_dealclub_member() ) {
		$df_thumbnail_header .= '<div class="actualprice elementor-element elementor-column elementor-col-33 elementor-top-column">
                                       <div class="actprice">
                                            <a class="today-price-a" href="' . $shmink . '">';
		if ( $dealclub_price <= 0 ) {
			$df_thumbnail_header .= '<span class="actpricehead">DealClub</span><br><span class="actpricebody">FREE</span>';
		} else {
			$df_thumbnail_header .= '<span class="actpricehead">DealClub</span><br><span class="actpricebody">$' . $dealclub_price . '</span>';
		}
		if ( $dealclub_price <= 0 ) {
			$percentage_off_df = 100;
		} else {
			if ( $regularprice == 0 ) {
												$percentage_off_df = 100;
			} else {
				$percentage_off_df = ( ( $regularprice - $dealclub_price ) / $regularprice ) * 100;
			}
		}
					  $df_thumbnail_header .= '</a>

                                       </div>
                                    </div>

                                     <div class="dealclub-price-thumb elementor-element elementor-column elementor-col-33 elementor-top-column">
                                        <a class="today-price-a" href="' . $shmink . '">
                                            Save<br><span class="nmtxt">' . round( $percentage_off_df, 2 ) . '%</span>
                                        </a>
                                    </div>';

	} else {
		$df_thumbnail_header .= '<div class="actualprice">
                                       <div class="actprice">
                                            <a class="today-price-a" href="' . $shmink . '">
                                            <span class="actpricehead">Deal Price</span><br><span class="actpricebody">$' . $saleprice . '</span>
                                            </a>

                                       </div>
                                    </div>

                                    <div class="dealclub-price-thumb">
                                        <a class="today-price-a" href="' . $shmink . '">
                                            Save<br><span class="nmtxt">' . round( $percentage_off, 2 ) . '%</span>
                                        </a>
                                    </div>';

	}

	   $df_thumbnail_header .= '</div>
                             </div>';
				return $df_thumbnail_header;
}



// Change 'add to cart' text on archive product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'button_add_to_cart_text' );
function button_add_to_cart_text() {
	 global $product;
	if ( is_active_dealclub_member() ) {
		   $updated_dynamic_price_for_dc_members = get_dynamic_price( $product->get_id() );
		if ( $updated_dynamic_price_for_dc_members == 0 ) {
			return __( 'Download', 'woocommerce' );
		} else {
			return __( 'Buy Now', 'woocommerce' );
		}
	}
	if( 0 == $product->get_sale_price() && ( $product->is_type( "simple" ) ) )
	{
		return __( 'Download', 'woocommerce' );
	}
	  return __( 'Buy Now', 'woocommerce' );
}

/*---Move Product Title*/
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );


// Remove Sales Flash
add_filter( 'woocommerce_sale_flash', 'woo_custom_hide_sales_flash' );
function woo_custom_hide_sales_flash() {
	return false;
}

// ==============================================================================

// Note the low hook priority, this should give to your other plugins the time to add their own items...
add_filter( 'woocommerce_account_menu_items', 'add_my_menu_items', 99, 1 );

function add_my_menu_items( $items ) {
	$current_user = wp_get_current_user();
	$product_id   = 88353;
	if ( wc_customer_bought_product( $current_user->email, $current_user->ID, $product_id ) ) {
		$my_items = array(
			// endpoint   => label
			'dfacademy' => __( 'DealFuel Academy' ),
			'subticket' => __( 'Support' ),
		);
	} else {
		$my_items = array(
			// endpoint   => label
			'subticket' => __( 'Support' ),
		);
	}

	$vendor_id = WC_Product_Vendors_Utils::is_vendor();
	if ( $vendor_id ) {
		$my_items = array(
			// endpoint   => label
			'dfacademy' => __( 'DealFuel Academy' ),
			'subticket' => __( 'Submit Ticket' ),
			'vendor'    => __( 'Vendor Dashboard' ),
		);
	}

	$my_items = array_slice( $items, 0, 1, true ) +
		$my_items +
		array_slice( $items, 1, count( $items ), true );

	return $my_items;
}

/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function gammaf() {

	add_rewrite_endpoint( 'subticket', EP_PAGES );
	$current_user = wp_get_current_user();
	$product_id   = 88353;
	if ( wc_customer_bought_product( $current_user->email, $current_user->ID, $product_id ) ) {
		add_rewrite_endpoint( 'dfacademy', EP_PAGES );
		add_rewrite_endpoint( 'social-media-essentials', EP_PAGES );
	}
	add_rewrite_endpoint( 'vendor', EP_PAGES );
}

add_action( 'init', 'gammaf' );


function my_custom_endpoint_content() {
	echo '<h2>DealFuel Academy</h2>';
	include_once 'df-template-df-academy.php';
}

function my_custom_endpoint_content_sm() {
	echo '<h2>Social Media Integration Essentials</h2>';
	include_once 'page-template-df-academy.php';
}

function my1_custom_endpoint_content() {
	echo '<h2 class="submit_tkt">Submit Ticket</h2>';
	echo do_shortcode( '[contact-form-7 id="313282" title="Contact form 1"]' );

}

function vendor_dash_custom_endpoint_content() {
	echo '<h2 class="vendor-dashboard-title">Vendor Dashboard</h2>';
	echo do_shortcode( '[product_vendors_report]' );
}


add_action( 'woocommerce_account_vendor_endpoint', 'vendor_dash_custom_endpoint_content' );
add_action( 'woocommerce_account_subticket_endpoint', 'my1_custom_endpoint_content' );
add_action( 'woocommerce_account_dfacademy_endpoint', 'my_custom_endpoint_content' );
add_action( 'woocommerce_account_social-media-essentials_endpoint', 'my_custom_endpoint_content_sm' );

add_action( 'init', 'remove_single_product_print_notices' );
function remove_single_product_print_notices() {
	remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
}

/**
 * Find out the type of user's memebrship
 */
function is_user_has_annual_or_monthly_memebership ( ) {
	global $wpdb;
	// get any active user memberships (requires Memberships 1.4+)
	if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
	}
	// $membership_type = $wpdb->get_row( "SELECT post_parent FROM `wp_posts` WHERE post_author=" . $user_id );
	$membership_type =	$wpdb->get_row( "SELECT post_parent FROM `wp_posts` WHERE post_author=" . $user_id . " AND post_status = 'wcm-active'" );

	$membership = $membership_type->post_parent;
	return $membership;
}


/*
 *Check if user has active WooCommerce membership or not
 * */
function is_user_an_active_member_wcm( $user_id = 0 ) {
	// bail if Memberships isn't active
	if ( ! function_exists( 'wc_memberships' ) ) {
		return false;
	}

		// get any active user memberships (requires Memberships 1.4+)
	if ( empty( $user_id ) ) {

			$user_id = get_current_user_id();
	}

	$args = array(
		'status' => array( 'active', 'free_trial', 'complimentary', 'pending' ),
	);

		$active_memberships = wc_memberships_get_user_memberships( $user_id, $args );

	if ( empty( $active_memberships ) ) {

		return false;

	} else {

		return true;
	}
}
/*
 *Check if user has active woocommerce membership or not
 * */
function is_user_an_active_member_by_email_wcm( $email ) {
	if ( empty( $email ) || ! isset( $email ) ) {
		return false;
	}
	$user = get_user_by( 'email', $email );
	if ( is_user_an_active_member_wcm() ) {
		return false;
	} else {
		return true;
	}
}

/*
 *Check if user is active woocommerce member or s2 member
 *   */
function is_active_dealclub_member() {

	global $wpdb;

	$user_id = get_current_user_id();

	if ( current_user_can( 'administrator' ) ) {

		return true;

	}
	if ( is_user_an_active_member_wcm( $user_id ) ) {

		return true;
	}
	$sql   = "SELECT * FROM wp_usermeta where user_id='" . $user_id . "' and meta_key='wp_capabilities' and meta_value LIKE '%s2member_level%'";
	$posts = $wpdb->get_results( $sql );
	if ( empty( $posts ) ) {

			return false;
	} else {
			return true;

	}

	return false;

}
add_action( 'wp_ajax_nopriv_my_special_action', 'checkUserMembership' );
function checkUserMembership() {
	if ( is_user_an_active_member_by_email_wcm( $_REQUEST['email'] ) != false ) {
		echo true;
	} else {
		echo false;
	}
	exit;
}


//modify cart prices accoding to different

add_action( 'woocommerce_before_calculate_totals', 'wwpa_simple_add_cart_price' );
function wwpa_simple_add_cart_price( $cart_object ) {

	$user_membership_type = is_user_has_annual_or_monthly_memebership();
	$is_dc_in_cart = is_dealclubmembership_in_cart();

	if ( ! is_array( is_dc_in_cart() ) || is_user_an_active_member_wcm() ) {

		foreach ( $cart_object->cart_contents as $key => $value ) {

			if ( $value['product_id'] == 174721 || $value['product_id'] == 174738 || $value['product_id'] == 174739 ) {
				// dont modify prices here

			} elseif ( empty( $value['variation'] ) ) { //simple product

						if ( $user_membership_type == 174761 ) {

							//first check if the user is monthly member

								if( is_dealclubmembership_in_cart() ) {

									//if user is monthly and annual dc is added in cart then cart price shoulbe acc to annual
									$updated_dynamic_price_for_dc_members = get_dynamic_price( $value['product_id'] )[0];

								} else {

									//if user is monthly but annual dc is not added in cart then cart price shoulbe acc to monthly
									$updated_dynamic_price_for_dc_members = get_dynamic_price( $value['product_id'] )[1];

								}
						} else if ( check_if_monthly_is_in_cart() ) { //When Non DC user and monthly is added in cart

							$updated_dynamic_price_for_dc_members = get_dynamic_price( $value['product_id'] )[1];

						}

						else { //is user membership is annual than price should be acc to annual
							$updated_dynamic_price_for_dc_members = get_dynamic_price( $value['product_id'] )[0];

						}

						$value['data']->set_price( $updated_dynamic_price_for_dc_members );

			} else { //variation product

				$dynamic_pricearr = get_all_dynamic_prices_with_id_as_key($value['product_id']);
				$jsonResponse = json_encode($dynamic_pricearr);
				$arrayResponse = json_decode($jsonResponse, true);

				// Convert JSON response to array
				$resultArray = [];
				foreach ($arrayResponse as $productId => $productData) {
					$resultArray[$productId] = $productData;
				}

				// Extract dynamic prices
				$dynamicPrices = [];
				foreach ($arrayResponse as $productId => $productData) {
					$dynamicPrices[$productId] = [
						"dynamic_price_array_annual" => $productData["dynamic_price_array_annual"],
						"dynamic_price_array_monthly" => $productData["dynamic_price_array_monthly"]
					];
				}

				$variation_arr = get_post_meta( $value['product_id'], '_pricing_rules', 'true' );


				foreach ( $variation_arr as $var_obj ) {
					if ( $var_obj['variation_rules']['args']['variations'][0] == $value['variation_id'] ) { //if our variation matches with cart's variation


						if (array_key_exists($var_obj['variation_rules']['args']['variations'][0],$dynamicPrices))
						{ //here we are checking if cart's variation exsits in our array where we have store dyanmic prices on the basis of variations
						// so if the variation exsits we will extract its monthly and annual value from the array

								if( $user_membership_type == 174765 ) { //if the membership is annual then cart will have annual' price

									$annual_value = $dynamicPrices[ $value['variation_id'] ]['dynamic_price_array_annual'];
									$value_without_dollar = str_replace('$', '', $annual_value);

									$value['data']->set_price( $value_without_dollar );

								} else if ( $user_membership_type == 174761 ) { //if user has monthly membership

									if ( check_if_annual_is_in_cart() ) { //if annual is in cart then annual's price

										$annual_value = $dynamicPrices[ $value['variation_id'] ]['dynamic_price_array_annual'];
										$value_without_dollar = str_replace('$', '', $annual_value);

										$value['data']->set_price( $value_without_dollar );


									} else {

										$monthly_value = $dynamicPrices[ $value['variation_id'] ]['dynamic_price_array_monthly'];
										$value_without_dollar = str_replace('$', '', $monthly_value);

										$value['data']->set_price( $value_without_dollar );



									}

								} else { // Non DC customers

									if ( check_if_annual_is_in_cart() ) { //if annual is in cart then annual's price

										$annual_value = $dynamicPrices[ $value['variation_id'] ]['dynamic_price_array_annual'];
										$value_without_dollar = str_replace('$', '', $annual_value);

										$value['data']->set_price( $value_without_dollar );


									} else if ( check_if_monthly_is_in_cart() ) { // if monthly is in cart

										$monthly_value = $dynamicPrices[ $value['variation_id'] ]['dynamic_price_array_monthly'];
										$value_without_dollar = str_replace('$', '', $monthly_value);

										$value['data']->set_price( $value_without_dollar );

									} else {
										//do nothing
									}

								}

						}else{
							error_log('WOOOOOOOOOOOOWW');
						}
						// $value['data']->set_price( $var_obj['rules'][1]['amount'] );

						// $value['data']->set_price(55);
					}
				}
			}
		}
	}
}


/**
 * Check if monthly membership is added in cart.
*/

function check_if_monthly_is_in_cart() {
	global $woocommerce;
	$items                 = $woocommerce->cart->get_cart();
	$valueFound = false;
	foreach ( $items as $item ) {

		if ( $item['product_id'] == 174721 ) {
			$valueFound = true;
			break;
		}

	}

	return $valueFound ;


}


/**
 * Check if annual membership is added in cart.
*/

function check_if_annual_is_in_cart() {
	global $woocommerce;
	$items                 = $woocommerce->cart->get_cart();
	$valueFound = false;
	foreach ( $items as $item ) {

		if ( $item['product_id'] == 174739 ) {
			$valueFound = true;
			break;
		}

	}

	return $valueFound ;


}


/**
 * Add one membership at a time in cart
 *
*/

function check_membership_add_to_cart() {
    global $woocommerce;

    $cart = $woocommerce->cart;
    $membership_product_ids = array( 174739, 174721 ); // Replace with your membership product IDs

    // Loop through cart items and check if there are already memberships in the cart
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( in_array( $cart_item['product_id'], $membership_product_ids ) ) {
            wc_add_notice( 'You can only purchase one membership at a time. Remove the existing membership from the cart then add other membership', 'error' );
            break;
        }
    }
}

// add_action( 'woocommerce_add_to_cart_validation', 'check_membership_add_to_cart' );




function is_dc_in_cart() {

	global $woocommerce;
	$items                 = $woocommerce->cart->get_cart();
	$ret_arr['total_cart'] = 0;

	$user_membership_type = is_user_has_annual_or_monthly_memebership();


	foreach ( $items as $item ) {

		if ( in_array( $item['product_id'], array( 174721, 174738, 174739 ) ) ) {
            $plan = $item['data']->get_sku();
        }
		if ( empty( $item['variation'] ) ) { // when product is simple product

			if( $user_membership_type == 174761 ) { //if membership is monthly then add monthly price to the cart which is stored at index 1 in get_dynamic_price

				$ret_arr['total_cart'] = $ret_arr['total_cart'] + get_dynamic_price( $item['product_id'] )[1];

			} else if ($user_membership_type != 174761 && check_if_monthly_is_in_cart() ) {

				$ret_arr['total_cart'] = $ret_arr['total_cart'] + get_dynamic_price( $item['product_id'] )[1];
			}

			else { //if not monthly then annual's price

				$ret_arr['total_cart'] = $ret_arr['total_cart'] + get_dynamic_price( $item['product_id'] )[0];
			}

		} else {
			$var_price     = 0;
			$variation_arr = get_post_meta( $item['product_id'], '_pricing_rules', 'true' );
			foreach ( $variation_arr as $var_obj ) {
				if ( $var_obj['variation_rules']['args']['variations'][0] == $item['variation_id'] ) {
						$var_price = $var_obj['rules'][1]['amount'];
						// $var_price=44;
				}
			}
			$ret_arr['total_cart'] = $ret_arr['total_cart'] + $var_price;
		}
	}

	return isset( $plan ) ? $plan : $ret_arr;
}

function get_product_dealclub_variation_price( $product ) {
	$pricing_rule = get_post_meta( $product, '_pricing_rules', 'true' );
	foreach ( $pricing_rule as $key => $item ) {
		$price[] = $pricing_rule[ $key ]['rules']['1']['amount'];
		// $price[]=33;
	}
	return $price;
}


// Vendor Sections start here

function df_vendor_report() {

	global $wpdb;

	$retrieve_data = $wpdb->get_results(
		"SELECT distinct(m.name),r.object_id
									FROM wp_terms m,wp_term_taxonomy t,wp_term_relationships r
									WHERE t.taxonomy='wcpv_product_vendors'
									and m.term_id=t.term_id
									and r.term_taxonomy_id=t.term_taxonomy_id
									and r.object_id !=0"
	);

	?>
<table width="100%" border="1">

	<tr>
		<th>Seller Name</th>
		<th>Product ID</th>
		<th>Product Name</th>
		<th>DealFuel Price</th>
		<th>DF Commission</th>
		<th>Deal Club Price</th>
		<th>Club Commission</th>
		<th>Total Sale</th>
	</tr>

	<?php

	foreach ( $retrieve_data as $key ) {

		?>
	<tr>

		<?php

			$data = $wpdb->get_results(
				"SELECT count(posts.ID) as total_count,order_items.order_item_name
                    					FROM wp_posts AS posts

					                    LEFT JOIN wp_woocommerce_order_items AS order_items ON posts.ID = order_items.order_id
					                    LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__product_id ON order_items.order_item_id = order_item_meta__product_id.order_item_id
					                    LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__qty ON order_items.order_item_id = order_item_meta__qty.order_item_id

					                    WHERE posts.post_type IN ( 'shop_order' )
					                    AND posts.post_status IN ( 'wc-completed')

					                    AND order_items.order_item_type = 'line_item'
					                    AND order_item_meta__product_id.meta_key IN ('_product_id','_variation_id')
					                    AND order_item_meta__qty.meta_key = '_line_subtotal'
                                        AND order_item_meta__product_id.meta_value = '" . $key->object_id . "'
										"
			);

		foreach ( $data as $k ) {

			$get_postmeta = $wpdb->get_results(
				"SELECT m1.meta_value AS 'sale_price',
										m2.meta_value AS 'pricing_rule'
										FROM wp_postmeta m1, wp_postmeta m2
										WHERE m1.post_id =" . $key->object_id . "
										AND m1.post_id = m2.post_id
										AND m1.meta_key = '_sale_price'
										AND m2.meta_key = '_pricing_rules'"
			);
			foreach ( $get_postmeta as $key2 ) {

				if ( $key2->sale_price == 0 || $k->total_count == 0 ) {
					$i = 1;
					break;
				} else {
					?>
					<td><?php echo $key->name; ?></td>
					<td><?php echo $key->object_id; ?></td>
					<td><?php echo $k->order_item_name; ?></td>
					<td><?php echo $key2->sale_price; ?></td>

					<?php

					$df_commission_key = '_product_vendors_commission_' . $key2->sale_price;
					$df_commission     = get_post_meta( $key->object_id, $df_commission_key, true );

					$x         = unserialize( $key2->pricing_rule );
					$min_price = array();
					$i         = 0;

					foreach ( $x as $y ) {

						$min_price[ $i ] = $y[ rules ][1][ amount ];
						$i++;

					}

							$dynamic_price = min( $min_price );

					?>
					<td><?php echo $df_commission . ' %'; ?></td>
					<td><?php echo $dynamic_price; ?></td>

					<?php

							$dynamic_price_key = '_product_vendors_commission_' . $dynamic_price;
							$club_commission   = get_post_meta( $key->object_id, $dynamic_price_key, true );

					?>

					<td><?php echo $club_commission . ' %'; ?></td>
					<td><?php echo $k->total_count; ?></td>
					<?php

				}
			}
		}
		?>
		</tr>
		<?php
	}
	?>
	  </table>
	<?php
}

// add_shortcode('df_show_vendors_sales_report', 'df_vendor_report');





// vendor report shortcode
/*
function vendor_report() {

				$selected_year = ( isset( $_POST['report_year'] ) && $_POST['report_year'] != 'all' ) ? $_POST['report_year'] : false;

			$selected_month = ( isset( $_POST['report_month'] ) && $_POST['report_month'] != 'all' ) ? $_POST['report_month'] : false;

// echo "<br>This is the selected month:". $selected_month . " ";
// echo "<br>This is the selected year:". $selected_year;
// echo "<br>============<br>";
if($selected_year)
	$a_date = $selected_year."-". $selected_month ."-23";
else
	$a_date = date( 'Y' )."-". $selected_month ."-23";
$lastday=date("t", strtotime($a_date));



	$vendor_id = WC_Product_Vendors_Utils::is_vendor();

		if( $vendor_id ) {
$user_id=get_current_user_id();
$vendor_id=get_user_meta($user_id,'product_vendor');


			$selected_year = ( isset( $_POST['report_year'] ) && $_POST['report_year'] != 'all' ) ? $_POST['report_year'] : false;

			$selected_month = ( isset( $_POST['report_month'] ) && $_POST['report_month'] != 'all' ) ? $_POST['report_month'] : false;

			// Get all vendor commissions
// $commissions = get_vendor_commissions( 588 );
global $wpdb;

if(!$selected_year && !$selected_month)
	$commissions = $wpdb->get_results("SELECT * FROM wp_wcpv_commissions WHERE vendor_id=$vendor_id[0]");
elseif($selected_year && $selected_month)
	$commissions = $wpdb->get_results("SELECT * FROM wp_wcpv_commissions WHERE order_date BETWEEN '".$selected_year."-".$selected_month."-01 00:00:00.000000' AND '".$selected_year."-".$selected_month."-".$lastday." 00:00:00.000000' AND vendor_id=$vendor_id[0]");
elseif($selected_year && !$selected_month)
	$commissions = $wpdb->get_results("SELECT * FROM wp_wcpv_commissions WHERE order_date BETWEEN '".$selected_year."-01-01 00:00:00.000000' AND '".$selected_year."-12-31 00:00:00.000000' AND vendor_id=$vendor_id[0]");
elseif(!$selected_year && $selected_month)
	$commissions = $wpdb->get_results("SELECT * FROM wp_wcpv_commissions WHERE order_date BETWEEN '".date( 'Y' )."-".$selected_month."-01 00:00:00.000000' AND '".date( 'Y' )."-".$selected_month."-31 00:00:00.000000' AND vendor_id=$vendor_id[0]");



$i=0;$arr=array();
$arr_unique=array();


			$month_options = '<option value="all">' . __( 'All months', 'wc_product_vendors' ) . '</option>';

			for( $i = 1; $i <= 12; $i++ ) {

				$month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );

				$month_name = date( 'F', mktime( 0, 0, 0, $i + 1, 0, 0 ) );

				$month_options .= '<option value="' . esc_attr( $month_num ) . '" ' . selected( $selected_month, $month_num, false ) . '>' . $month_name . '</option>';

			}



			$year_options = '<option value="all">' . __( 'All years', 'wc_product_vendors' ) . '</option>';

			$current_year = date( 'Y' );

			for( $i = $current_year; $i >= ( $current_year - 5 ); $i-- ) {

				$year_options .= '<option value="' . $i . '" ' . selected( $selected_year, $i, false ) . '>' . $i . '</option>';

			}


foreach ( $commissions as $commission )
{
	if($i==0)
	{
	echo "<br>ID:" . $commission->total_commission_amount;
		echo " status:" . $commission->commission_status;
		echo " order:" . $commission->order_id;
		echo " product:" . $commission->product_id;
		echo " variation:" . $commission->variation_id;

	echo "<br>";
	}

	if($commission->variation_id==0)
	{
		array_push($arr, $commission->product_id);
	}
	else
		array_push($arr, $commission->variation_id);

	$i++;
}
// echo "Commission Data:" . $i;
// count($commissions);
// print_r($commissions,true);

echo "<br>";

// echo "<br>The Array is:<br>" . print_r(array_unique($arr)) . "<br>";

// echo "Number Of Sales " . print_r(array_count_values($arr));;

$arr_unique = array_unique($arr);?>

<div class="product_vendors_report_form">

			<form name="vendors_form" action="" method="post" id="vendors_form">

							Select report date:

							<select name="report_month" id="report_month"><?php echo $month_options; ?></select>

							<select name="report_year" id="report_year"><?php echo $year_options; ?></select>

							<input type="submit" class="vendor_button" value="Submit" name="vendor_submit" id="vendor_submit"/>

			</form>

</div>

<table class="shop_table" cellspacing="0" border="1px">

						<thead class="vendor_thead">

							<tr>

								<th>Product Name</th>

								<th>Number of Sales</th>

								<th>Earnings</th>

							</tr>

						</thead>

						<tbody class="vendor_tbody">
<?php
foreach( $arr_unique as $arr_val )
{

	$product_type;
	//for normal product
	if( $commission->product_id == $arr_val )
	{
		$earnings = $wpdb->get_results("SELECT total_commission_amount FROM wp_wcpv_commissions WHERE vendor_id=$vendor_id[0] AND product_id=$arr_val");
		$product_type="single";
	}
	else
	{
		$earnings = $wpdb->get_results("SELECT total_commission_amount FROM wp_wcpv_commissions WHERE vendor_id=$vendor_id[0] AND variation_id=$arr_val");
		$product_type="var";
	}

	$total_earnings=0;
	?>
	<tr>
	<td>
	<?php
	$product = wc_get_product( $arr_val );


if($product_type=="var")
echo $product->get_formatted_name();
else
echo $product->get_title();
// echo "loop for " . $arr_val . ": ";
	?>
	</td>
	<td>
		<?php echo count($earnings); ?>
	</td>
	<?php
	foreach($earnings as $earning)
	{
		$total_earnings+=$earning->total_commission_amount;
	   // echo " ". print_r( $earning->total_commission_amount );
	}
	?>
	<td>
	<?php
		echo "$" . round($total_earnings,2) ."<br>";
	?>
	</td>
	</tr>
	<?php
}
?>
</tbody>
</table>
<?php
			$total_earnings = 0;

			foreach( $commissions as $commission ) {

// $earnings = get_post_meta( $commission->ID, '_commission_amount', true );
 //               $earnings = $commission->total_commission_amount;

// $proceess = get_post_meta( $commission->ID, '_processor_fee', true );

  //              $earnings = $earnings - get_post_meta( $commission->ID, '_processor_fee', true );

// $paid_status = get_post_meta( $commission->ID, '_paid_status', true );
   //             $paid_status = $commission->commission_status;

// $order_id = get_post_meta( $commission->ID, '_commission_order', true );
	 //           $order_id = $commission->order_id;

	   //         $paid=0;$unpaid=0;


// $product_id = get_post_meta( $commission->ID, '_commission_product', true );
// $product_id = $commission->product_id;

		//        $product = get_product( $product_id );



			}


		}






}
*/

// vendor report shortcode

function vendor_report() {
	global $wpdb;
	$userid     = get_current_user_id();
	$vendor_raw = $wpdb->get_results(
		"
    SELECT term_id
    FROM `wp_termmeta` WHERE `meta_key` LIKE 'vendor_data' AND `meta_value` LIKE '%$userid%'
"
	);

	$vendor_id = $vendor_raw[0]->term_id;

	$html = '';

	if ( 'WC_Product_Vendors_Utils::is_vendor()' ) {

		if ( $vendor_id ) {

			$selected_year = ( isset( $_POST['report_year'] ) && $_POST['report_year'] != 'all' ) ? $_POST['report_year'] : false;

			$selected_month = ( isset( $_POST['report_month'] ) && $_POST['report_month'] != 'all' ) ? $_POST['report_month'] : false;

			$commissions = $wpdb->get_results(
				"
        SELECT *
        FROM `wp_wcpv_commissions` WHERE `vendor_id` = $vendor_id
    "
			);

			$total_earnings = 0;

			foreach ( $commissions as $commission ) {

				$earnings = $commission->product_commission_amount;

				$proceess = $commission->product_tax_amount;

				$earnings = $earnings - $proceess;

				$paid_status = $commission->commission_status;

				$order_id = $commission->order_id;

				$paid   = 0;
				$unpaid = 0;

				if ( isset( $paid_status ) ) {

					if ( $paid_status == 'paid' ) {

						$paid = $earnings;

					} elseif ( $paid_status == 'unpaid' ) {

						$unpaid = $earnings;
					}
				}

				$product_id = $commission->product_id;

				if ( ! in_array( $product_id, $dataReborn ?? []) ) {
					$dataReborn[] = $product_id;
				}

				$product = wc_get_product( $product_id );

				if ( ! isset( $product ) || ! $product || is_wp_error( $product ) || ! is_object( $product ) ) {

					continue;

				}

				$purchased_variation_id = null;

				$order = new WC_Order( $order_id );

				$items = $order->get_items( 'line_item' );

				foreach ( $items as $item => $item_val ) {

					$product_name = $item_val['name'];

					$product_id        = $item_val['product_id'];
					$product_data_item = get_product( $product_id );

					$vendor_id_arr = array();
					$terms         = get_the_terms( $product_id, 'wcpv_product_vendors' );

					if (is_array($terms)) {
						for ($i = 0; $i < count($terms); $i++) {
							$vendor_id_arr[] = $terms[$i]->term_id;
						}
					}

					if ( $product_data_item->is_type( 'simple' ) && in_array( $vendor_id, $vendor_id_arr ) ) {

						$item_quantity = $order->get_item_meta( $item, '_qty', true );

						if ( ! isset( $data[ $product_id ]['product'] ) ) {

							$data[ $product_id ]['product'] = $product->get_title();

						}

						if ( ! isset( $data[ $product_id ]['product_url'] ) ) {

							$data[ $product_id ]['product_url'] = get_permalink( $product_id );

						}

						if ( isset( $data[ $product_id ]['sales'] ) ) {

							$data[ $product_id ]['sales'] += $item_quantity;

						} else {

							$data[ $product_id ]['sales'] = $item_quantity;

						}

						if ( isset( $data[ $product_id ]['earnings'] ) ) {

							$data[ $product_id ]['earnings'] += $earnings;

						} else {

							$data[ $product_id ]['earnings'] = $earnings;
						}
					}//close vendor compare condition
					elseif ( $product_data_item->is_type( 'variable' ) && in_array( $vendor_id, $vendor_id_arr ) ) {

						$item_quantity = $order->get_item_meta( $item, '_qty', true );

						$purchased_variation_id = $item_val['variation_id'];

						/*
						* Fetch all variations from product id

						*/

						$product = new WC_Product_Variable( $product_id );

						$variations = $product->get_available_variations();

						$var_data = array();

						foreach ( $variations as $variation ) {

							$display_regular_price = $variation['display_regular_price'] . '<span class="currency">' . $currency_symbol . '</span>';

							$display_price = $variation['display_price'] . '<span class="currency">' . $currency_symbol . '</span>';

							if ( $purchased_variation_id == $variation['variation_id'] ) {

								if ( ! isset( $data[ $variation['variation_id'] ]['product'] ) ) {

									$data[ $variation['variation_id'] ]['product'] = $product->get_title() . ' - ' . $variation['attributes']['attribute_choose-a-plan'];

								}

								if ( ! isset( $data[ $variation['variation_id'] ]['product_url'] ) ) {

									$data[ $variation['variation_id'] ]['product_url'] = get_permalink( $product_id );

								}

								if ( isset( $data[ $variation['variation_id'] ]['sales'] ) ) {

									$data[ $variation['variation_id'] ]['sales'] += $item_quantity;

								} else {

									$data[ $variation['variation_id'] ]['sales'] = $item_quantity;

								}

								if ( isset( $data[ $variation['variation_id'] ]['earnings'] ) ) {

									$data[ $variation['variation_id'] ]['earnings'] += $earnings;

								} else {

									$data[ $variation['variation_id'] ]['earnings'] = $earnings;

								}
							}
						}
					}
				} //close foreach loop for each order items

				$total_earnings += $earnings;

				$i++;
			}

			$month_options = '<option value="all">' . __( 'All months', 'wc_product_vendors' ) . '</option>';

			for ( $i = 1; $i <= 12; $i++ ) {

				$month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );

				$month_name = date( 'F', mktime( 0, 0, 0, $i + 1, 0, 0 ) );

				$month_options .= '<option value="' . esc_attr( $month_num ) . '" ' . selected( $selected_month, $month_num, false ) . '>' . $month_name . '</option>';

			}

			$year_options = '<option value="all">' . __( 'All years', 'wc_product_vendors' ) . '</option>';

			$current_year = date( 'Y' );

			for ( $i = $current_year; $i >= ( $current_year - 5 ); $i-- ) {

				$year_options .= '<option value="' . $i . '" ' . selected( $selected_year, $i, false ) . '>' . $i . '</option>';

			}

			$html .= '<div class="product_vendors_report_form">

                        <form name="vendors_form" action="" method="post" id="vendors_form">

                            ' . __( 'Select report date:   ', 'wc_product_vendors' ) . '    <select name="report_month" id="report_month">' . $month_options . '</select>


                            <select name="report_year" id="report_year">' . $year_options . '</select>
                            <input type="submit" class="vendor_button" value="Submit" name="vendor_submit" id="vendor_submit"/>

                        </form>

                      </div>';

			$html .= '<table class="shop_table" cellspacing="0" border="1px">

                        <thead class="vendor_thead">

                            <tr>

                                <th>' . __( 'Product', 'wc_product_vendors' ) . '</th>

                                <th>' . __( 'Sales', 'wc_product_vendors' ) . '</th>

                                <th>' . __( 'Earnings', 'wc_product_vendors' ) . '</th>

                            </tr>

                        </thead>

                        <tbody class="vendor_tbody">';

			if ( isset( $data ) && is_array( $data ) ) {

				foreach ( $dataReborn as $dataR ) {

					$product = wc_get_product( $dataR );
					// $units_sold = get_post_meta( $dataR, 'total_sales', true );
					$units_sold_record       = $wpdb->get_results( "SELECT SUM(CASE WHEN product_quantity = '' THEN 1 ELSE 0 END) AS 'cnt1', SUM(CASE WHEN product_quantity <> '' THEN product_quantity ELSE 0 END) AS 'cnt2' FROM wp_wcpv_commissions WHERE `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );
					$units_sold = $units_sold_record[0]->cnt1 + $units_sold_record[0]->cnt2;
					$commission_amt   = $wpdb->get_var( "SELECT SUM(total_commission_amount) FROM `wp_wcpv_commissions` WHERE `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );
					$FinalCommission += $commission_amt;

					$html .= '<tr>

                                <td style="width:50%"><a href="' . esc_url( $product->get_permalink() ) . '">' . $product->get_name() . '</a></td>

                                <td>' . $units_sold . '</td>

                                <td>' . get_woocommerce_currency_symbol() . number_format( $commission_amt, 2 ) . '</td>

                              </tr>';

				}

				$html .= '<tr>

                            <td colspan="2"><b>' . __( 'Total', 'wc_product_vendors' ) . '</b></td>





                            <td>' . get_woocommerce_currency_symbol() . number_format( $FinalCommission, 2 ) . '</td>

                          </tr>';

			} else {

				$html .= '<tr><td colspan="3"><em>' . __( 'No sales found', 'wc_product_vendors' ) . '</em></td></tr>';

			}

			$html .= '</tbody>

                    </table>';

		}
	}

	return $html;

}



// Vendor report: Total earnings
add_shortcode( 'product_vendors_report', 'vendor_report' );


add_action( 'wp_ajax_get_vendor_report_data', 'get_vendor_report_data' );
add_action( 'wp_ajax_nopriv_get_vendor_report_data', 'get_vendor_report_data' );
function get_vendor_report_data() {

	global $wpdb;
	$userid     = get_current_user_id();
	$vendor_raw = $wpdb->get_results(
		"
    SELECT term_id
    FROM `wp_termmeta` WHERE `meta_key` LIKE 'vendor_data' AND `meta_value` LIKE '%$userid%'
"
	);

	$vendor_id = $vendor_raw[0]->term_id;
	$html      = '';

	if ( $vendor_id ) {

		if ( $vendor_id ) {
			$selected_month = ( isset( $_POST['report_month'] ) && $_POST['report_month'] != 'all' ) ? $_POST['report_month'] : false;

			$selected_year = ( isset( $_POST['report_year'] ) && $_POST['report_year'] != 'all' ) ? $_POST['report_year'] : false;

			$selected_date = ( isset( $_POST['wcpv_report_date'] ) && $_POST['wcpv_report_date'] != 'all' ) ? $_POST['wcpv_report_date'] : false;

			if ( $selected_year && $selected_month ) {
				$commissions = $wpdb->get_results(
					"
        SELECT *
        FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-$selected_month-01 00:00:00.000000' AND '$selected_year-$selected_month-31 23:59:59.999999' AND `vendor_id` = $vendor_id
    "
				);
			} elseif ( $selected_year && ! $selected_month ) {
				$commissions = $wpdb->get_results(
					"
        SELECT *
        FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-01-01 00:00:00.000000' AND '$selected_year-12-31 23:59:59.999999' AND `vendor_id` = $vendor_id
    "
				);
			} elseif ( ! $selected_year && $selected_month ) {
				$selected_year = date( 'Y' );
				$commissions   = $wpdb->get_results(
					"
        SELECT *
        FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-$selected_month-01 00:00:00.000000' AND '$selected_year-$selected_month-31 23:59:59.999999' AND `vendor_id` = $vendor_id
    "
				);
			} else {
				$commissions = $wpdb->get_results(
					"
        SELECT *
        FROM `wp_wcpv_commissions` WHERE `vendor_id` = $vendor_id
    "
				);
			}
			$total_earnings = 0;

			foreach ( $commissions as $commission ) {

				$earnings = $commission->product_commission_amount;

				$proceess = $commission->product_tax_amount;

				$earnings = $earnings - $proceess;

				$paid_status = $commission->commission_status;

				$order_id = $commission->order_id;

				$paid   = 0;
				$unpaid = 0;

				if ( isset( $paid_status ) ) {

					if ( $paid_status == 'paid' ) {

						$paid = $earnings;

					} elseif ( $paid_status == 'unpaid' ) {

						$unpaid = $earnings;
					}
				}

				$product_id = $commission->product_id;

				if ( ! in_array( $product_id, $dataReborn ?? []) ) {
					$dataReborn[] = $product_id;
				}

				$product = get_product( $product_id );

				if ( ! isset( $product ) || ! $product || is_wp_error( $product ) || ! is_object( $product ) ) {

					continue;

				}

				$purchased_variation_id = null;

				$order = new WC_Order( $order_id );

				$items = $order->get_items( 'line_item' );

				foreach ( $items as $item => $item_val ) {

					$product_name = $item_val['name'];

					$product_id        = $item_val['product_id'];
					$product_data_item = get_product( $product_id );

					$vendor_id_arr = array();
					$terms         = get_the_terms( $product_id, 'wcpv_product_vendors' );
					if(!empty($terms)){
						for ( $i = 0;$i < count( $terms );$i++ ) {

							$vendor_id_arr[] = $terms[ $i ]->term_id;

						}
					}

					if ( $product_data_item->is_type( 'simple' ) && in_array( $vendor_id, $vendor_id_arr ) ) {

						$item_quantity = $order->get_item_meta( $item, '_qty', true );

						if ( ! isset( $data[ $product_id ]['product'] ) ) {

							$data[ $product_id ]['product'] = $product->get_title();

						}

						if ( ! isset( $data[ $product_id ]['product_url'] ) ) {

							$data[ $product_id ]['product_url'] = get_permalink( $product_id );

						}

						if ( isset( $data[ $product_id ]['sales'] ) ) {

							$data[ $product_id ]['sales'] += $item_quantity;

						} else {

							$data[ $product_id ]['sales'] = $item_quantity;

						}

						if ( isset( $data[ $product_id ]['earnings'] ) ) {

							$data[ $product_id ]['earnings'] += $earnings;

						} else {

							$data[ $product_id ]['earnings'] = $earnings;
						}
					}//close vendor compare condition
					elseif ( $product_data_item->is_type( 'variable' ) && in_array( $vendor_id, $vendor_id_arr ) ) {

						$item_quantity = $order->get_item_meta( $item, '_qty', true );

						$purchased_variation_id = $item_val['variation_id'];

						/*
						* Fetch all variations from product id

						*/

						$product = new WC_Product_Variable( $product_id );

						$variations = $product->get_available_variations();

						$var_data = array();

						foreach ( $variations as $variation ) {

							$display_regular_price = $variation['display_regular_price'] . '<span class="currency">' . $currency_symbol . '</span>';

							$display_price = $variation['display_price'] . '<span class="currency">' . $currency_symbol . '</span>';

							if ( $purchased_variation_id == $variation['variation_id'] ) {

								if ( ! isset( $data[ $variation['variation_id'] ]['product'] ) ) {

									$data[ $variation['variation_id'] ]['product'] = $product->get_title() . ' - ' . $variation['attributes']['attribute_choose-a-plan'];

								}

								if ( ! isset( $data[ $variation['variation_id'] ]['product_url'] ) ) {

									$data[ $variation['variation_id'] ]['product_url'] = get_permalink( $product_id );

								}

								if ( isset( $data[ $variation['variation_id'] ]['sales'] ) ) {

									$data[ $variation['variation_id'] ]['sales'] += $item_quantity;

								} else {

									$data[ $variation['variation_id'] ]['sales'] = $item_quantity;

								}

								if ( isset( $data[ $variation['variation_id'] ]['earnings'] ) ) {

									$data[ $variation['variation_id'] ]['earnings'] += $earnings;

								} else {

									$data[ $variation['variation_id'] ]['earnings'] = $earnings;

								}
							}
						}
					}
				} //close foreach loop for each order items

				$total_earnings += $earnings;

			}

			$month_options = '<option value="all">' . __( 'All months', 'wc_product_vendors' ) . '</option>';

			for ( $i = 1; $i <= 12; $i++ ) {

				$month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );

				$month_name = date( 'F', mktime( 0, 0, 0, $i + 1, 0, 0 ) );

				$month_options .= '<option value="' . esc_attr( $month_num ) . '" ' . selected( $selected_month, $month_num, false ) . '>' . $month_name . '</option>';

			}

			$year_options = '<option value="all">' . __( 'All years', 'wc_product_vendors' ) . '</option>';

			$current_year = date( 'Y' );

			for ( $i = $current_year; $i >= ( $current_year - 5 ); $i-- ) {

				$year_options .= '<option value="' . $i . '" ' . selected( $selected_year, $i, false ) . '>' . $i . '</option>';

			}

			$html .= '<table class="shop_table" cellspacing="0" border="1px">

                        <thead class="vendor_thead">

                            <tr>

                                <th>' . __( 'Product', 'wc_product_vendors' ) . '</th>

                                <th>' . __( 'Sales', 'wc_product_vendors' ) . '</th>

                                <th>' . __( 'Earnings', 'wc_product_vendors' ) . '</th>

                            </tr>

                        </thead>

                        <tbody class="vendor_tbody">';

			if ( isset( $data ) && is_array( $data ) ) {

				foreach ( $dataReborn as $dataR ) {

					$product = wc_get_product( $dataR );
					if ( $selected_year && $selected_month ) {
						$commission_amt = $wpdb->get_var( "SELECT SUM(total_commission_amount) FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-$selected_month-01 00:00:00.000000' AND '$selected_year-$selected_month-31 23:59:59.999999' AND `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );

						$units_sold_record = $wpdb->get_results( "SELECT SUM(CASE WHEN product_quantity = '' THEN 1 ELSE 0 END) AS 'cnt1', SUM(CASE WHEN product_quantity <> '' THEN product_quantity ELSE 0 END) AS 'cnt2' FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-$selected_month-01 00:00:00.000000' AND '$selected_year-$selected_month-31 23:59:59.999999' AND `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );
						$units_sold = $units_sold_record[0]->cnt1 + $units_sold_record[0]->cnt2;

					} elseif ( $selected_year && ! $selected_month ) {
						$commission_amt = $wpdb->get_var( "SELECT SUM(total_commission_amount) FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-01-01 00:00:00.000000' AND '$selected_year-12-31 23:59:59.999999' AND `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );

						$units_sold_record = $wpdb->get_results( "SELECT SUM(CASE WHEN product_quantity = '' THEN 1 ELSE 0 END) AS 'cnt1', SUM(CASE WHEN product_quantity <> '' THEN product_quantity ELSE 0 END) AS 'cnt2' FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-01-01 00:00:00.000000' AND '$selected_year-12-31 23:59:59.999999' AND `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );
						$units_sold = $units_sold_record[0]->cnt1 + $units_sold_record[0]->cnt2;

					} elseif ( ! $selected_year && $selected_month ) {
						$selected_year  = date( 'Y' );
						$commission_amt = $wpdb->get_var( "SELECT SUM(total_commission_amount) FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-$selected_month-01 00:00:00.000000' AND '$selected_year-$selected_month-31 23:59:59.999999' AND `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );

						$units_sold_record = $wpdb->get_results( "SELECT SUM(CASE WHEN product_quantity = '' THEN 1 ELSE 0 END) AS 'cnt1', SUM(CASE WHEN product_quantity <> '' THEN product_quantity ELSE 0 END) AS 'cnt2' FROM `wp_wcpv_commissions` WHERE `order_date` BETWEEN '$selected_year-$selected_month-01 00:00:00.000000' AND '$selected_year-$selected_month-31 23:59:59.999999' AND `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );
						$units_sold = $units_sold_record[0]->cnt1 + $units_sold_record[0]->cnt2;

					} else {
						$commission_amt = $wpdb->get_var( "SELECT SUM(total_commission_amount) FROM `wp_wcpv_commissions` WHERE `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );

						// $units_sold = get_post_meta( $dataR, 'total_sales', true );
						$units_sold_record = $wpdb->get_results( "SELECT SUM(CASE WHEN product_quantity = '' THEN 1 ELSE 0 END) AS 'cnt1', SUM(CASE WHEN product_quantity <> '' THEN product_quantity ELSE 0 END) AS 'cnt2' FROM `wp_wcpv_commissions` WHERE `vendor_id` = '$vendor_id' AND `product_id` = '$dataR'" );
						$units_sold = $units_sold_record[0]->cnt1 + $units_sold_record[0]->cnt2;

					}

					$FinalCommission += $commission_amt;

					$html .= '<tr>

                                <td><a href="' . esc_url( $product->get_permalink() ) . '">' . $product->get_name() . '</a></td>

                                <td>' . $units_sold . '</td>

                                <td>' . get_woocommerce_currency_symbol() . number_format( $commission_amt, 2 ) . '</td>

                              </tr>';

				}

				$html .= '<tr>

                            <td colspan="2"><b>' . __( 'Total', 'wc_product_vendors' ) . '</b></td>





                            <td>' . get_woocommerce_currency_symbol() . number_format( $FinalCommission, 2 ) . '</td>

                          </tr>';

			} else {

				$html .= '<tr><td colspan="3"><em>' . __( 'No sales found', 'wc_product_vendors' ) . '</em></td></tr>';

			}

			$html .= '</tbody>

                    </table>';

		}
	}

	echo "$html";
	die;
}

function dealfuel_custom_scripts() {
	wp_enqueue_script( 'dealfuel', get_bloginfo( 'template_directory' ) . '/../astra-child/dealfuel.js', 'jquery', false, true );
			wp_enqueue_script( 'jquery-ui', 'https://code.jquery.com/ui/1.10.1/jquery-ui.min.js', 'jquery', false, true );

	wp_enqueue_script( 'bootstrap_min', get_bloginfo( 'template_directory' ) . '/../astra-child/js/bootstrap.min.js', array(), false, true );
	wp_localize_script( 'dealfuel', 'dealfuel_data', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

add_action( 'wp_enqueue_scripts', 'dealfuel_custom_scripts' );


function dc_pricing_table_level_monthly( $atts, $content = null ) {
	ob_start();
	get_template_part( 'part', 'free' );
		$content = ob_get_contents();
	ob_end_clean();
	$content = get_template_part( 'part', 'monthly' );
	return $content;
}
add_shortcode( 'dc_pricing_table_level_monthly', 'dc_pricing_table_level_monthly' );

function dc_pricing_table_level_annual( $atts, $content = null ) {

	ob_start();
	get_template_part( 'part', 'free' );
		$content = ob_get_contents();
	ob_end_clean();
	$content = get_template_part( 'part', 'annual' );
	return $content;
}
add_shortcode( 'dc_pricing_table_level_annual', 'dc_pricing_table_level_annual' );

function dc_pricing_table_level_quarterly( $atts, $content = null ) {
	ob_start();
	get_template_part( 'part', 'free' );
		$content = ob_get_contents();
	ob_end_clean();
	$content = get_template_part( 'part', 'quarterly' );
	return $content;
}
add_shortcode( 'dc_pricing_table_level_quarterly', 'dc_pricing_table_level_quarterly' );

add_filter( 'woocommerce_widget_cart_is_hidden', '__return_true' );


// Guest Checkout Account Creation

function wc_register_guests( $order_id ) {
	// get all the order data
	$order = new WC_Order( $order_id );

	// get the user email from the order
	$order_email = $order->billing_email;

	// check if there are any users with the billing email as user or email
	$email = email_exists( $order_email );
	$user  = username_exists( $order_email );

	// if the UID is null, then it's a guest checkout
	if ( $user == false && $email == false ) {

		// random password with 12 chars
		$random_password = wp_generate_password();

		// create new user with email as username & newly created pw
		$user_id = wp_create_user( $order_email, $random_password, $order_email );

		// WC guest customer identification
		update_user_meta( $user_id, 'guest', 'yes' );

		// user's billing data
		update_user_meta( $user_id, 'billing_address_1', $order->billing_address_1 );
		update_user_meta( $user_id, 'billing_address_2', $order->billing_address_2 );
		update_user_meta( $user_id, 'billing_city', $order->billing_city );
		update_user_meta( $user_id, 'billing_company', $order->billing_company );
		update_user_meta( $user_id, 'billing_country', $order->billing_country );
		update_user_meta( $user_id, 'billing_email', $order->billing_email );
		update_user_meta( $user_id, 'billing_first_name', $order->billing_first_name );
		update_user_meta( $user_id, 'billing_last_name', $order->billing_last_name );
		update_user_meta( $user_id, 'billing_phone', $order->billing_phone );
		update_user_meta( $user_id, 'billing_postcode', $order->billing_postcode );
		update_user_meta( $user_id, 'billing_state', $order->billing_state );

		// user's shipping data
		update_user_meta( $user_id, 'shipping_address_1', $order->shipping_address_1 );
		update_user_meta( $user_id, 'shipping_address_2', $order->shipping_address_2 );
		update_user_meta( $user_id, 'shipping_city', $order->shipping_city );
		update_user_meta( $user_id, 'shipping_company', $order->shipping_company );
		update_user_meta( $user_id, 'shipping_country', $order->shipping_country );
		update_user_meta( $user_id, 'shipping_first_name', $order->shipping_first_name );
		update_user_meta( $user_id, 'shipping_last_name', $order->shipping_last_name );
		update_user_meta( $user_id, 'shipping_method', $order->shipping_method );
		update_user_meta( $user_id, 'shipping_postcode', $order->shipping_postcode );
		update_user_meta( $user_id, 'shipping_state', $order->shipping_state );

		// link past orders to this newly created customer
		wc_update_new_customer_past_orders( $user_id );
	}

}

// add this newly created function to the thank you page
add_action( 'woocommerce_thankyou', 'wc_register_guests', 10, 1 );


// Custom Sidebar
// Register and load the widget
function df_load_widget() {
	register_widget( 'df_widget' );
}
add_action( 'widgets_init', 'df_load_widget' );

// Creating the widget
class df_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
			'df_widget',
			// Widget name will appear in UI
			__( 'DealFuel Sidebar Widget', 'df_widget_domain' ),
			// Widget description
			array( 'description' => __( 'Sidebar widget for blog and blog posts', 'df_widget_domain' ) )
		);
	}

	// Creating widget front-end

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// This is where you run the code and display the output
		// echo __( 'Hello, World! DF!!!', 'df_widget_domain' );
		?>
   <div class="deal_sidebar col-xs-12 col-sm-3" style="padding:2% 1%;">
			<?php
				$category_name = get_the_category( get_the_ID() );
				$category      = $category_name[0]->slug;
				$post_title    = get_the_title();
			?>
								  <!--      Code added for cross selling deals by Dinesh on 11th Feb, 2014                -->
			  <br />
			  <div id = "latest_deals">

					  <!-- Code addes by Sagar for new sidebar cross selling -->

					  <?php
									  $args = array(
										  'post_type'      => 'product',
										  'posts_per_page' => 3,
									  );

									  $loop = new WP_Query( $args );

									  while ( $loop->have_posts() ) :

										  $loop->the_post();
										  global $product;

										  if ( has_post_thumbnail() ) :
												  $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'full' );
											  $image_url      = $image_data[0];
											  $image_id       = attachment_url_to_postid( $image_url );
											  $image_alt      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
												  endif;
											?>

							  <div class="col-sm-12 col-xs-12">
									  <a href="<?php echo get_permalink( $loop->post->ID ); ?>" title="<?php echo esc_attr( $loop->post->post_title ? $loop->post->post_title : $loop->post->ID ); ?>">
									  <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="img-responsive imgcard">

											  <div class="row">
											  <div class="col-lg-12 col-md-12" style="margin-top:10px;margin-bottom:10px;     ">
													  <div style="float:left;width:100%;"><span class="intrested"><?php the_title(); ?></span> <span class="intprice"> <?php echo $product->get_price_html(); ?> </span></div>
													  </div>
											  </div>
											  <div class="spacer30" ></div>
									  </a>
							  </div>
										  <?php endwhile; ?>
								  <?php wp_reset_query(); ?>

					  <!-- Code addes by Sagar for new sidebar cross selling -->
			  </div>
		<?php

		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Latest Hot Deals', 'wpb_widget_domain' );
		}
		// Widget admin form
		?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here



function contact_us_action() {

	$toEmailName = get_option( 'blogname' );
	$toEmail     = 'support@dealfuel.com';
	$subject     = $_POST['your_subject'];
	$message     = '';
	$message    .= '<p>' . DEAR . $toEmailName . ',</p>';
	$message    .= '<p>' . NAME . ' : ' . $_POST['your_name'] . ',</p>';
	$message    .= '<p>' . EMAIL . ' : ' . $_POST['your_email'] . ',</p>';
	$message    .= '<p>' . MESSAGE . ' : ' . nl2br( $_POST['your_message'] ) . '</p>';
	$headers     = 'MIME-Version: 1.0' . "\r\n";
	$headers    .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	// Additional headers
	$headers .= 'To: ' . $toEmailName . ' <' . $toEmail . '>' . "\r\n";
	$headers .= 'From: ' . $_POST['your_name'] . ' <' . $_POST['your_email'] . '>' . "\r\n";

	// Mail it
	if ( wp_mail( $toEmail, $subject, $message, $headers ) ) {
		echo 'Your message has been sent successfully. We will get back to you soon.';
	} else {
		echo 'derp';
	}

	die();
}
add_action( 'wp_ajax_contact_us_action', 'contact_us_action' );
add_action( 'wp_ajax_nopriv_contact_us_action', 'contact_us_action' );

function auto_login_new_user( $user_id ) {

	$userdata       = array();
	$userdata['ID'] = $user_id;
	if ( $_POST['password'] != '' ) {
		$userdata['user_pass'] = $_POST['password'];

		$new_user_id = wp_update_user( $userdata );

		wp_set_current_user( $new_user_id );
		wp_set_auth_cookie( $new_user_id );
		// You can change home_url() to the specific URL,such as

		wp_redirect( home_url() . '/checkout' );
		exit(); // always exit
	}
}
add_action( 'user_register', 'auto_login_new_user', 110 );

add_action( 'user_register', 'save_password', 120 );
function save_password( $user_id ) {

    if( ! is_dealclubmembership_in_cart() ) {
        $userdata       = array();
    	$userdata['ID'] = $user_id;

    	if ( isset( $_COOKIE['df_gift_deal_registration'] ) && $_COOKIE['df_gift_deal_registration'] == true ) {
    		unset( $_COOKIE['df_gift_deal_registration'] );
    	} else {

    		if ( $_POST['ws_plugin__s2member_custom_reg_field_user_pass1'] !== '' ) {

    			$user = get_user_by( 'id', $user_id );
    			$user_email    = $user->user_email;
    			$user_password = $_POST['ws_plugin__s2member_custom_reg_field_user_pass1'];
    			$home_url  = home_url();
                $reset_key = get_password_reset_key( $user );
    			$pwsubject = 'Welcome to Dealfuel';
    			$pwmessage = "Hi there,

    Thanks for signing up for Dealfuel! Below you will find all of your account information, keep it somewhere safe.

    Your Site: $home_url/wp-login.php

    Your UserName/Email: $user->user_login
    Reset your password here:" . $home_url . "/account/lost-password/?action=newaccount&key=" . $reset_key . "&login=" . $user->user_login;
    // Reset your password here:" . site_url( 'wp-login.php' ) . '?action=rp&key=' . $reset_key . '&login=' . $user_email ;
    			wp_mail( $user_email, $pwsubject, $pwmessage );
    	    }
     		$new_user_id = wp_update_user( $userdata );
        }
    }

}

// added by sushma for gift functionality including guest checkout

add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );
function my_custom_checkout_field( $checkout ) {

	global $woocommerce;
	$cart_total_oncheckout = $woocommerce->cart->get_cart_total();

	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
		$_product   = $values['data'];
		$product_id = $_product->id;

		$product = wc_get_product( $product_id );

		if ( current_user_can( 's2member_level1' ) or current_user_can( 's2member_level2' ) or current_user_can( 's2member_level3' ) or current_user_can( 's2member_level4' ) or current_user_can( 'administrator' ) ) {
				$price = get_dynamic_price( $product_id );
		} else {
			if ( $product->is_type( 'simple' ) ) {
				$price = $product->get_sale_price();
			} else {
				$price = get_post_meta( $product_id, '_min_variation_price', true );
			}
		}
		if ( $price > 0 ) {
			$valid_gift_product = $product_id;
		} else {
			$valid_gift_product = 0;
		}
	}

	$paid_product_in_cart = conditional_product_in_cart( $valid_gift_product );
}

function conditional_product_in_cart( $product_id ) {
	global $woocommerce;

	$paid_product_in_cart = false;

	if ( $product_id != 0 ) {
		$paid_product_in_cart = true;
	}
	return $paid_product_in_cart;
}

add_action( 'woocommerce_checkout_process', 'my_custom_checkout_field_process_validate' );
function my_custom_checkout_field_process_validate() {
	// Check if set, if its not set add an error.

	if ( isset( $_POST['giftFlag'] ) ) {
		if ( empty( $_POST['email_id'] ) or ( !is_email( wp_unslash( $_POST['email_id'] )) ) or empty( $_POST['sname'] ) or empty( $_POST['rname'] ) or empty( $_POST['msg'] ) ) {
			wc_add_notice( __( 'Please Enter All Fields' ), 'error' );
		}
	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );
function my_custom_checkout_field_update_order_meta( $order_id ) {
	if ( ! empty( $_POST['email_id'] ) and ! empty( $_POST['sname'] ) and ! empty( $_POST['msg'] ) and ! empty( $_POST['rname'] ) ) {

		update_post_meta( $order_id, 'Receiver Email Id', sanitize_text_field( $_POST['email_id'] ) );
		update_post_meta( $order_id, 'Receiver Name', sanitize_text_field( $_POST['rname'] ) );
		update_post_meta( $order_id, 'Sender Name', sanitize_text_field( $_POST['sname'] ) );
		update_post_meta( $order_id, 'personalize Message', sanitize_text_field( $_POST['msg'] ) );
	}
}

add_action( 'woocommerce_order_status_completed', 'give_gift' ); // Executes when a status changes to completed
function give_gift( $order_id ) {
	global $billing_user_id, $receiver_email_id, $gift_order_id;
	$receiver_email_id = get_post_meta( $order_id, 'Receiver Email Id', true );
	$receiver_name     = get_post_meta( $order_id, 'Receiver Name', true );
	$sender_name       = get_post_meta( $order_id, 'Sender Name', true );
	$gift_msg          = get_post_meta( $order_id, 'personalize Message', true );

	$order = new WC_Order( $order_id );

	$billing_email   = $order->get_billing_email();
	if ( email_exists( $billing_email ) == false ) {

		$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
		$user_id         = wp_create_user( $billing_email, $random_password, $billing_email );
	}

	$user            = get_user_by( 'email', $billing_email );
	$billing_user_id = $user->ID;
	if ( ! is_user_logged_in() ) {
		if ( empty( $billing_user_id ) and email_exists( $billing_email ) == false ) {
						$_COOKIE['df_gift_deal_registration'] = true;
			$random_password                                  = wp_generate_password( $length = 12, $include_standard_special_chars = false );
			$billing_user_id                                  = wp_create_user( $billing_email, $random_password, $billing_email );
		} else {
				update_post_meta( $order_id, '_customer_user', $billing_user_id );
		}
	}
	if ( empty( $receiver_email_id ) and empty( $receiver_name ) and empty( $sender_name ) and empty( $gift_msg ) ) {

	    if (isset($_POST['giftFlag'])) {
    		if ( ! empty( $random_password ) ) {

    			$subject  = 'Welcome to DealFuel';
    			$home_url = home_url();
    			$message  = "Hi there,

    			Thanks for signing up for DealFuel! Below you will find all of your account information, keep it somewhere safe.

    			Your Site: https://dealfuel.com/wp-login.php

    			Your UserName/Email: $billing_email
    			Reset your password here: $home_url/wp-login.php?action=lostpassword";

    			wp_mail( $billing_email, $subject, $message );

    			update_post_meta( $order_id, '_customer_user', $billing_user_id );
    		}
	    }
	} else {
		$order_items        = $order->get_items();
		$gift_product_title = '';
		$order_items_count  = count( $order_items );
		$sr_no              = 1;
		foreach ( $order_items as $item ) {
			$product_id   = $item['product_id'];
			$string_title = get_the_title( $product_id );
			if ( $sr_no == $order_items_count ) {
				$separator = '';
			} else {
				$separator = ',  ';
			}
			$gift_product_title .= $string_title . $separator;
			$gift_product_link   = get_permalink();
			$sr_no++;
		}

		$gift_order_id = $order_id;

		 /*start* get one related product to send in email */

						// get Product ids to exclude
						$exclude_product_id = array();
						$customer_orders    = get_posts(
							array(
								'meta_key'    => '_billing_email',
								'meta_value'  => $billing_email,
								'post_type'   => 'shop_order',
								'numberposts' => -1,
							)
						);
		foreach ( $customer_orders as $k => $v ) {
			$cust_order = new WC_Order( $customer_orders[ $k ]->ID );
			 $items     = $cust_order->get_items();
			foreach ( $items as $item ) {
							  $exclude_product_id[] = $item['product_id'];
			}
		}
						// get product ids of current order
						$orders1      = $order->get_items();
						$products_ids = array();
		foreach ( $orders1 as $select_order ) {
			$products_ids[]       = $select_order['product_id'];
			$exclude_product_id[] = $select_order['product_id'];
		}
						// get unique product ids
						$exclude_product_ids = array_unique( $exclude_product_id );
						// get one random product id
						$rand_keys   = array_rand( $products_ids );
						$product_ide = $products_ids[ $rand_keys ];

						$all_term = array();
						$terms    = wp_get_post_terms( $product_ide, 'product_cat' );

						// get categories of product excluding freebies and expired
		foreach ( $terms as $term ) {
			if ( $term->slug != 'freebies' ) {
				 $all_term[] = $term->slug;
			}
		}
						$rand_keys1   = array_rand( $all_term );
						$product_cats = $all_term[ $rand_keys1 ];

						// get product which are not freebies and expired
						$args = array(
							'posts_per_page' => 1,
							'tax_query'      => array(
								'relation' => 'AND',
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'slug',
									'terms'    => $product_cats,
								),
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'slug',
									'terms'    => array( 'freebies' ),
									'operator' => 'NOT IN',
								),
							),
							'post_type'      => 'product',
							'meta_query'     => array(
								array(
									'key'   => 'status',
									'value' => '2',
								),
							),
							'orderby'        => 'rand',
							'post__not_in'   => $exclude_product_ids,
						);

						global $related_prod_title;
						global $related_prod_link;

						$the_query = new WP_Query( $args );
						if ( $the_query->have_posts() ) {
							while ( $the_query->have_posts() ) {
									$the_query->the_post();
									$related_prod_title = get_the_title();
									$related_prod_link  = get_permalink();
							}
						}
						 /* End of get one related product to send in email */

						// GIFT DEAL CODE
						$subject = "Your Gift has been delivered to $receiver_name";
						$headers = array( 'Content-Type: text/html; charset=UTF-8' );
						// for receiver gift email template
						$smessage .= '<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>Thank You</title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width; initial-scale=1.0">

          <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.*/
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #0a8cce;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:220px!important;}
         img[class=colimg2] {width: 440px!important;height:220px!important;}


         }
         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:140px!important;}
         img[class=colimg2] {width: 280px!important;height:140px!important;}
         td[class=mobile-hide]{display:none!important;}
         td[class="padding-bottom25"]{padding-bottom:25px!important;}

         }
      </style>
		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	</head>

	<body>

		<!-- Start of preheader -->

<!-- Start of header -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="header">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                      <table width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <!-- logo -->
                                    <table width="140" align="center" border="0" cellpadding="0" cellspacing="0" class="devicewidth">
                                       <tbody>
                                          <tr>
                                             <td width="169" height="45" align="center">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
                                                   <img src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/dealfuel-llogo.jpg" alt="" border="0" width="250" height="67" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of logo -->
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="14"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of Header -->

<!-- Start of menu -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="menu" mc:edit >
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>

                              <tr>
                                 <td>
                                    <table width="600" align="center" border="0" cellpadding="0" cellspacing="0" class="devicewidth">
                                       <tbody>
                                          <tr>
                                             <td align="center" valign="middle" style="font-family: NotoSans, arial, sans-serif; font-size: 14px;color: #FFFFFF" st-content="viewonline">

                                               <img src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/ribbon.png" />

                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>

                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>

            </table>
         </td>
      </tr>

   </tbody>
</table>
<!-- End of menu -->


<!-- Start Full Text -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="full-text" mc:edit="body">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="background-color: #fff;">

                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="40" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                       <tbody>
                                          <!-- Title -->
                                          <tr>';

						$smessage .= '<td style="font-family: NotoSans, arial, sans-serif;font-style:italic; font-size: 28px; color: #333333; text-align:center; line-height: 30px;" st-title="fulltext-heading">';
						$smessage .= "Hello $sender_name";
						$smessage .= '</td>';
						$smessage .= '</tr>
                                          <!-- End of Title -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: NotoSans, arial, sans-serif; font-size: 16px; color: #000000; text-align:center; line-height: 30px;" st-content="fulltext-content">';
						$smessage .= 'Thank you for gifting from DealFuel.com<br>';
						$smessage .= "$gift_product_title";
						if ( ! empty( $random_password ) ) {
							$smessage .= "Your DealFuel Credentials:<br>
			Username:  $billing_email<br>
			Password: $random_password";
						}
						$smessage .= '</td>
                                          </tr>
                                          <!-- End of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of full text -->
<!-- Start of main-banner -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="banner">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                       <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="background-color: #fff;">
                           <tbody>
                              <tr>
                                 <!-- start of image -->
                                 <td align="center" st-image="banner-image">
                                    <div class="imgpop">
                                       <a target="_blank" href="#"><img mc:edit style="max-width:600px;" width="600" border="0" height="272" alt="" border="0" style="display:block; border:none; outline:none; text-decoration:none;" src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/thank-u.png" class="banner"></a>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <!-- end of image -->
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of main-banner -->
<!-- Start Full Text -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="full-text" mc:edit="body">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="background-color: #fff;">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="40" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                       <tbody>';

						$smessage .= '<tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: notosans, arial, sans-serif; font-size: 16px; color: #000000; text-align:center; line-height: 30px;" st-content="fulltext-content">';
						$smessage .= 'Here is the next perfect gifting option:<br>';

						$smessage .= "<a href='$related_prod_link' style='color: #557da1; font-weight: normal; text-decoration: none;'>$related_prod_title</a>";

						$smessage .= '</td>
                                          </tr>
                                          <!-- End of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of full text -->

<!-- Start of Footer -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="banner">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                       <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth"  style="background-color: #ffffff;">
                           <tbody>
                              <tr>
                                 <!-- start of image -->
                                 <td align="center" st-image="banner-image">
                                    <div class="imgpop">
                                       <a target="_blank" href="#"><img mc:edit style="max-width:600px;" width="600" border="0" height="47" alt="" border="0" style="display:block; border:none; outline:none; text-decoration:none;" src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/footer.png" ></a>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <!-- end of image -->
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of Footer -->



	</body>
</html>';

						wp_mail( $billing_email, $subject, $smessage, $headers );

						$user         = get_user_by( 'email', $receiver_email_id );
						$gift_user_id = $user->ID;

						if ( empty( $gift_user_id ) and email_exists( $receiver_email_id ) == false ) {
										$_COOKIE['df_gift_deal_registration'] = true;
							$receiver_password                                = wp_generate_password( $length = 12, $include_standard_special_chars = false );
							$gift_user_id                                     = wp_create_user( $receiver_email_id, $receiver_password, $receiver_email_id );
						}

						if ( ! is_user_logged_in() ) {
							update_post_meta( $order_id, '_customer_user', $gift_user_id );
							delete_post_meta( $order_id, '_billing_email', $billing_email );
						} else {
							update_post_meta( $order_id, '_customer_user', $gift_user_id );
						}

						$subject = "You have just received a gift from $sender_name";
						$headers = array( 'Content-Type: text/html; charset=UTF-8' );

						$rmessage .= '<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<meta name="description" content="">

		<meta name="author" content="">

		<meta name="viewport" content="width=device-width; initial-scale=1.0">



          <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.*/
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #0a8cce;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:220px!important;}
         img[class=colimg2] {width: 440px!important;height:220px!important;}


         }
         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:140px!important;}
         img[class=colimg2] {width: 280px!important;height:140px!important;}
         td[class=mobile-hide]{display:none!important;}
         td[class="padding-bottom25"]{padding-bottom:25px!important;}

         }
      </style>
		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	</head>

	<body>

		<!-- Start of preheader -->

<!-- Start of header -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="header">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                      <table width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <!-- logo -->
                                    <table width="140" align="center" border="0" cellpadding="0" cellspacing="0" class="devicewidth">
                                       <tbody>
                                          <tr>
                                             <td width="169" height="45" align="center">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
                                                   <img src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/dealfuel-llogo.jpg" alt="" border="0" width="250" height="67" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of logo -->
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="14"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of Header -->

<!-- Start of menu -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="menu" mc:edit >
   <tbody>






      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>

                              <tr>
                                 <td>
                                    <table width="600" align="center" border="0" cellpadding="0" cellspacing="0" class="devicewidth">
                                       <tbody>
                                          <tr>
                                             <td align="center" valign="middle" style="font-family: notosans, arial, sans-serif; font-size: 14px;color: #FFFFFF" st-content="viewonline">

                                               <img src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/ribbon.png" />


                                             </td>

                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>

                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>

   </tbody>
</table>
<!-- End of menu -->

<!-- Start Full Text -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="full-text" mc:edit="body">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="background-color: #fff;">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="40" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                       <tbody>
                                          <!-- Title -->
                                          <tr>
                                             <td style="font-family: notosans, arial, sans-serif;font-style:italic; font-size: 28px; color: #333333; text-align:center; line-height: 30px;" st-title="fulltext-heading">';

						$rmessage .= " Hello $receiver_name";
						$rmessage .= '</td>
                                          </tr>
                                          <!-- End of Title -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: notosans, arial, sans-serif; font-size: 16px; color: #000000; text-align:center; line-height: 30px;" st-content="fulltext-content">';
						$rmessage .= "You have received a gift from $sender_name <br>This gift can be redeemed at https://dealfuel.com";
						$rmessage .= '             </td>
                                          </tr>
                                          <!-- End of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>

                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of full text -->
<!-- Start of main-banner -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="banner">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                       <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="background-color: #fff;">
                           <tbody>
                              <tr>
                                 <!-- start of image -->
                                 <td align="center" st-image="banner-image">
                                    <div class="imgpop">
                                       <a target="_blank" href="#"><img mc:edit style="max-width:600px;" width="600" border="0" height="272" alt="" border="0" style="display:block; border:none; outline:none; text-decoration:none;" src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/gift-for-u.png" class="banner"></a>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <!-- end of image -->
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of main-banner -->

<!-- Start Full Text -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="full-text">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" align="left" class="devicewidth" style="background-color: #ffffff;">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="560" align="left" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                       <tbody>


                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td mc:edit style="font-family: notosans, arial, sans-serif; font-size: 14px; color: #666666; text-align:center; line-height: 30px;" st-content="fulltext-content">';
						if ( empty( $receiver_password ) ) {
							$rmessage .= "To: $receiver_name<br>
			From: $sender_name<br>
			Message: $gift_msg<br>

			<h3>Gift: $gift_product_title</h3>";
						} else {
							$rmessage .= "To: $receiver_name<br>
			From: $sender_name<br>
			Message: $gift_msg<br>

			<h3>Gift: $gift_product_title</h3>

			Your DealFuel Credentials:<br>
			Username:  $receiver_email_id<br>
			Password: $receiver_password";
						}
						$rmessage .= ' </td>
                                          </tr>';

						$rmessage .= ' <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->

                                          <!-- End of content -->

                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of full text -->

<!-- Start of seperator -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="seperator">
   <tbody>
      <tr>
         <td>
            <table width="600" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" style="background-color: #ffffff;">
               <tbody>
               	 <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                  <tr>
                     <td mc:edit width="400" align="center" height="30" style="font-size:12px;font-family:NotoSans, Helvetica, sans-serif; line-height:20px;padding:0 15px;">
							To redeem your purchase, please login to your DealFuel account and go to your "My Account". You will be able to see your gift under the "Purchase History" area. You will also receive an email from DealFuel giving you the download instructions.
<br><br>
If your gift is a credit note, it will simply be reflected as credits when you login to your account.
<br><br>
In case of any queries, please send us a mail at support@dealfuel.com
<br><br>
<b style="font-size: 18px;">Enjoy your gift.<br>
Wishing you Happy Holidays</b>

                     </td>

                  </tr>
                    <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of seperator -->
<!-- Start of main-banner -->
<table width="100%" bgcolor="#f7f7f7" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="banner">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                       <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth"  style="background-color: #ffffff;">
                           <tbody>
                              <tr>
                                 <!-- start of image -->
                                 <td align="center" st-image="banner-image">
                                    <div class="imgpop">
                                       <a target="_blank" href="#"><img mc:edit style="max-width:600px;" width="600" border="0" height="47" alt="" border="0" style="display:block; border:none; outline:none; text-decoration:none;" src="https://dealfuel.com/wp-content/themes/dealfuel2015/images/footer.png" ></a>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <!-- end of image -->
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of main-banner -->
	</body>
</html>';

						wp_mail( $receiver_email_id, $subject, $rmessage, $headers );
						$WC_Order = create_order( $args );

	}
}

// create order
function create_order( $args = array() ) {
	global $billing_user_id, $gift_order_id, $receiver_email_id;
	$default_args = array(
		'status'        => '',
		'customer_id'   => null,
		'customer_note' => null,
		'order_id'      => 0,
		'created_via'   => '',
		'parent'        => 0,
	);

	$args       = wp_parse_args( $args, $default_args );
	$order_data = array();

	if ( $args['order_id'] > 0 ) {
		$updating         = true;
		$order_data['ID'] = $args['order_id'];
	} else {
		$updating                    = false;
		$order_data['post_type']     = 'shop_order';
		$order_data['post_status']   = 'wc-' . apply_filters( 'woocommerce_default_order_status', 'completed' );
		$order_data['ping_status']   = 'closed';
		$order_data['post_author']   = 1;
		$order_data['post_password'] = uniqid( 'order_' );
		$order_data['post_title']    = sprintf( __( 'Order &ndash; %s', 'woocommerce' ), strftime( _x( '%1$b %2$d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'woocommerce' ) ) );

		$order_data['post_parent'] = absint( $args['parent'] );
	}

	if ( $args['status'] ) {
		if ( ! in_array( 'wc-' . $args['status'], array_keys( wc_get_order_statuses() ) ) ) {
			return new WP_Error( 'woocommerce_invalid_order_status', __( 'Invalid order status', 'woocommerce' ) );
		}
		$order_data['post_status'] = 'wc-' . $args['status'];
	}

	if ( ! is_null( $args['customer_note'] ) ) {
		$order_data['post_excerpt'] = $args['customer_note'];
	}

	if ( $updating ) {
		$order_id = wp_update_post( $order_data );
	} else {
		$order_id = wp_insert_post( apply_filters( 'woocommerce_new_order_data', $order_data ), true );
	}

	if ( is_wp_error( $order_id ) ) {
		return $order_id;
	}

	if ( ! $updating ) {
		update_post_meta( $order_id, '_order_key', 'wc_' . apply_filters( 'woocommerce_generate_order_key', uniqid( 'order_' ) ) );

		update_post_meta( $order_id, '_order_currency', get_woocommerce_currency() );
		update_post_meta( $order_id, '_prices_include_tax', get_option( 'woocommerce_prices_include_tax' ) );
		update_post_meta( $order_id, '_customer_ip_address', WC_Geolocation::get_ip_address() );
		update_post_meta( $order_id, '_customer_user_agent', isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );
		update_post_meta( $order_id, '_customer_user', 0 );
		update_post_meta( $order_id, '_created_via', sanitize_text_field( $args['created_via'] ) );
	}

	update_post_meta( $order_id, '_customer_user', $billing_user_id );

	update_post_meta( $order_id, '_order_version', WC_VERSION );

	$item_id = woocommerce_add_order_item(
		$order_id,
		array(
			'order_item_name' => 'gifted deal to ' . " $receiver_email_id " . 'order_id_' . "$gift_order_id",
			'order_item_type' => 'line_item',
		)
	);

	if ( $item_id ) {

		foreach ( $metavalues as $key => $value ) {
			woocommerce_add_order_item_meta( $item_id, $key, $value );
		}
	}

	return wc_get_order( $order_id );
}

/*
 * Site Tracking Code
 */

if ( ! function_exists( 'tt_trackingcode' ) ) {
	function tt_trackingcode() {
		global $smof_data;
		if ( $smof_data['site_analytics'] ) {
			echo $smof_data['site_analytics'];
		}
	}
}


if ( ! function_exists( 'tt_site_logo' ) ) {
	function tt_site_logo() {
		echo '<div id="logo"><a href="https://www.dealfuel.com"><img src="https://www.dealfuel.com/wp-content/uploads/2018/04/logo.png" alt="DealFuel" class=""></a></div>';
	}
}

// add_filter( 'woocommerce_shortcode_products_query' , 'bbloomer_exclude_cat_shortcodes');

function bbloomer_exclude_cat_shortcodes( $query_args ) {

	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'Freebies' ), // Don't display products from this category
			'operator' => 'NOT IN',
		),
	);

	return $query_args;
}

add_action( 'wp_head', 'get_ajax_url' );
function get_ajax_url() {
	?>
		<script>
				var ajaxurl="<?php echo admin_url( 'admin-ajax.php' ); ?>";
		</script>
		<!-- Moosend Homepage script -->
		<script>if(!window.mootrack){ !function(t,n,e,o,a){function d(t){var n=~~(Date.now()/3e5),o=document.createElement(e);o.async=!0,o.src=t+"?ts="+n;var a=document.getElementsByTagName(e)[0];a.parentNode.insertBefore(o,a)}t.MooTrackerObject=a,t[a]=t[a]||function(){return t[a].q?void t[a].q.push(arguments):void(t[a].q=[arguments])},window.attachEvent?window.attachEvent("onload",d.bind(this,o)):window.addEventListener("load",d.bind(this,o),!1)}(window,document,"script","https://cdn.stat-track.com/statics/moosend-tracking.min.js","mootrack"); } mootrack('loadForm', '6a0deb7831354ad9bf2f8c097b18aebb');</script>

		<!-- Moosend Expired Deal Page-->
		<script>if(!window.mootrack){ !function(t,n,e,o,a){function d(t){var n=~~(Date.now()/3e5),o=document.createElement(e);o.async=!0,o.src=t+"?ts="+n;var a=document.getElementsByTagName(e)[0];a.parentNode.insertBefore(o,a)}t.MooTrackerObject=a,t[a]=t[a]||function(){return t[a].q?void t[a].q.push(arguments):void(t[a].q=[arguments])},window.attachEvent?window.attachEvent("onload",d.bind(this,o)):window.addEventListener("load",d.bind(this,o),!1)}(window,document,"script","https://cdn.stat-track.com/statics/moosend-tracking.min.js","mootrack"); } mootrack('loadForm', '9fd6a32de1784c7bb1a76bf144fac26d');</script>

		<!-- Moosend Expired Deal Sidebar -->
		<script>if(!window.mootrack){ !function(t,n,e,o,a){function d(t){var n=~~(Date.now()/3e5),o=document.createElement(e);o.async=!0,o.src=t+"?ts="+n;var a=document.getElementsByTagName(e)[0];a.parentNode.insertBefore(o,a)}t.MooTrackerObject=a,t[a]=t[a]||function(){return t[a].q?void t[a].q.push(arguments):void(t[a].q=[arguments])},window.attachEvent?window.attachEvent("onload",d.bind(this,o)):window.addEventListener("load",d.bind(this,o),!1)}(window,document,"script","https://cdn.stat-track.com/statics/moosend-tracking.min.js","mootrack"); } mootrack('loadForm', 'c3d9d5e560734a66ba99d9292836ae28');</script>

		<?php
}

// define the woocommerce_paypal_args callback
function filter_woocommerce_paypal_args( $array ) {
	for ( $sr = 1;$sr <= count( $array );$sr++ ) {
		if ( ! isset( $array[ 'item_name_' . $sr ] ) ) {
			break;
		}
		$array[ 'item_name_' . $sr ] = $array[ 'item_name_' . $sr ] . ' (Dealfuel.com)';
	}
	return $array;
};
add_filter( 'woocommerce_paypal_args', 'filter_woocommerce_paypal_args', 10, 1 );

// woocommerce_paypal_digital_goods_nvp_args
function woohack_change_item_name( $paypal_object ) {
	foreach ( $paypal_object['items'] as $key => $paypal_item ) {
		$paypal_object['items'][ $key ]['item_name'] = $paypal_item ['item_name'] . ' (Dealfuel.com)';
	}

	return $paypal_object;

}
add_filter( 'woocommerce_paypal_digital_goods_nvp_args', 'woohack_change_item_name', 10, 1 );


add_action( 'woocommerce_product_options_general_product_data', 'df_add_product_vendor_settings' );

function df_add_product_vendor_settings() {
	global $post, $woocommerce;
	$html = '';

		$sale_price = get_post_meta( $post->ID, '_sale_price', true );

	if ( ! empty( $sale_price ) ) {

		$round      = true;
		$sale_price = $round ? round( $sale_price, 2 ) : $sale_price;

		$commission = get_post_meta( $post->ID, '_product_vendors_commission_' . $sale_price, true );

		$html = '<div class="options_group">
							<p class="form-field _product_vendors_commission_field">
							<label for="_product_vendors_commission">' . __( 'Vendor Commission for $' . $sale_price, 'wc_product_vendors' ) . '</label>
							<input class="short" size="6" placeholder="0" type="number" step="any" name="_product_vendors_commissions[' . $sale_price . ']" id="_product_vendors_commission_' . $price . '" value="' . $commission . '" />&nbsp;&nbsp;%
							<span class="description">' . __( 'OPTIONAL: Enter the percentage of the sale price that will go to each product vendor. If no value is entered then the vendor\'s default commission will be used.', 'wc_product_vendors' ) . '</span>
							</p>
						</div>';
	}
		$prices = get_all_dynamic_prices( $post->ID );

	foreach ( $prices as $price ) {

		$round = true;
		$price = $round ? round( $price, 2 ) : $price;

		$commission = get_post_meta( $post->ID, '_product_vendors_commission_' . $price, true );
		$html      .= '<div class="options_group">
						<p class="form-field _product_vendors_commission_field">
						<label for="_product_vendors_commission">' . __( 'Vendor Commission for $' . $price, 'wc_product_vendors' ) . '</label>
						<input class="short" size="6" placeholder="0" type="number" step="any" name="_product_vendors_commissions[' . $price . ']" id="_product_vendors_commission_' . $price . '" value="' . $commission . '" />&nbsp;&nbsp;%
						<span class="description">' . __( 'OPTIONAL: Enter the percentage of the sale price that will go to each product vendor. If no value is entered then the vendor\'s default commission will be used.', 'wc_product_vendors' ) . '</span>
						</p>
					</div>';

	}

		echo $html;

}


add_action( 'woocommerce_process_product_meta', 'df_process_vendor_product_settings', 10, 2 );

function df_process_vendor_product_settings( $post_id, $post ) {

	foreach ( $_POST['_product_vendors_commissions'] as $k => $id ) {
		if ( isset( $_POST['_product_vendors_commissions'][ $k ] ) ) {
			$commission = $_POST['_product_vendors_commissions'][ $k ];

			$round = true;
			$k     = $round ? round( $k, 2 ) : $k;

			update_post_meta( $post_id, '_product_vendors_commission_' . $k, $commission );
		}
	}

}

add_action( 'woocommerce_product_after_variable_attributes', 'df_product_vendor_add_variation_settings', 10, 3 );
/**
 * Add vendor settings to product variations
 *
 * @param int $loop           Current variation loop
 * @param obj $variation_data Variation data object
 */

function df_product_vendor_add_variation_settings( $loop, $variation_data, $variation ) {

	$price = $variation_data['_sale_price'];

	// Check if $price is an array and retrieve the first value
    if ( is_array( $price ) ) {
        $price = reset( $price );
    }

	$round = true;
	$price = $round ? round( $price, 2 ) : $price;

	$vendor_meta_key = '_product_vendors_commission_' . $price;
	$commission      = get_post_meta( $variation->ID, $vendor_meta_key, true );

	if ( ! empty( $price ) ) {

		$commission = isset( $commission ) ? $commission : '';

		$html = '<tr>
	                        <td>
	                            <div class="_product_vendors_commission">
	                                <label for="_product_vendors_commission_' . $loop . '">' . __( 'Vendor Commission for $' . $price, 'wc_product_vendors' ) . ':</label>
	                                <input size="4" type="number" step="any" name="variable_product_vendors_commission_[' . $loop . '][' . $price . ']" id="_product_vendors_commission_' . $price . '_' . $loop . '" value="' . $commission . '" />
	                				<span class="description">' . __( 'OPTIONAL: Enter the percentage that will go to each product vendor.', 'wc_product_vendors' ) . '</span>
	                            </div>
	                        </td>
	                    </tr>
	             ';

		echo $html;
	}
}

add_action( 'woocommerce_process_product_meta_variable', 'df_product_vendor_process_variation_settings', 10, 1 );
/**
 * Update product variation settings
 *
 * @return void
 */
function df_product_vendor_process_variation_settings( $post_id ) {

	if ( isset( $_POST['variable_post_id'] ) && is_array( $_POST['variable_post_id'] ) ) {

		foreach ( $_POST['variable_post_id'] as $k => $id ) {

			$price = $_POST['variable_sale_price'][ $k ];

			$round = true;
			$price = $round ? round( $price, 2 ) : $price;

			$commission = $_POST['variable_product_vendors_commission_'][ $k ];

			foreach ( $commission as $key => $value ) {
				$commission = $value;
			}

			update_post_meta( $id, '_product_vendors_commission_' . $price, $commission );

		}
	}

}

function get_all_dynamic_prices( $postid ) {

	$pricing_rules = get_post_meta( $postid, '_pricing_rules', 'true' );

	$prices = array();
	$i      = 0;
	if ( ! empty( $pricing_rules ) ) {
		foreach ( $pricing_rules as $key => $item ) {

			$prices[ $i ] = $pricing_rules[ $key ][ 'rules' ][1][ 'amount' ];


			$i++;

		}
	}
	return $prices;
}


/*
 * Function to get dynamicPrice/plusPrice with id as key and 2 digit decimal format.
 */
function get_all_dynamic_prices_with_id_as_key( $postid ) {

	$pricing_rules = get_post_meta( $postid, '_pricing_rules', 'true' );
	$prices = array();
	$i      = 0;

	// error_log('Inside get_all_dynamic_prices_with_id_as_key ');
	// error_log(print_r($pricing_rules,true));

	if ( ! empty( $pricing_rules ) ) {
		foreach ( $pricing_rules as $key => $item ) {

			$variation_id_key = $pricing_rules[ $key ]['variation_rules']['args']['variations'][0];
			$membership_type = $pricing_rules[ $key]['conditions'][1]['args']['memberships'][0];

			$regular_price = (float)get_post_meta($variation_id_key, '_regular_price', true);
			$sale_price    = (float)get_post_meta($variation_id_key, '_sale_price', true);
			$dc_price    = (float)$pricing_rules[ $key ]['rules'][1]['amount'];

			//fetching the dynaminc price on the basis of membership

			if($membership_type==174761 || $membership_type==174765){


				if( $membership_type==174765 ){

					$dynamic_price_array_annual = $pricing_rules[ $key ]['rules']['1']['amount'];

				}else {

					$dynamic_price_array_monthly = $pricing_rules[ $key ]['rules']['1']['amount'];

				}

			}

			$price_difference = $sale_price - $plus_price;

			$prices[ $variation_id_key ]['regular_price'] = "$" . number_format($regular_price,2 ,'.', '');
			$prices[ $variation_id_key ]['sale_price']    = "$" . number_format($sale_price,2 ,'.', '');
			$prices[ $variation_id_key ]['dc_price']    = "$" . number_format($dc_price,2 ,'.', '');
			$prices[ $variation_id_key ]['price_difference'] = number_format($price_difference,2 ,'.', '');
			$prices[ $variation_id_key ]['dynamic_price_array_annual']    = "$" . number_format($dynamic_price_array_annual,2 ,'.', '');
			$prices[ $variation_id_key ]['dynamic_price_array_monthly']    = "$" . number_format($dynamic_price_array_monthly,2 ,'.', '');
			$i++;

		}
	}
	return $prices;
}
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

function dealfuel_change_empty_cart_button_url() {
	return get_site_url() . '/all-deals/';

	// Can use any page instead, like return '/sample-page/';
}


// To change the status of orders
add_filter( 'woocommerce_payment_complete_order_status', 'rfvc_update_order_status', 10, 2 );

function rfvc_update_order_status( $order_status, $order_id ) {

	$order = new WC_Order( $order_id );

	if ( 'processing' == $order_status && ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {
		return 'completed';
	}

	return $order_status;
}

// To remove the tabs in product page
add_filter( 'woocommerce_product_tabs', 'df_remove_product_tabs', 98 );

function df_remove_product_tabs( $tabs ) {
	unset( $tabs['additional_information'] );
	unset( $tabs['vendor'] );
	return $tabs;
}

/** get license keys under acccout area */
if ( ! function_exists( 'get_downlaods_license_keys_df' ) ) {

	function get_downlaods_license_keys_df( $product_id, $order_id ) {

		global $wpdb,$product;
		if ( ! empty( $product_id ) && ! empty( $order_id ) ) {

			$license_key_arr = array();
			$query           = "SELECT *  FROM `df_new_license_keys` WHERE `post_id`= $product_id AND `order_id`= $order_id";
			// echo $query;
			$result = $wpdb->get_results( $query );
			$i      = 0;

			foreach ( $result as $key => $val ) {

				$product                               = wc_get_product( $product_id );
				$license_key_arr[ $i ]['license_key']  = $val->license_keys;
				$license_key_arr[ $i ]['product_name'] = $product->get_title();
				$i++;
			}
			return $license_key_arr;
		} else {
			return;
		}

	}
}

/* Code added by Prajakti for courses */

// code for creating custom post type 'courses'.
add_action( 'init', 'create_post_type_courses' );
function create_post_type_courses() {
	register_post_type(
		'course',
		array(
			'labels'      => array(
				'name'               => 'Courses',
				'singular_name'      => 'Course',
				'add_new'            => 'Add New Course',
				'add_new_item'       => 'Add New Course',
				'edit_item'          => 'Edit Course',
				'new_item'           => 'New Course',
				'all_items'          => 'All Courses',
				'view_item'          => 'View Courses',
				'search_items'       => 'Search Courses',
				'not_found'          => 'No Courses Found',
				'not_found_in_trash' => 'No Courses found in Trash',
				'parent_item_colon'  => '',
				'menu_name'          => 'Courses',
			),
			'public'      => true,
			'has_archive' => true,
			'supports'    => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions', 'page-attributes', 'post-formats' ),
		)
	);
}

// Add metabox for adding Price for course
add_action( 'init', 'course_price_meta_box_setup' );
function course_price_meta_box_setup() {
	add_action( 'add_meta_boxes', 'add_course_price' );
	add_action( 'save_post', 'course_save_price_class_meta', 10, 2 );
}
function add_course_price() {
	add_meta_box(
		'course-price',      // Unique ID
		esc_html__( 'Course Price', '' ),    // Title
		'course_price_post_class_meta_box',   // Callback function
		'course',         // Admin page (or post type)
		'side',         // Context
		'default'         // Priority
	);
}

function course_price_post_class_meta_box( $object, $box ) {
	// Callback function
	?>
	<?php wp_nonce_field( basename( __FILE__ ), 'course_price_class_nonce' ); ?>
 <p>
	 <label for="course-price-post-class"><?php _e( 'Add Course Price', '' ); ?></label>
	 <br />
	 <input type="text" class="widefat" name="course-price-post-class" id="course-price-post-class" size="3" value="<?php echo esc_attr( get_post_meta( $object->ID, 'course-price', true ) ); ?>" />
   </p>
	<?php
}

function course_save_price_class_meta( $post_id, $post ) {
	if ( ! isset( $_POST['course_price_class_nonce'] ) || ! wp_verify_nonce( $_POST['course_price_class_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}
	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}
	$new_meta_value = ( isset( $_POST['course-price-post-class'] ) ? sanitize_html_class( $_POST['course-price-post-class'] ) : '' );
	$meta_key       = 'course-price';
	$meta_value     = get_post_meta( $post_id, $meta_key, true );
	if ( $new_meta_value && '' == $meta_value ) {
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	} elseif ( '' == $new_meta_value && $meta_value ) {
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

// Add metabox for adding author for course
add_action( 'init', 'course_author_meta_box_setup' );
function course_author_meta_box_setup() {
	add_action( 'add_meta_boxes', 'add_course_author' );
	add_action( 'save_post', 'course_save_author_class_meta', 10, 2 );
}
function add_course_author() {
	add_meta_box(
		'course-author',      // Unique ID
		esc_html__( 'Course author', '' ),    // Title
		'course_author_post_class_meta_box',   // Callback function
		'course',         // Admin page (or post type)
		'side',         // Context
		'default'         // Priority
	);
}
function course_author_post_class_meta_box( $object, $box ) {
	// Callback function
	?>
	<?php wp_nonce_field( basename( __FILE__ ), 'course_author_class_nonce' ); ?>
 <p>
	 <label for="course-author-post-class"><?php _e( 'Add Course author', '' ); ?></label>
	 <br />
	 <input type="text" class="widefat" name="course-author-post-class" id="course-author-post-class" size="3" value="<?php echo esc_attr( get_post_meta( $object->ID, 'course-author', true ) ); ?>" />
   </p>
	<?php
}

function course_save_author_class_meta( $post_id, $post ) {
	if ( ! isset( $_POST['course_author_class_nonce'] ) || ! wp_verify_nonce( $_POST['course_author_class_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}
	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}
	$new_meta_value = ( isset( $_POST['course-author-post-class'] ) ? sanitize_html_class( $_POST['course-author-post-class'] ) : '' );
	$meta_key       = 'course-author';
	$meta_value     = get_post_meta( $post_id, $meta_key, true );
	if ( $new_meta_value && '' == $meta_value ) {
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	} elseif ( '' == $new_meta_value && $meta_value ) {
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

add_action( 'edit_form_after_editor', 'add_course_desc_curriculam' );
add_action( 'save_post', 'save_course_desc_curriculam', 10, 2 );

function add_course_desc_curriculam() {
	 global $post;
	if ( 'course' != $post->post_type ) {
		return;
	}

	$editor1 = get_post_meta( $post->ID, 'course-description', true );
	$editor2 = get_post_meta( $post->ID, 'course-curriculam', true );

	wp_nonce_field( plugin_basename( __FILE__ ), 'wspe_114084' );

	echo '<h2>Course Description</h2>';
	echo wp_editor( $editor1, 'custom_editor_1', array( 'textarea_name' => 'custom_editor_1' ) );
	echo '<h2>Course Curriculam</h2>';
	echo wp_editor( $editor2, 'custom_editor_2', array( 'textarea_name' => 'custom_editor_2' ) );
}

function save_course_desc_curriculam( $post_id, $post_object ) {
	if ( ! isset( $post_object->post_type ) || 'course' != $post_object->post_type ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['wspe_114084'] ) || ! wp_verify_nonce( $_POST['wspe_114084'], plugin_basename( __FILE__ ) ) ) {
		return;
	}

	if ( isset( $_POST['custom_editor_1'] ) ) {
		update_post_meta( $post_id, 'course-description', $_POST['custom_editor_1'] );
	}

	if ( isset( $_POST['custom_editor_2'] ) ) {
		update_post_meta( $post_id, 'course-curriculam', $_POST['custom_editor_2'] );
	}
}
/*** End of code for courses */

// add custom meta box
add_action( 'save_post', 'deal_info' );

function deal_info() {
	global $post;
	$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
	update_post_meta( $post->ID, 'Redirection_url', get_post_meta( $post->ID, 'Redirection_url', true ) );
	update_post_meta( $post->ID, 'Course_price', get_post_meta( $post->ID, 'Course_price', true ) );
	update_post_meta( $post->ID, 'Course_author', get_post_meta( $post->ID, 'Course_author', true ) );
	update_post_meta( $post->ID, 'Dealclub_Redirection_url', get_post_meta( $post->ID, 'Dealclub_Redirection_url', true ) );
	update_post_meta( $post->ID, 'page_id', get_post_meta( $post->ID, 'page_id', true ) );
	update_post_meta( $post->ID, 'deal_template', get_post_meta( $post->ID, 'deal_template', true ) );
	update_post_meta( $post->ID, 'deal_terms', get_post_meta( $post->ID, 'deal_terms', true ) );
	if ( 'tpl_dealclub_trial.php' == $page_template ) {
		update_post_meta( $post->ID, 'paypal_button_shortcode', get_post_meta( $post->ID, 'paypal_button_shortcode', true ) );
		update_post_meta( $post->ID, 'stripe_button_shortcode', get_post_meta( $post->ID, 'stripe_button_shortcode', true ) );
		update_post_meta( $post->ID, 'payment_condition', get_post_meta( $post->ID, 'payment_condition', true ) );
	}
}


function dequeue_my_css() {
	wp_dequeue_style( 'astra-addon-css-4' );
	wp_deregister_style( 'astra-addon-css-4' );
	wp_dequeue_style( 'astra-addon-css-9' );
	wp_deregister_style( 'astra-addon-css-9' );
	wp_dequeue_style( 'hfe-style' );
	wp_deregister_style( 'hfe-style' );
	wp_dequeue_style( 'elementor-icons-bc' );
	wp_deregister_style( 'elementor-icons-bc' );
}
add_action( 'wp_enqueue_scripts', 'dequeue_my_css', 100 );

/* Code to save the site from Contact Form Resources Loading */
add_filter( 'wpcf7_load_js', '__return_false' ); // Disable CF7 JavaScript
add_filter( 'wpcf7_load_css', '__return_false' ); // Disable CF7 CSS

// Load only on the contact page
add_action( 'wp_enqueue_scripts', 'load_wpcf7_scripts' );
function load_wpcf7_scripts() {
	if ( is_page( 'contact' ) ) {
		if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
		}
		if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
			wpcf7_enqueue_styles();
		}
	}
}

// Code to change points and rewards number of events
add_filter( 'wc_points_rewards_my_account_points_events', 'filter_wcpr_my_account_points_events', 10, 2 );
function filter_wcpr_my_account_points_events( $events, $user_id ) {
	return 20; // Change to 20 instead of 5
}


add_filter( 'woocommerce_coupon_is_valid', 'DF_membership_coupon_validation', 10, 2 );

function DF_membership_coupon_validation( $result, $coupon ) {

	if ( 'dcspecial25' === strtolower( $coupon->get_code() ) ) {
		return plugin_is_user_new();
	}

	return true;
}

function plugin_is_user_new() {

	// get date object of the time the user registered
	$user_id = get_current_user_id();
	$udata   = get_userdata( $user_id );

	$registered_date = new DateTime( $udata->user_registered );

	// calculate the date 20 days ago
	$twenty_days_ago = new DateTime( '-20 days' );

	$user_status = 0;
	if ( wc_memberships_is_user_member( $user_id, 'sub_yearly' ) || wc_memberships_is_user_member( $user_id, 'sub_quarterly' ) || wc_memberships_is_user_member( $user_id, 'sub_monthly' ) ) {

		$user_status = 1;
	}

	// if($user_status==0)
	return( $user_status > 0 );
	// compare (return true if registered after 20 days ago)
	// return ($registered_date > $twenty_days_ago);
}

/** Disable Ajax Call from WooCommerce */
add_action( 'wp_enqueue_scripts', 'dequeue_woocommerce_cart_fragments', 11 );
function dequeue_woocommerce_cart_fragments() {
	if ( is_front_page() ) {
		wp_dequeue_script( 'wc-cart-fragments' );
	} }

add_action( 'woocommerce_order_refunded', 'action_woocommerce_order_refunded', 10, 2 );
function action_woocommerce_order_refunded( $order_id, $refund_id ) {
	// Get the WC_Order Object instance (from the order ID)
	$order = wc_get_order( $order_id );

	// Get the Order refunds (array of refunds)
	$order_refunds = $order->get_refunds();

	// Loop through the order refunds array
	foreach ( $order_refunds as $refund ) {
		// Loop through the order refund line items

		foreach ( $refund->get_items() as $item_id => $item ) {

			global $wpdb;

			$results_comm = $wpdb->get_results(
				"SELECT *
    FROM  `wp_wcpv_commissions` WHERE `order_id` = $order_id ",
				ARRAY_A
			);
			$var_post_id  = '';

			// error_log(print_r($results_comm, true));
			foreach ( $results_comm as $row ) {

				if ( $row['variation_id'] == 0 ) {
					$key_product = $row['product_id'];
				} else {
					$key_product = $row['variation_id'];
				}

				$refunded_product = $item->get_product_id();

				if ( $row['product_id'] == $refunded_product ) {
					   // update_post_meta( $var_post_id, '_commission_amount', 0 );
					   // update_post_meta( $var_post_id, '_processor_fee', 0 );

					$table_name = 'wp_wcpv_commissions';

					/*
					$wpdb->update( $table_name, array(
					'product_tax_amount' => 0,
					'product_commission_amount' => 0,
					'total_commission_amount' => 0
					),
					array('order_id' => $row['order_id'], 'product_id' => $row['product_id'])
					);*/

					$wpdb->delete( $table_name, array( 'id' => $row['id'] ) );
				}
			}
		}
	}
	update_vendor_commission_fee($order_id);
}


/**
 * Exclude products from a particular category on the shop page
 **/
function custom_pre_get_posts_query( $q ) {

	$tax_query = (array) $q->get( 'tax_query' );

	if ( is_shop() && ! is_search() ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'freebies' ), // Don't display products in the clothing category on the shop page.
			'operator' => 'NOT IN',
		);
	}

	$q->set( 'tax_query', $tax_query );

}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

add_filter( 'woocommerce_shortcode_products_query', 'custom_exclude_cat_shortcodes', 10, 3 );

function custom_exclude_cat_shortcodes( $query_args, $atts, $loop_name ) {

		$categories           = $atts['category'];
		$categories           = str_replace( ' ', '', $categories );
			$categories_array = explode( ',', $categories );
		$category_count       = count( $categories_array );
	if ( $category_count >= 1 && ! empty( $categories_array[0] ) ) {
		echo '<div class="exclude-cat">Do not exclude any category</div>';
		   // Do not exclude any category
	} else {

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'freebies' ), // Don't display products from this category
				'operator' => 'NOT IN',
			),
		);
	}

	return $query_args;
}

add_action( 'woocommerce_order_status_changed', 'update_vendor_commission_fee', 100, 1 );
function update_vendor_commission_fee( $order_id ) {

	// Get the WC_Order Object instance (from the order ID)
	$order = wc_get_order( $order_id );

	global $wpdb;

	foreach ( $order->get_items() as $item_id => $item_data ) {

		$coupon = $order->get_coupon_codes();
		if ( $item_data->get_subtotal() != 0 && $order->get_status() == 'completed' ) {

			// Get an instance of corresponding the WC_Product object
			$product = $item_data->get_product();

			// $product_name = $product->get_name(); // Get the product name

			$product_price = $product->get_sale_price();

			// $item_quantity = $item_data->get_quantity(); // Get the item quantity

			$item_total = $item_data->get_total(); // Get the item line total

			// $net_product_price = $item_total - ($item_total * 0.0375 + 0.3 ) ;

			// $commission = ($product_price / $item_total ) * 100;

			// $net_seller_comm = $net_product_price / 100 * $commission;

			// $processor_fee = $product_price - $net_seller_comm;

			if ( $coupon[0] != '' ) {
				$item_subtotal = $item_data->get_total();
			} else {
				 $item_subtotal = $item_data->get_subtotal();
			}

			$processor_fee = $item_subtotal * 0.0375 + 0.3;

			$points_redeemed = $wpdb->get_var( "SELECT meta_value FROM `wp_postmeta` WHERE `post_id` = '" . $item_data->get_order_id() . "' AND `meta_key` LIKE '_wc_points_redeemed' " );

			if ( ! $points_redeemed && ! $coupon ) {
				$points_redeemed = $wpdb->get_var( "SELECT meta_value FROM `wp_postmeta` WHERE `post_id` = '" . $item_data->get_order_id() . "' AND `meta_key` LIKE '_wc_points_logged_redemption' " );
			}

			$product_commission_amount = $wpdb->get_var( "SELECT product_commission_amount FROM `wp_wcpv_commissions` WHERE `order_id` = '" . $item_data->get_order_id() . " ' " );

			$prices = get_all_dynamic_prices( $item_data->get_product_id() );

			$dynamic_commission = 0;
			foreach ( $prices as $price ) {

				$round = true;
				$price = $round ? round( $price, 2 ) : $price;

				$commission         = get_post_meta( $item_data->get_product_id(), '_product_vendors_commission_' . $price, true );
				//$dynamic_commission = 0;

				if ( $item_data->get_subtotal() == $price ) {
					$dynamic_commission = $commission;
				}
			}

			if ( $dynamic_commission ) {
				$product_commission_amount = $item_subtotal * ( floatval($dynamic_commission) / 100 );
			} else {
				$fixed_commission = get_post_meta( $item_data->get_product_id(), '_wcpv_product_commission', true );
				if ( ! $fixed_commission ) {
					$fixed_commission = get_post_meta( $item_data->get_product_id(), '_product_vendors_commission', true );

				}
				if ( $fixed_commission ) {
					$product_commission_amount = $item_subtotal * ( $fixed_commission / 100 );
				}
			}

			if ( $points_redeemed ) {

				if ( $dynamic_commission ) {
					$subtotal_fee              = $item_data->get_total() + ( floatval($points_redeemed) / 100 );  // subtotal
					$product_commission_amount = $item_data->get_subtotal() * ( $dynamic_commission / 100 );
				} else {
					$subtotal_fee     = $item_data->get_total() + ( floatval($points_redeemed) / 100 );
					$fixed_commission = intval( get_post_meta( $item_data->get_product_id(), '_wcpv_product_commission', true ) );
					if ( ! $fixed_commission ) {
						$fixed_commission = get_post_meta( $item_data->get_product_id(), '_product_vendors_commission', true );
					}
					if ( $fixed_commission ) {
						$product_commission_amount = $item_data->get_subtotal() * ( $fixed_commission / 100 );
					}
				}

				$subtotal_fee = $item_data->get_total() + ( floatval($points_redeemed) / 100 );

				// $net_product_price = $subtotal - ($subtotal * 0.0375 + 0.3 ) ;

				// $commission = ($product_price / $subtotal ) * 100;

				// $net_seller_comm = $net_product_price / 100 * $commission;

				// $processor_fee = $product_price - $net_seller_comm;

				$processor_fee = $item_data->get_subtotal() * 0.0375 + 0.3;

			}

			if ( $dynamic_commission ) {
				$half_processing = ( $processor_fee * $dynamic_commission ) / 100;
			} else {
				$half_processing = floatval($processor_fee) * floatval( floatval($fixed_commission) / 100 );
			}

			// $total_commission_amount = $product_commission_amount - ($processor_fee/2);
			$total_commission_amount = $product_commission_amount - $half_processing;

			$table_name = 'wp_wcpv_commissions';

			$wpdb->update(
				$table_name,
				array(
					'product_tax_amount'        => $processor_fee,
					'product_commission_amount' => $product_commission_amount,
					'total_commission_amount'   => $total_commission_amount,
				),
				array(
					'order_id'     => $item_data->get_order_id(),
					'product_name' => $item_data->get_name(),
				),
				array(
					'%s',
				),
				array( '%d', '%s' )
			);
		}
	}

}

add_filter( 'woocommerce_loop_add_to_cart_args', 'df_remove_rel', 10, 2 );
function df_remove_rel( $args, $product ) {
	unset( $args['attributes']['rel'] );

	return $args;
}

add_filter( 'woocommerce_customer_get_downloadable_products', 'filter_customer_downloadable_products', 10, 1 );
function filter_customer_downloadable_products( $downloads ) {
	$limit = 500; // Number of downloads to keep

	// Only on My account Downloads section for more than 5 downloads
	if ( is_wc_endpoint_url( 'downloads' ) && sizeof( $downloads ) > $limit ) {
		$keys_by_order_id = $sorted_downloads = array();
		$count            = 0;

		// Loop through the downloads array
		foreach ( $downloads as $key => $download ) {
			// Store the array key with the order ID
			$keys_by_order_id[ $key ] = $download['order_id'];
		}

		// Sorting the array by Order Ids in DESC order
		arsort( $keys_by_order_id );

		// Loop through the sorted array
		foreach ( $keys_by_order_id as $key => $order_id ) {
			// Set the corresponding  download in a new array (sorted)
			$sorted_downloads[] = $downloads[ $key ];
			$count++; // Increase the count
			// When the count reaches the limit
			if ( $count === $limit ) {
				break; // We stop the loop
			}
		}
		return $sorted_downloads;
	}
	return $downloads;
}

/*
 * Display variation dropdown in Product page feature image click for variation product
 */

function inky_astra_wc_dropdown_variation_attribute_options( $args = array() ) {
	$args = wp_parse_args(
		apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
		array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		)
	);

	// Get selected value.
	if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
		$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
		$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
	}

	$options               = $args['options'];
	$product               = $args['product'];
	$attribute             = $args['attribute'];
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class                 = $args['class'];
	$show_option_none      = (bool) $args['show_option_none'];
	$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
	$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
	if ( ! empty( $options ) ) {
		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms(
				$product->get_id(),
				$attribute,
				array(
					'fields' => 'all',
				)
			);
			error_log( '  Terms array   ' . print_r( $terms ) );
			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options, true ) ) {
					$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
				}
			}
		} else {
			foreach ( $options as $option ) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
				$html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
			}
		}
	}

	$html                   .= '</select>';
	$variation_dropdown_html = apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args );
	return $variation_dropdown_html;
}


// remove expired products ( status 3 from recent products shortcode )
add_filter(
	'woocommerce_shortcode_products_query',
	function( $query_args, $atts, $loop_name ) {
		if ( $loop_name == 'recent_products' ) {
			$query_args['meta_query'] = array(
				array(
					'key'     => 'Status',
					'value'   => 3,
					'compare' => 'NOT LIKE',
				),
			);
		}
		return $query_args;
	},
	10,
	3
);

// product suggestion in order mail
function order_mail_product_suggestion( $atts ) {
	$atts    = shortcode_atts(
		array(
			'id' => '',
		),
		$atts,
		'order_mail_product_suggestion'
	);
	$orderId = esc_attr( $atts['id'] );
	$order   = wc_get_order( (int) $orderId );
	$items   = $order->get_items();
	$cat     = array();
	$pid     = array();
	foreach ( $items as $item ) {
		$pid[] = $item->get_product_id();
		$terms = wp_get_post_terms( $item->get_product_id(), 'product_cat', array( 'fields' => 'ids' ) );
		foreach ( $terms as $term ) {
			$cat[] = $term;
		}
	}
	$uniuqcat = array_unique( $cat );
	$uniuqpid = array_unique( $pid );
	$html     = '';
	$args     = array(
		'post_type'           => 'product',
		'stock'               => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => '20',
		'orderby'             => 'rand',
		'meta_key'            => 'status',
		'meta_value'          => '2',
		'meta_compare'        => '=',
	);
	$loop     = new WC_Product_Query( $args );
	$products = $loop->get_products();
	if ( $products ) {
		$html .= '<div id="suggestion" style="padding: 20px 0;">';
			// $html .= '<h2 style="text-align: center;font-weight: 400;color: #000;">YOU MAY ALSO LIKE</h2>';
			$html             .= '<table style="width: 100%;table-layout: fixed;border-collapse: collapse;">';
				$html         .= '<tbody>';
					$html     .= '<tr>';
							$i = 0;
		foreach ( $products as $product ) {
			if ( in_array( $product->get_id(), $pid ) ) {
				continue;
			}
			if ( 'publish' === get_post_status( $product->get_id() ) && 0 != $product->get_price() ) {
				$html .= '<td class="cross-sell-img" align="center" style="padding: 5px;">';
				if ( has_post_thumbnail( $product->get_id() ) ) {
					$imageXS = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
					$html   .= '<a href="' . get_permalink( $product->get_id() ) . '"><img src="' . $imageXS[0] . '" /></a>';
				} else {
					$html .= '<a href="' . get_permalink( $product->get_id() ) . '"><img src="' . woocommerce_placeholder_img_src() . '" alt="Placeholder" width="300px" height="300px" /></a>';
				}
				// $html .= '<h3><a style="color: #000;font-weight: normal;text-decoration: none;font-size: 13px;text-align: center;display: block;line-height: 16px;" href="'.get_permalink( $product->get_id() ).'">'.esc_attr($product->get_title() ? $product->get_title() : $product->get_id()).'</a></h3>';
				// $html .= '<p><a style="font-weight: normal;text-decoration: none;display: block;margin: 0 auto;max-width: 72px;padding: 4px 9px;text-align: center;background-color: #000;color: #fff;font-size: 13px;" href="'.get_permalink( $product->get_id() ).'" class="shop-now">Shop Now</a></p>';
				$i++;
				if ( $i == 4 ) {
					break;
				}
			}
		}
					$html .= '</tr>';
				$html     .= '</tbody>';
			$html         .= '</table>';
		$html             .= '</div>';
	}
	// now return the preapred html
	return $html;
}
// register shortcode
add_shortcode( 'email_product_suggestion', 'order_mail_product_suggestion' );

/*
 * Add css for backend
 */
add_action( 'admin_enqueue_scripts', 'inky_astra_load_admin_styles' );
/**
 * Function to add css for backend
 */
function inky_astra_load_admin_styles() {
	wp_enqueue_style( 'admin_css_inky_astra', get_stylesheet_directory_uri() . '/css/admin-style-inky.css', false, '1.0.0' );
}

add_action('woocommerce_single_product_summary', 'replace_single_add_to_cart_button', 1 );
function replace_single_add_to_cart_button() {
global $product;

// For variable product types
if( $product->is_type( 'variable' ) ) {
$is_soldout = true;
foreach( $product->get_available_variations() as $variation ){
if( $variation['is_in_stock'] )
$is_soldout = false;
}
if( $is_soldout ){
remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
add_action( 'woocommerce_single_variation', 'sold_out_button', 20 );
}
}
}

// The sold_out button replacement
function sold_out_button() {
global $post, $product;

?>
<div class="woocommerce-variation-add-to-cart variations_button">
<?php
do_action( 'woocommerce_before_add_to_cart_quantity' );

woocommerce_quantity_input( array(
'min_value' => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
) );

do_action( 'woocommerce_after_add_to_cart_quantity' );
?>
<?php _e( "<div class='single_add_to_cart_button button alt' style='font-weight:bold; text-align:center; width:60%; margin-left: auto; margin-right: auto;'>Expired</div>", "woocommerce" ); ?>
</div>
<?php
}

add_action( 'woocommerce_account_downloads_columns', 'df_custom_downloads_columns', 10, 1 ); // Orders and account
function df_custom_downloads_columns( $columns ){
    // Removing "Download expires" column
    if(isset($columns['download-expires']))
        unset($columns['download-expires']);

    // Removing "Download remaining" column
    if(isset($columns['download-remaining']))
        unset($columns['download-remaining']);

		//Removing Download button column
		if(isset($columns['download-file']))
        unset($columns['download-file']);

    return $columns;
}

add_action( 'woocommerce_thankyou', 'custom_freebie_purchase_event_fb_pixel', 50);
function custom_freebie_purchase_event_fb_pixel($order_id) {

	$order = wc_get_order( $order_id );

	$order_subtotal = $order->get_subtotal();


	if( $order_subtotal == 0 ){
		$event_name = 'Purchase_Freebie';
	}
	else{
		$event_name = 'Purchase_Value';
	}

	$content_type  = 'product';
	$contents      = [];
	$product_ids   = [ [] ];
	$product_names = [];

			foreach ( $order->get_items() as $item ) {

				if ( $product = isset( $item['product_id'] ) ? wc_get_product( $item['product_id'] ) : null ) {

					$product_ids[]   = \WC_Facebookcommerce_Utils::get_fb_content_ids( $product );
					$product_names[] = $product->get_name();

					if ( 'product_group' !== $content_type && $product->is_type( 'variable' ) ) {
						$content_type = 'product_group';
					}

					$quantity = $item->get_quantity();
					$content  = new \stdClass();

					$content->id       = \WC_Facebookcommerce_Utils::get_fb_retailer_id( $product );
					$content->quantity = $quantity;

					$contents[] = $content;
				}
			}

	echo "<script>fbq('trackCustom', '" . $event_name . "',{ content_type: '".$content_type."', content_ids: '".wp_json_encode( array_merge( ... $product_ids ) )."', content_name: '".wp_json_encode( $product_names )."', contents: '".wp_json_encode( $contents )."', value: '".$order->get_total()."', currency: '".get_woocommerce_currency()."'})</script>";
}

function is_dealclubmembership_in_cart(): bool {
    if (is_admin()) {
        return false;
    }

    global $woocommerce;
    if (is_null($woocommerce->cart)) {
        wc_load_cart();
    }

    $items = $woocommerce->cart->get_cart();

    foreach ($items as $item) {
        if ($item['product_id'] == '174721' || $item['product_id'] == '174739' || $item['product_id'] == '174738') {
            return true;
        }
    }

    return false;
}

/* Added to display read more tag for all blog posts */
add_action('after_setup_theme','readmore_tag_func',10);

    function readmore_tag_func() {
        // Remove "Read more", conditionally added by astra
        remove_filter('excerpt_more', 'astra_post_link', 1);

        // Add "Read more" to every post
        add_filter('the_excerpt',
            function ($content) {
                return $content . astra_post_link();
            },
            99
        );

        // Remove "..." added by astra
        add_filter(
            'astra_post_link',
            function ($value) {
                return str_replace("&hellip;", "", $value);
            }
        );
    }


	add_filter( 'wp_nav_menu_items', 'df_menu_search_box', 20, 2 );
	function df_menu_search_box( $menu, $args ) {
		// Only used for the main menu.
		if ( 'primary' === $args->theme_location ) {

	$menu .= '<li>' .do_shortcode('[astra_search style="inline"]') . '</li>';
		}
		return $menu;
	}

	/* Added by Prasada - Overriden woocommerce_account_downloads function for MyAccount->Downloads page pagination */
	remove_action( 'woocommerce_account_downloads_endpoint', 'woocommerce_account_downloads' );
	if ( ! function_exists( 'woocommerce_account_downloads' ) ) {
		 function woocommerce_account_downloads($current_page) {
			 $current_page    = empty( $current_page ) ? 1 : absint( $current_page );
			 wc_get_template( 'order/order-downloads.php',
			 array(
				 'current_page'    => absint( $current_page ),
			 )
			 );
		 }
	}
	add_action( 'woocommerce_account_downloads_endpoint', 'woocommerce_account_downloads' );

	/* Added by Prasada - Downloads page sorting in descending order */
	function sortDownloadsArrayByKey(&$array,$key,$string = false,$desc = true){

		usort($array,function ($a, $b) use(&$key,&$desc)
		{
				if($a[$key] == $b[$key]){return 0;}
				else     return ($a[$key] > $b[$key]) ? -1 : 1;

		});
	}


// hide coupon field on the cart page
function disable_coupon_field_on_cart( $enabled ) {
	if ( is_cart() ) {
		$enabled = false;
	}
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'disable_coupon_field_on_cart' );


function woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {

   if ( is_null( $product ) ) {
      $product = $GLOBALS['product'];
   }

   $defaults = array(
      'input_id' => uniqid( 'quantity_' ),
      'input_name' => 'quantity',
      'input_value' => '1',
      'classes' => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
      'max_value' => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
      'min_value' => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
      'step' => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
      'pattern' => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
      'inputmode' => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
      'product_name' => $product ? $product->get_title() : '',
   );

   $args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

   // Apply sanity to min/max args - min cannot be lower than 0.
   $args['min_value'] = max( $args['min_value'], 0 );
   // Note: change 20 to whatever you like
   $args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : 5;

   // Max cannot be lower than min if defined.
   if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
      $args['max_value'] = $args['min_value'];
   }

   $options = '';

   for ( $count = $args['min_value']; $count <= $args['max_value']; $count = $count + $args['step'] ) {

      // Cart item quantity defined?
      if ( '' !== $args['input_value'] && $args['input_value'] >= 1 && $count == $args['input_value'] ) {
        $selected = 'selected';
      } else $selected = '';

      $options .= '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';

   }

   $string = '<div class="quantity"><select name="' . $args['input_name'] . '">' . $options . '</select></div>';

   if ( $echo ) {
      echo $string;
   } else {
      return $string;
   }

}


add_shortcode ('geturl', 'get_current_page_url');
function get_current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

    $pageURL = wp_get_referer();

    if($_POST['form_fields']['field_9755c9a']){
		$DC_param = $_POST['form_fields']['field_9755c9a'];
    	$pageURL = wp_get_referer() . '?add-to-cart=' . $DC_param;
    }
	return $pageURL;
}

add_action( 'woocommerce_after_shop_loop_item', 'remove_my_actionX', 9 );
function remove_my_actionX(){
    remove_action('woocommerce_after_shop_loop_item', 'astra_woo_woocommerce_shop_product_content', 10);
}
//remove_action( 'woocommerce_after_shop_loop_item', 'astra_woo_woocommerce_shop_product_content', 100 );
add_action( 'woocommerce_after_shop_loop_item', 'df_astra_woo_woocommerce_shop_product_content' );

	function df_astra_woo_woocommerce_shop_product_content() {

		global $product;
		$shop_structure = apply_filters( 'astra_woo_shop_product_structure', astra_get_option( 'shop-product-structure' ) );
		if ( is_array( $shop_structure ) && ! empty( $shop_structure ) ) {

			do_action( 'astra_woo_shop_before_summary_wrap' );

            echo '<div class="astra-shop-summary-wrap">';

			do_action( 'astra_woo_shop_summary_wrap_top' );

			foreach ( $shop_structure as $value ) {

				switch ( $value ) {
					case 'title':
						/**
						 * Add Product Title on shop page for all products.
						 */
						do_action( 'astra_woo_shop_title_before' );
						astra_woo_woocommerce_template_loop_product_title();
						do_action( 'astra_woo_shop_title_after' );
						break;
					case 'price':
						/**
						 * Add Product Price on shop page for all products.
						 */
						do_action( 'astra_woo_shop_price_before' );
						woocommerce_template_loop_price();
						if($product->get_average_rating() !== '0'){
                            woocommerce_template_loop_rating();
                         }
						echo '</div>';
						do_action( 'astra_woo_shop_price_after' );
						break;
					case 'ratings':
						/**
						 * Add rating on shop page for all products.
						 */
						do_action( 'astra_woo_shop_rating_before' );
						echo '<div class="inky-woo-shop-product-leftdiv">';

						do_action( 'astra_woo_shop_rating_after' );
						break;
					case 'short_desc':
						do_action( 'astra_woo_shop_short_description_before' );
						astra_woo_shop_product_short_description();
						do_action( 'astra_woo_shop_short_description_after' );
						break;
					case 'add_cart':
						do_action( 'astra_woo_shop_add_to_cart_before' );
								echo '<div class="inky-woo-shop-product-rightdiv">';
								echo '<a href="' . esc_url( get_the_permalink() ) . '" title="View Deal" class="button inky-view-deal-btn"> View Deal</a>';
								echo '</div>';
						do_action( 'astra_woo_shop_add_to_cart_after' );
						break;
					case 'category':
						/**
						 * Add and/or Remove Categories from shop archive page.
						 */
						do_action( 'astra_woo_shop_category_before' );
						astra_woo_shop_parent_category();
						do_action( 'astra_woo_shop_category_after' );
						break;
					default:
						break;
				}
			}

			do_action( 'astra_woo_shop_summary_wrap_bottom' );

			echo '</div>';

			do_action( 'astra_woo_shop_after_summary_wrap' );

		}
	}



add_filter ( 'woocommerce_account_menu_items', 'df_remove_my_account_links', 100 );
function df_remove_my_account_links( $menu_links ){

	unset( $menu_links['edit-address'] ); // Addresses
	unset( $menu_links['dashboard'] ); // Remove Dashboard
	unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	//unset( $menu_links['orders'] ); // Remove Orders
	//unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	unset( $menu_links['customer-logout'] ); // Remove Logout link
	unset( $menu_links['subscriptions'] );

	return $menu_links;

}

// Rename, re-order my account menu items
function fwuk_reorder_my_account_menu() {

	$current_user = wp_get_current_user();
	$product_id   = 88353;
	if ( wc_customer_bought_product( $current_user->email, $current_user->ID, $product_id ) ) {
		$dfacmenu = array(
			// endpoint   => label
			'dfacademy' => __( 'DealFuel Academy' ),
			'subticket' => __( 'Support' ),
		);
	} else {
		$dfacmenu = array(
			// endpoint   => label
			'subticket' => __( 'Support' ),
		);
	}

	$vendor_id = WC_Product_Vendors_Utils::is_vendor();
	if ( $vendor_id ) {
		$vendor_menu = array(
			'vendor'    => __( 'Vendor Dashboard' ),
		);
	}

    $neworder = array(
        'orders'             => __( 'Orders', 'woocommerce' ),
        'downloads'          => __( 'Downloads', 'woocommerce' ),
        'edit-account'       => __( 'Account', 'woocommerce' ),
  		'members-area'       => __( 'Memberships', 'woocommerce' ),
		'points-and-rewards'      => __( 'Credit Points', 'woocommerce' ),
        'subticket'      => __( 'Support', 'woocommerce' ),

    );
		if(!empty($vendor_menu)){
			$neworder = $neworder+$vendor_menu;
		}
		if(!empty($dfacmenu)){
			$neworder = $neworder + $dfacmenu;
		}
    return $neworder;
}
add_filter ( 'woocommerce_account_menu_items', 'fwuk_reorder_my_account_menu', 100 );

// Function to hide DC added to cart message on Checkout page
add_filter( 'wc_add_to_cart_message', 'remove_cart_message' );

function remove_cart_message() {
	if(is_page('checkout')){
    return;
	}
}

add_action('wp_footer','add_placeholders_on_account_form',99,1);
function add_placeholders_on_account_form($order_id){

	?>

<script>
jQuery(document).ready(function(){
	jQuery(".woocommerce-edit-account .woocommerce-EditAccountForm #password_current").attr("placeholder","Current Password");
	jQuery(".woocommerce-edit-account .woocommerce-EditAccountForm #password_1").attr("placeholder","New Password");
	jQuery(".woocommerce-edit-account .woocommerce-EditAccountForm #password_2").attr("placeholder","Confirm New Password");
	jQuery(".woocommerce-edit-account .woocommerce-EditAccountForm fieldset legend").html("Password Change (leave blank to leave unchanged)");

});


</script>
	<?php
}

add_filter( 'woocommerce_catalog_orderby', 'custom_remove_default_price_low_to_high_sorting_option' );

function custom_remove_default_price_low_to_high_sorting_option( $options ){

	unset( $options[ 'price' ] );
	return $options;
}
add_filter( 'woocommerce_catalog_orderby', 'add_custom_price_asc_sorting_option' );

function add_custom_price_asc_sorting_option( $options ){

	$options['price-asc'] = 'low to high';

	return $options;

}
// Custom Login CSS
function my_login_stylesheet() {
	wp_enqueue_style( 'custom-login', '/wp-content/themes/astra-child/css/style-login.css' );

}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

function my_login_scripts() {
    wp_enqueue_script( 'new-login-script', '/wp-content/themes/astra-child/js/new-login-script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'login_enqueue_scripts', 'my_login_scripts' );

add_action('wp_footer','thankyou_page_ui',10,1);
function thankyou_page_ui(){
	global $wpdb, $wp;

	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	$current_slug = add_query_arg( array(), $wp->request );

if(strpos($current_slug,"order-received") !== false){
	$order = new WC_Order(get_query_var('order-received')); //wc_get_order( $order_id );
	$order_id  = $order->get_id();
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery(".woocommerce-thankyou-order-received, .rec_subtext").wrapAll("<div class='rec_title_wrap'></div>");
			jQuery("h1.entry-title").hide();
			jQuery("<h3 class='woocommerce-order-details__title order_title_h3'>Order ID: <?php echo $order_id; ?></h3>").insertBefore(".woocommerce-table--order-details");

		});
	</script>
	<?php
}
if(strpos($current_slug,"lifetime-deal") !== false){
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery("<hr>").insertBefore(".page-template-df-template-lifetime-deals .products .ast-product .add_to_cart_button");

		});
	</script>

	<?php
}
if(strpos($current_slug,"dealclub") !== false){
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery("<hr>").insertBefore(".page-template-df-template-dealclub .products .product .add_to_cart_button");

		});
	</script>

	<?php
}
if(strpos($current_slug,"past-deals") !== false){
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery("<hr>").insertBefore(".page-template-page-template-past-deals .products .ast-product .product_type_simple");

		});
	</script>

	<?php
}
if(strpos($current_slug,"all-deals") !== false || strpos($current_slug,"deals-tools") !== false
  || strpos($current_slug,"deals-ecourses") !== false || strpos($current_slug,"deals-marketing") !== false
	|| strpos($current_slug,"deals-wordpress") !== false || strpos($current_slug,"deals-web-development") !== false
	|| strpos($current_slug,"deals-software") !== false || strpos($current_slug,"deals-design") !== false
	|| strpos($current_slug,"deals-hosting-deals") !== false || strpos($current_slug,"deals-others") !== false){
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery("<hr>").insertBefore(".page-template-df-template-all-deals .ast-container .products .product .add_to_cart_button");

		});
	</script>

	<?php
}
}


function custom_order_received_text( $text, $order ) {
	$customer_email = $order->get_billing_email();
    $new = $text . "<p class='rec_subtext'>We have also sent you detailed instructions to download your purchase on your registered email id (".$customer_email.")</p>";
    return $new;
}
add_filter('woocommerce_thankyou_order_received_text', 'custom_order_received_text', 10, 2 );

function redirect_to_orders_page() {

	$current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$dashboard_url = get_permalink( get_option('woocommerce_myaccount_page_id'));

	if(is_user_logged_in() && $dashboard_url == $current_url){
	$url = get_home_url() . '/account/orders';
	wp_redirect( $url );
	exit;
	}
}
add_action('template_redirect', 'redirect_to_orders_page');

add_action('wp_footer','add_new_ui_to_account_page_login');
function add_new_ui_to_account_page_login(){
	if(is_page('account')){
	?>
	<script>
		jQuery(document).ready(function(){
			  jQuery('<div class="log-left"><p><img width="80px" height="80px" src="https://www.dealfuel.com/wp-content/uploads/2019/07/dealfuel_logo__5__GQK_icon.ico" class="attachment-full size-full lazyloaded" alt="" data-ll-status="loaded" width="273" height="64"></p><h2><span style="color: #ffffff;">Get more things done with us</span></h2><p class="subtextp"><span style="color: #ffffff;">We are excited to see you!</span></p><p class="subtextsign"><span style="color: #ffffff;">Sign in to start</span></p></div>').insertBefore('.woocommerce-form-login');
			  jQuery('.woocommerce-form-login,#nav,#backtoblog').wrapAll('<div class="log-right"></div>');
			  jQuery('.log-left,.log-right').wrapAll('<div class="logwrapper"></div>');
			  jQuery('<h1>Log In</h1>').insertBefore('.woocommerce-form-login');
				jQuery('.page-id-60570 .entry-title').hide();
				jQuery('.page-id-60570 .woocommerce h2:contains(Login)').hide();
		});
	</script>
	<?php
}
}
add_action('wp_footer','disable_upsell_pitch_freebie_deals');

function disable_upsell_pitch_freebie_deals(){
	if(is_product()){
		global $product;

		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		$category_name = $terms[0]->name;
		if( $category_name == "Freebies"){
			?>
			<script>
				jQuery(document).ready(function(){
					jQuery('.sidebar-main #elementor-library-3').hide();
				});
				</script>
			<?php
		}

	}
}
//Webhook for submit a deal form- Prasada
add_action( 'elementor_pro/forms/new_record', function( $record, $handler ) {
    //make sure its our form
    $form_name = $record->get_form_settings( 'form_name' );

    // Replace MY_FORM_NAME with the name you gave your form
    if ( 'submit_deal_form' !== $form_name ) {
        return;
    }

    $raw_fields = $record->get( 'fields' );
    $fields = [];
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }



if ( isset( $fields['complete_submit_deal'] ) ) {
	$partner_id   = sanitize_text_field( $fields['partner_id'] );
	$email_id     = sanitize_email( $fields['email_id'] );
	$company_name = sanitize_text_field( $fields['company_name'] );
	$website      = sanitize_text_field( $fields['website'] );
	$deal_title   = sanitize_title( $fields['deal_title'] );
	$our_price    = filter_var( $fields['original_price'], FILTER_SANITIZE_NUMBER_FLOAT );
	$dis_price    = filter_var( $fields['discount_price'], FILTER_SANITIZE_NUMBER_FLOAT );
	$deliverables = sanitize_text_field( $fields['deliverables'] );
	$intro_blurb  = sanitize_text_field( $fields['intro_blurb'] );
	$deal_terms   = sanitize_text_field( $fields['deal_terms'] );
	$testimonials = sanitize_text_field( $fields['testimonials'] );

	if ( isset( $fields['deal_title'] ) ) {
		// Create post object
		$my_post = array(
			'post_title'   => $deal_title,
			'post_content' => '',
			'post_status'  => 'draft',
			'post_author'  => 1,
			'post_type'    => 'product',
		);
		// Insert the post into the database
		$success = false;
		if ( ! ( $newpost = wp_insert_post( $my_post, $wp_error ) ) ) {
			$success = false;
			echo "$wp_error";
			echo 'cannot insert deal';
		} else {
			$success = true;
		}

		add_post_meta( $newpost, 'coupon_website', $website );
		add_post_meta( $newpost, 'our_price', $our_price );
		add_post_meta( $newpost, 'current_price', $dis_price );
		add_post_meta( $newpost, 'email_id', $email_id );
		add_post_meta( $newpost, 'owner_name', $partner_id );
		add_post_meta( $newpost, 'deal_terms', $deal_terms );
		add_post_meta( $newpost, 'status', 0 );
		add_post_meta( $newpost, 'is_expired', 0 );
		$content = '<p>' . $intro_blurb . '</p>' .
			'<p>' . $testimonials . '</p>' .
			'<p>' . $deliverables . '</p>';

		 // handles basic file upload using WordPress functionality
		// to do - include more checks
		if ( isset( $_FILES ) ) {

			$name = "form_fields['add_image0']";//'add_image0';
			if ( $_FILES[ $name ]['size'] > 2000000 ) {
				exit( 'Each image uploaded must be less than 2 Megabytes.' );
			} elseif ( ! $_FILES[ $name ]['error'] ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				$id_df       = media_handle_sideload( $_FILES[ $name ], $newpost, 'seller uploaded image ' );
				$content .= '<p><img src="' . $fields['add_image0']  . '"/></p>';
			}
			$name = "form_fields['add_image1']";
			if ( $_FILES[ $name ]['size'] > 2000000 ) {
				exit( 'Each image uploaded must be less than 2 Megabytes.' );
			} elseif ( ! $_FILES[ $name ]['error'] ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				$id_df       = media_handle_sideload( $_FILES[ $name ], $newpost, 'seller uploaded image ' );
				$content .= '<p><img src="' . $fields['add_image1'] . '"/></p>';
			}

		}
		// time to update the post.
		if ( ! wp_update_post( array(
			'ID'           => $newpost,
			'post_content' => $content,
		) ) ) {
			echo 'cannot update database';
		}


	}
}



}, 10, 2 );
add_action('init', 'df_add_author_woocommerce_products', 999 );

function df_add_author_woocommerce_products() {
    add_post_type_support( 'product', 'author' );
}
function hide_menu(){
 global $current_user;
$user_role = $current_user->roles[0];

    if($user_role == 'author_specific_product'){


        remove_menu_page( 'users.php' );
        remove_submenu_page( 'users.php', 'user-new.php' );
        remove_submenu_page( 'users.php', 'profile.php' );

        remove_menu_page( 'upload.php' );
        remove_submenu_page( 'upload.php', 'media-new.php' );

        remove_menu_page( 'edit.php' );
        remove_submenu_page( 'edit.php', 'post-new.php' );
        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );


        remove_menu_page( 'edit.php?post_type=page' );
        remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' );
				remove_submenu_page( 'edit.php?post_type=product', 'post-new.php?post_type=product' );
				remove_menu_page('edit.php?post_type=elementor_library');
				remove_menu_page( 'edit.php?post_type=course');
				remove_menu_page('wpcf7');
				echo '<style>
            .post-type-product .page-title-action{
              display: none !important;
            }
        </style>';


        remove_menu_page( 'edit-comments.php' );

         remove_menu_page( 'tools.php' );
         remove_menu_page( 'options-general.php' );

    }
}




add_action('admin_head', 'hide_menu');


/**
 * Astra theme above header search result filter with post type products only. $form is html input for filter
 *
 * @param string $form html input for filter.
 */
function inky_astra_get_search_form( $form ) {
	$form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
    <label>
    <span class="screen-reader-text">' . _x( 'Search for:', 'label', 'inky-astra-child' ) . '</span>
        <input type="search" class="search-field" ' . apply_filters( 'astra_search_field_toggle_data_attrs', '' ) . ' placeholder="' . apply_filters( 'astra_search_field_placeholder', esc_attr_x( 'Search &hellip;', 'placeholder', 'inky-astra-child' ) ) . '" value="' . get_search_query() . '" name="s" role="search" tabindex="-1"/>
        </label>
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="search-submit" value="' . esc_attr__( 'Search', 'inky-astra-child' ) . '"  aria-label="search submit"><i class="astra-search-icon"></i></button>
        </form>';

		return $form;
}
add_filter( 'astra_get_search_form', 'inky_astra_get_search_form' );

/*
 * Function to exclude out of stock products in search result page, shop page and category page
 */

add_filter( 'woocommerce_product_query_meta_query', 'df_shop_only_instock_products', 10, 2 );
function df_shop_only_instock_products( $meta_query, $query ) {

    if( is_search() || is_shop() || ( is_product_category() && !is_product_category('expired')) ) {
			$meta_query[] = array(
			    'key'     => '_stock_status',
			    'value'   => 'outofstock',
			    'compare' => '!='
			);

		}


		return $meta_query;
}
add_action('wp_footer','add_required_sign_for_subsc_btn_checkout');
function add_required_sign_for_subsc_btn_checkout(){
  if(is_page('checkout')){
  ?>
  <script>
  jQuery(document).ready(function(){
    jQuery('.woochimp_checkout_checkbox label').append('&nbsp;<span style="color:red;">*</span>');
  });
  </script>
  <?php
}
}
add_action('wp_head','remove_double_price_in_product_sidebar',10);
function remove_double_price_in_product_sidebar(){
	global $product;
	if(is_product()){
	//if ( $product->is_type( 'variable' ) ) {
		?>
	  <script>
	  jQuery(document).ready(function(){


			jQuery("#secondary .elementor-product-variable").css("margin-top","-22px");
			jQuery("#secondary .variations select").change(function(){
				if ( jQuery(this).val() != "" ) {
						jQuery('.product-sidebar .elementor-widget-woocommerce-product-price .price').hide();
				}

			});
			jQuery(".reset_variations").click(function(){
				jQuery(".single_variation_wrap .woocommerce-variation-price .price").hide();
				jQuery(".product-sidebar .elementor-widget-woocommerce-product-price .price").show();
			});
	  });
	  </script>
	  <?php
	//}

}
}
/* Changed the store url */
function wc_empty_cart_redirect_url() {
	return "https://www.dealfuel.com/all-deals/";
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

/* Added to display DC prices only on Deal Pages if DealClub is in cart - By Prasada */

add_filter( 'woocommerce_get_price_html', 'df_change_product_price_html', 10, 2 );

function df_change_product_price_html( $price, $product ) {

	$user_membership_type = is_user_has_annual_or_monthly_memebership();

	if(!is_admin()){
	global $woocommerce;

	if ((is_user_an_active_member_wcm()) || ( isset($_GET["utm_source"]) && $_GET["utm_source"] == "" && is_dealclubmembership_in_cart() && get_dynamic_price( $product->get_id() ) != '' )) {

		// display prices on shop page for simple products products

		if( $user_membership_type == 174765 || $user_membership_type == 174763 ) {
			//if user's membership is annual or monthly then show annual's price

			$updated_dynamic_price = get_dynamic_price( $product->get_id() )[0];

		}else {//if user's memership is monthly,show monthly's price

			$updated_dynamic_price = get_dynamic_price( $product->get_id() )[1];
		}

		if( '' == $updated_dynamic_price )
		{
		    return $price;
		}
		if ( $product->is_on_sale() ) {
			$sale_price = (float) $product->get_sale_price();
		} else {
			$sale_price = (float) $product->get_price();
		}
		if ( $product->is_type( 'simple' ) ) {
			   $regular_price = number_format($product->get_regular_price(),2);
				 	$sale_price = number_format($product->get_sale_price(),2);
		}
		$price = '<p style="margin-top: 10px;" class="' . esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ) . '"><del>$' . $sale_price . '</del><ins>$' . $updated_dynamic_price . '</ins></p>';

	}
	if( (is_user_an_active_member_wcm() && $product->is_type( 'variable' )  ) || (isset($_GET["utm_source"]) && $_GET["utm_source"] == "" && is_dealclubmembership_in_cart() && $product->is_type( 'variable' ) ))
	{	// display prices on shop page for variable products

		$variation_arr2 = get_post_meta( $product->get_id(), '_pricing_rules', 'true' );
		foreach ( $variation_arr2 as $var_obj2 ) {
			$var_amounts .= $var_obj2['rules'][1]['amount']."-";

		}

		$var_amount_arr = explode("-",$var_amounts);


		//round the values of the array upto 2 decimal points

		foreach ($var_amount_arr as $key => $value) {
			if (is_numeric($value)) {
				$var_amount_arr[$key] = round($value, 2);
			}
		}

		if($var_amount_arr[0] != 0){

			if( $user_membership_type == 174765 || $user_membership_type == 174763 ) {
				//if user's membership is annual or quaterly then show annual's price
				$price_val = '$'.$var_amount_arr[0].'-'.'$'.$var_amount_arr[count($var_amount_arr) -3];

			}else {//if user's memership is monthly,show monthly's price

				$price_val = '$'.$var_amount_arr[1].'-'.'$'.$var_amount_arr[3];


			}

		}
		else{
			$price_val = '$'.$var_amount_arr[0];
		}

		$price = '<p style="margin-top: 10px;" class="price"><span class="woocommerce-Price-amount amount"><bdi>'.$price_val.'</bdi></span></p>';

	}

	return $price;
	}
}
add_action( 'woocommerce_variable_add_to_cart', 'df_update_price_with_variation_price' );

function df_update_price_with_variation_price() {
	if(!is_admin()){
   global $product;
   $price = $product->get_price_html();

	if( (is_user_an_active_member_wcm()) || ($product->is_type( 'variable' ) && is_dealclubmembership_in_cart()) ){
		wc_enqueue_js( "
       jQuery(document).on('found_variation', function( event, variation ) {
          jQuery('.summary > p.price').html(variation.price_html);
          jQuery('.woocommerce-variation-price').hide();
					jQuery('.reset_variations').click(function(){
						jQuery('p.varcls').hide();
					});
       });

    " );
	}elseif ( $product->is_type( 'variable' ) && !is_dealclubmembership_in_cart() ) {
	wc_enqueue_js( "
		 jQuery(document).on('found_variation', function( event, variation ) {
				jQuery('.summary > p.price').html(variation.price_html);
				jQuery('.woocommerce-variation-price').show();
		 });

	" );
 }

 	}
}



/*
 * Function to display message on archived deal page
 */

add_shortcode( 'archive_deal_title', 'archive_deal_title' );
function archive_deal_title() {
    echo "<b>" . get_the_title() . "</b> has expired and is no longer available.";
}

/*
 * Display product title image and description on archived product page
 */

add_shortcode( 'df_display_new_deal_on_archived_deal_page', 'df_display_new_deal_on_archived_deal_page' );

/**
 * Function to display product title image description on archived product page
 *
 * @param array $atts attribute array for shortcode.
 */
function df_display_new_deal_on_archived_deal_page( $atts ) {


		$current_product_id = get_the_ID();

		$terms = wp_get_object_terms( $current_product_id, 'product_cat' );
	  if ( count( $terms ) > 0 ) {
	  	foreach( $terms as $item ) {
	  		$term_ids[] = $item->term_id;
	  	}
	  }
		$expire_cat_id = array(193);
		$category_array = array_diff($term_ids, $expire_cat_id);

		$args  = shortcode_atts(
			array(
				'value' => 'deal_title',
			),
			$atts
		);
		$value = esc_attr( $args['value'] );

    $args = array(
      'post_type'      => 'product',
      'posts_per_page' => 1,
      'product_cat'    => $category_array,
      'orderby'        => 'date',
      'order'          => 'DSC',
    );

    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) :
      $loop->the_post();
      global $product;

      if ( 'deal_title' == $value ) {
        ?>
        <a href="<?php echo get_permalink( $loop->post->ID ); ?>" title="<?php echo esc_attr( $loop->post->post_title ? $loop->post->post_title : $loop->post->ID ); ?>">
          <h2><?php the_title(); ?></h2>
        </a>
        <?php
      }else if ( 'feature_image' == $value ){
        ?>
        <div class="product-featured-image">
          <a href="<?php echo get_permalink( $loop->post->ID ); ?>" title="<?php echo esc_attr( $loop->post->post_title ? $loop->post->post_title : $loop->post->ID ); ?>">
            <?php
            if ( has_post_thumbnail( $loop->post->ID ) ) {
                echo get_the_post_thumbnail( $loop->post->ID, 'featured' );
            } else {
                echo '<img src="' . woocommerce_placeholder_img_src() . '" alt="Placeholder" width="360px" height="240px" />';
            }
            ?>
          </a>
        </div>
        <?php
      }else if( 'deal_description' == $value ){

        //$product_description_short = get_the_content( 'Read more' , false , $loop->post->ID );
        $product_description_short = wp_trim_words( get_the_content( 'Read more' , false , $loop->post->ID ), 30, '...' );
        echo $product_description_short . "...";

      }else if('add_to_cart' == $value){
        ?>
        <a href="<?php echo get_permalink( $loop->post->ID ); ?>" class="button btn_deal" id="<?php echo 'pixelo-buy-button-' . get_the_ID(); ?>"><?php _e( 'Check Out The Deal', 'pixelo' ); ?></a>
        <?php
      }



    endwhile;
    wp_reset_query();

}

/*
 * Check if the given email id is exsists in moosend or not
 */

function check_if_email_exists_moosend($email){

  $request_uri = "https://api.moosend.com/v3/subscribers/e002e427-4fbb-4f07-8c1d-41bc4c3202b4/view.json?apikey=776e1f08-b290-46e0-bb05-4d0e83eabc33&Email=".$email ;
  $request = wp_remote_get( $request_uri );

  if( is_wp_error( $request ) || '200' != wp_remote_retrieve_response_code( $request ) ){
    return false;
  }

  $events = json_decode( wp_remote_retrieve_body( $request ) );

  if( $events->Error= "MEMBER_NOT_FOUND"){
    return true; //this email is not found in moosend list
  }

  return false;

}


/*
 * Added validation on subscribe to Newsletter
 * If there is only freebie in cart then subscribe to Newsletter is compulsary
 */

add_action('woocommerce_after_checkout_validation','subscribe_validation_on_checkout_for_freebies_moosend',9999, 2);
function subscribe_validation_on_checkout_for_freebies_moosend($fields, $errors){
	// in case any validation errors
	if( !empty( $errors->get_error_codes() ) ) {
			// omit all existing error messages
			foreach( $errors->get_error_codes() as $code ) {
			$errors->remove( $code );
			}
			// display custom single error message
			$errors->add( 'validation', 'Please fill all the required fields.' );
		}
	}



/*
 * Function to add subscribers in moosend if not exsists ( Checkout page )
 */

add_action( 'woocommerce_checkout_update_order_meta', 'df_add_subscribers_in_moosend' );

function df_add_subscribers_in_moosend( $order_id ) {

    if ( $_POST['moosend_subscribe_checkbox'] ) update_post_meta( $order_id, '_moosend_subscribe_checkbox', esc_attr( $_POST['moosend_subscribe_checkbox'] ) );
    if ( $_POST['moosend_subscribe_checkbox'] && check_if_email_exists_moosend($email) ){ //Add Email in moosend if not exsists

      $billing_email = esc_attr( $_POST['billing_email'] );

      $first_name =  esc_attr( $_POST['billing_first_name'] );
      $first_name_string = "First Name=".$first_name;

      $last_name =  esc_attr( $_POST['billing_last_name'] );
      $last_name_string = "Last Name=".$last_name;

      $request_uri = "https://api.moosend.com/v3/subscribers/e002e427-4fbb-4f07-8c1d-41bc4c3202b4/subscribe.json?apikey=776e1f08-b290-46e0-bb05-4d0e83eabc33";
      $wp_args['Email'] = $billing_email;
      $wp_args['CustomFields'] = array('Source=Checkout-Subscribe',$first_name_string,$last_name_string);
      $request = wp_remote_post( $request_uri,  array('body' => $wp_args));

      if( is_wp_error( $request ) || '200' != wp_remote_retrieve_response_code( $request ) ){

      }
      $events = json_decode( wp_remote_retrieve_body( $request ) );

    }
}

add_action( 'woocommerce_register_form_start', 'df_add_name_woo_account_registration' );

function df_add_name_woo_account_registration() {
    ?>

    <p class="form-row form-row-first">
    <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>

    <p class="form-row form-row-last">
    <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>

    <div class="clear"></div>

    <?php
}
/*
 * Registration form firstname and last name fields Validation
 */

add_filter( 'woocommerce_registration_errors', 'df_registration_validate_name_fields', 10, 3 );

function df_registration_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( ' First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $errors->add( 'billing_last_name_error', __( ' Last name is required!.', 'woocommerce' ) );
    }
    return $errors;
}

/* Remove billing first name last name fields from checkout if logged in */

add_filter( 'woocommerce_billing_fields' , 'df_remove_billing_fields' );
function df_remove_billing_fields( $fields ) {
	if ( is_user_logged_in() ) {
         unset($fields['billing_last_name']);
				 unset($fields['billing_first_name']);
	}
         return $fields;

}
/* Update/fill user billing first name and last name at checkout page if empty */

add_action( 'woocommerce_thankyou', 'df_update_order_first_and_last_names', 30, 1 );
function df_update_order_first_and_last_names( $order_id ) {
	if ( ! $order_id )
	        return;

	$order = new WC_Order( $order_id );

	$user_id = $order->get_customer_id(); // Get user ID

    if( empty($user_id) || $user_id == 0 )
        return;

    $first_name = $order->get_billing_first_name();

    if( empty($first_name) ){

        $first_name = get_user_meta( $user_id, 'billing_first_name', true );
        if( empty($first_name) )
            $first_name = get_user_meta( $user_id, 'first_name', true );

				$order->set_billing_first_name($first_name); // Save last nam
		}
		else{
			update_user_meta( $user_id, 'billing_first_name', sanitize_text_field($first_name) );
			update_user_meta( $user_id, 'first_name', sanitize_text_field($first_name) );
		}

    $last_name = $order->get_billing_last_name();

    if( empty($last_name) ){
        $last_name = get_user_meta( $user_id, 'billing_last_name', true );
        if( empty($last_name) )
            $last_name = get_user_meta( $user_id, 'last_name', true );

        $order->set_billing_last_name($last_name); // Save last name
    }
		else{
			update_user_meta( $user_id, 'billing_last_name', sanitize_text_field($last_name) );
			update_user_meta( $user_id, 'last_name', sanitize_text_field($last_name) );
		}
}
/* Saving Registration form firstname and last name fields  */

add_action( 'woocommerce_created_customer', 'df_registration_save_name_fields' );

function df_registration_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
    }

}

// Save custom checkout field value as user meta data
add_action('woocommerce_checkout_update_customer','df_checkout_update_customer', 10, 2 );
function df_checkout_update_customer( $customer, $data ){
    if (  is_user_logged_in() || is_admin() )
        return;
    // Update user meta data
    if ( isset($_POST['billing_first_name']) ){
				$first_name = $_POST['billing_first_name'];
        update_user_meta( $customer->get_id(), 'billing_first_name', $first_name );
			}
}


/* Adding  Cart Total attributes to Hotjar */

add_action('woocommerce_cart_contents', 'send_to_hotjar_cart_total_attributes');
function send_to_hotjar_cart_total_attributes() {
    if(!is_home() && !is_admin()){
         if(WC()->cart->cart_contents_total > 0){

	        $current_user = wp_get_current_user();
    	    $curruser_id = $current_user->ID;
		    $cart_total = WC()->cart->cart_contents_total;


	?>
				<script>
				var userId = <?php echo $curruser_id; ?> || null;
				window.hj('identify', userId, {
				     'Cart Total': <?php echo $cart_total; ?>,
				});
				</script>

<?php
         }
	}
}
/* Adding  Order Total attributes to Hotjar */
add_action('wp_footer','send_to_hotjar_order_total_attributes');
function send_to_hotjar_order_total_attributes(){
if(!is_home() && !is_admin()){
	if ( ! is_wc_endpoint_url('order-received') ) return;
    global $wp;

	$current_user = wp_get_current_user();
	$curruser_id = $current_user->ID;

    // If order_id is defined
    if ( isset($wp->query_vars['order-received']) && absint($wp->query_vars['order-received']) > 0 ){

		    $order_id  = absint($wp->query_vars['order-received']); // The order ID
		    $order     = wc_get_order($order_id ); // The WC_Order object
				$order_total = $order->get_total();
				$customer_email = $order->billing_email;

	?>
				<script>
				var userId = <?php echo $curruser_id; ?> || null;
				window.hj('identify', userId, {
				     'order_total': <?php echo $order_total; ?>,
						 'order_id': <?php echo $order_id; ?>,
						 'email': "<?php echo $customer_email; ?>",
				});
				</script>

<?php
		}
	}
}

/*
 * Redirect to checkout page if DealClub membership is added from checkout page sidebar
 */

add_filter( 'woocommerce_add_to_cart_redirect', 'df_astra_custom_add_to_cart_redirect' );

function df_astra_custom_add_to_cart_redirect( $url ) {
	if (isset($array['utm_source'])) {
		if($_REQUEST['utm_source'] == "checkout-page"){
			$url = WC()->cart->get_checkout_url();
		}else{
		//	$url = WC()->cart->get_cart_url();
		}
		return $url;
	}
}
/*
 * Exclude freebie and expired deals category from Search result page
 */
function df_astra_custom_pre_get_posts_query( $q ) {

    $tax_query = (array) $q->get( 'tax_query' );

		if(is_shop() && is_search()){ //exclude from search result
	    $tax_query[] = array(
	           'taxonomy' => 'product_cat',
	           'field' => 'slug',
	           'terms' => array( 'freebies', 'expired' ),
	           'operator' => 'NOT IN'
	    );
		}

    $q->set( 'tax_query', $tax_query );

}
add_action( 'woocommerce_product_query', 'df_astra_custom_pre_get_posts_query' );

/*
 * Regenerate download permission guest checkout
 */
add_action( 'woocommerce_order_status_completed', 'assign_download_permission_for_guest_checkout' ); // Executes when a status changes to completed
function assign_download_permission_for_guest_checkout( $order_id ) {
	global $wpdb;
	$wpdb->delete( 'wp_woocommerce_downloadable_product_permissions' , array( 'order_id' => $order_id ) );

	$order = wc_get_order( $order_id );
	if ( sizeof( $order->get_items() ) > 0 ) {
        foreach ( $order->get_items() as $item ) {
            $_product = $order->get_product_from_item( $item );
            if ( $_product && $_product->exists() && $_product->is_downloadable() ) {
                $downloads = $_product->get_files();
                foreach ( array_keys( $downloads ) as $download_id ) {
                    wc_downloadable_file_permission( $download_id, $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'], $order, $item['qty'] );
                }
            }
        }
    }
    update_post_meta( $order_id, '_download_permissions_granted', 1 );
    do_action( 'woocommerce_grant_product_download_permissions', $order_id );

}

/* Added by Prasada - New Product sidebar UI changes - start */
add_shortcode('productpage_sidebar_addtocart','productpage_sidebar_addtocart_shortcode');
function productpage_sidebar_addtocart_shortcode(){
    global $product;

    if (!is_product()){
        return;
    }
	error_log('Hello World 3');
	$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();

	if( $product->is_type( 'simple' ) ){


        $productid = $product->get_id();
        $product_name = $product->get_name();

        //Multiple Products add to cart
        $product_ids = $productid.',174739';

        $add_to_cart_url = esc_url_raw( add_query_arg( 'add-to-cart', $product_ids, wc_get_cart_url() ) );


        if($product->get_sale_price() == 0 && get_dynamic_price($productid) == 0 ){

                ?>
                <form>
                    <div class="radio_section">
                        <div>
                            <div class="withdcleft">
                                <label>Price</label>
                            </div>
                            <div class="withoutdcright"><span class="wodcpriceins"><?php echo "$" . $product->get_sale_price(); ?></span><span class="wodcpricedel"><?php echo "$".number_format($product->get_regular_price(),2); ?></span>
                            </div>

                    </div>
                </form>
                <div class="sticky_add_to_cart1">
                    <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="sticky_addtocart_left sticky_sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">
                    <span class='dcpriceins membership'><?php echo '  $'.$product->get_sale_price(); ?></span><br><span class='membership'>Download Now</span></a>
                </div>
                <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="sidc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Download Now</a>

                <?php

        }


        if ( $product->get_sale_price() > 0  && get_dynamic_price( $product->get_id() ) >= 0 ) {

            ?>

			<div class="df_show_discounted_price_dynamically">
					<span class="df_show_price"><?php echo "$" . get_dynamic_price( $product->get_id())[0] ?></span><span class="df_price_del"><?php echo "$".number_format($product->get_regular_price(),2); ?></span>
				</div>
            <form>

                <div class="radio_section">
                    <div>
                        <div class="withdcleft">
                            <input type="radio" class="df_display_dynamic_price withdcpricerad" id="withdealclub" checked name="radiodealclub" value="withdealclub" data-product-id="<?php echo $product->get_id(); ?>" data-price="<?php echo get_dynamic_price( $product->get_id())[0] ?>">
                            <label class="annaul_text" >25% OFF with $49/Year Membership</label>
                        </div>

						<div style="display:none" class="tooltip" id="tooltip">

								<div class="tooltip-arrow"></div>
  								<div class="tooltip-content"><span class="tooltip_msg"> You will be upgraded to $49/Year Annual Membership </span></div>

						</div>

                    </div>

					<div>
                        <div class="montlydcleft">
                            <input type="radio" class="df_display_dynamic_price withmonthlypricerad" id="withmonthlydealclub" name="radiodealclub" value="withmonthlydealclub" data-product-id="<?php echo $product->get_id(); ?>" data-price="<?php echo  get_dynamic_price( $product->get_id())[1] ?>">
                            <label class="monthly_text" >10% OFF with $9/Month Membership</label>
                        </div>
                    </div>


                    <div style="clear: both;">
                    <div class="withoutdcleft">
                        <input type="radio" class="df_display_dynamic_price withoutdcpricerad" id="withoutdealclub" name="radiodealclub" value="withoutdealclub" data-price="<?php echo $product->get_sale_price(); ?>">
                        <label class="non_dc_text">Buy at Deal Price</label>
                    </div>
                    </div>
                </div>
            </form>

            <div class="sticky_add_to_cart1">

                <a style="display:none" href="javascript:void(0);" id="simple_scroll_to_top" class="sticky_addtocart_left sticky_sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">
                <span>Buy Now</span></a>

            </div>
            <script>
            jQuery(document).ready(function(){

				console.log("LEVEL 1");

                var is_dc_in_cart = "<?php echo is_dealclubmembership_in_cart(); ?>";
                var is_dc_active_member = "<?php echo is_user_an_active_member_wcm(); ?>";
				var is_annual_or_monthly = "<?php echo is_user_has_annual_or_monthly_memebership(); ?>";
				var monthly_dynamic_price = <?php echo get_dynamic_price( $product->get_id() )[1]; ?>

				// jQuery for scroll to top
					jQuery("#simple_scroll_to_top").click(function() {
					var targetDiv = jQuery("#dealpage-details-sc");
					var offset = 180; // Adjust the offset value as needed
					var targetPosition = targetDiv.offset().top - offset;

					jQuery("html, body").animate({ scrollTop: targetPosition }, "slow");


				});

					//tooltip for annual upsell when user in monthly member

					if ( is_annual_or_monthly == 174761  ) {

						jQuery('.withdcleft').click(function() {
						jQuery('.tooltip ').show();
						});

						jQuery('.montlydcleft').click(function() {
						jQuery('.tooltip ').hide();
						});

					}

				//find out the which radio button is selected and make its text green
				var selectedRadioButton = jQuery('input[name="radiodealclub"]:checked').val();

				if ( selectedRadioButton == 'withdealclub' && is_annual_or_monthly == 174765 ) {

					jQuery('.annaul_text').css('color', 'green');
					jQuery('.monthly_text').css('color', '#2E3739');
					jQuery('.non_dc_text').css('color', '#2E3739');

				} else if (selectedRadioButton == 'withmonthlydealclub' ) {

					jQuery('.monthly_text').css('color', 'green');
					jQuery('.annaul_text').css('color', '#2E3739');
					jQuery('.non_dc_text').css('color', '#2E3739');

				} else if (selectedRadioButton == 'withoutdealclub' ) {

					jQuery('.monthly_text').css('color', '#2E3739');
					jQuery('.annaul_text').css('color', '#2E3739');

				} else {
					//

				}

				//change the text color of buttons on their selection for non dc and monthly

				if( is_dc_active_member == 1 && is_annual_or_monthly == 174761 ){ // if membership is monthly

					jQuery('input[name="radiodealclub"]').click(function() {

					var value = jQuery(this).val();
					if(value == "withdealclub"){
						jQuery('.annaul_text').css('color', '#2E3739');
						jQuery('.monthly_text').css('color', 'green');
						jQuery('.non_dc_text').css('color', '#9E9E98');//hide non dc this using color
					}
					else if ( value == "withmonthlydealclub"){
						jQuery('.monthly_text').css('color', 'green');
						jQuery('.annaul_text').css('color', '#2E3739');
						jQuery('.non_dc_text').css('color', '#9E9E98');//hide non dc this using color

					}
					else {
						//
					}

					});

				}

				//hide buy with monthly and non dc text on product page
				//acc to memebership - On defualt

				if (  is_dc_active_member == 1 ) {

					if( is_annual_or_monthly == 174761 ){ // if membership is monthly

						jQuery('.non_dc_text').css('color', '#9E9E98');//hide non dc this using color

					}else if (is_annual_or_monthly == 174765  ) { //if annaul

						jQuery('.non_dc_text').css('color', '#9E9E98');//hide non dc this using color
						jQuery('.monthly_text').css('color', '#9E9E98');//hide monthly this using color

					}

				}




                if((is_dc_in_cart == 1) || (is_dc_active_member == 1)){ // if DC in cart

					if( is_annual_or_monthly == 174761 ){ // if membership is monthly

						jQuery('.radio_section .withoutdcleft').addClass('disabled-wodc');
						jQuery('.radio_section .withoutdcright').addClass('disabled-wodc');
						jQuery('.sticky_add_to_cart1 .sticky_addtocart_right.sticky_siwodc_buynow_btn_sidebar').addClass('disabled wc-variation-selection-needed');
						jQuery('.radio_section .withoutdcpricerad').attr('disabled',true);

						// if monthly member then monthly radio button should be checked by default

						jQuery('.radio_section .withmonthlypricerad').attr('checked',true);

						//change product price page acc to monthly

						if ( monthly_dynamic_price != 0 ) {
							monthly_dynamic_price = parseFloat(monthly_dynamic_price).toFixed(2);
						}

						var updated_monthly_price = '$' + monthly_dynamic_price;
						jQuery('.df_show_price').text(updated_monthly_price);

						//hide and show add to cart and buy now button acc to monthly membership

						jQuery('.sidc_addtocart_btn_sidebar ').hide();
						jQuery('.sidc_buynow_btn_sidebar ').hide();
						jQuery('.simc_addtocart_btn_sidebar ').show();
						jQuery('.simc_buynow_btn_sidebar ').show();
						jQuery('.siwodc_addtocart_btn_sidebar  ').hide();
						jQuery('.siwodc_buynow_btn_sidebar ').hide();


					} else {
						jQuery('.radio_section .withoutdcleft').addClass('disabled-wodc');
						jQuery('.radio_section .withoutdcright').addClass('disabled-wodc');
						jQuery('.radio_section .montlydcleft').addClass('disabled-wodc');
						jQuery('.sticky_add_to_cart1 .sticky_addtocart_right.sticky_siwodc_buynow_btn_sidebar').addClass('disabled wc-variation-selection-needed');
						jQuery('.radio_section .withoutdcpricerad').attr('disabled',true);
						jQuery('.radio_section .withmonthlypricerad').attr('disabled',true);
					}

                }

                jQuery('input[name="radiodealclub"]').click(function() {

                    var value = jQuery(this).val();
                    if(value == "withdealclub"){
                        jQuery('.sidc_addtocart_btn_sidebar').show();
                        jQuery('.siwodc_addtocart_btn_sidebar').hide();
                        jQuery('.siwodc_buynow_btn_sidebar').hide();
                        jQuery('.sidc_buynow_btn_sidebar').show();
						jQuery('.simc_addtocart_btn_sidebar').hide();
                        jQuery('.simc_buynow_btn_sidebar').hide();
                    }
					else if ( value == "withmonthlydealclub"){
						jQuery('.sidc_addtocart_btn_sidebar').hide();
                        jQuery('.sidc_buynow_btn_sidebar').hide();
                        jQuery('.siwodc_addtocart_btn_sidebar').hide();
                        jQuery('.siwodc_buynow_btn_sidebar').hide();
						jQuery('.simc_addtocart_btn_sidebar').show();
                        jQuery('.simc_buynow_btn_sidebar').show();

					}
                    else if ( value == "withoutdealclub" ){
                        jQuery('.sidc_addtocart_btn_sidebar').hide();
                        jQuery('.sidc_buynow_btn_sidebar').hide();
						jQuery('.simc_addtocart_btn_sidebar').hide();
                        jQuery('.simc_buynow_btn_sidebar').hide();
                        jQuery('.siwodc_addtocart_btn_sidebar').show();
                        jQuery('.siwodc_buynow_btn_sidebar').show();
                    }else {
						//
					}

					//display price in frontend on the basis of option selected.
					var price = jQuery(this).data('price');

					if ( price != 0 ) {
						price = parseFloat(price).toFixed(2);
					}


					price = '$'+ price;

					jQuery('.df_show_price').text(price);


                });

            });

            </script>
                        <?php

					if ( $is_annual_or_monthly == 174761) { //when membership is monthly


						//Multiple Products add to cart

						//product id for dc annual
						$product_ids_annual = $productid.',174739';

						$annual_add_to_cart_url = esc_url_raw( add_query_arg( 'add-to-cart', $product_ids_annual, wc_get_cart_url() ) );


						?>
						<a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $product_ids_annual ) ); ?>" class="sidc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="simc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="siwodc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>



						<a href="<?php echo $annual_add_to_cart_url; ?>" class="sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="simc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="siwodc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>

						<?php



					}

                   	else if(is_dealclubmembership_in_cart() || is_user_an_active_member_wcm()){

                        ?>
                        <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="sidc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>
                        <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="siwodc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

                        <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>
                        <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="siwodc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>


                        <?php
                    }
                    else {

						//Multiple Products add to cart

						//product id for dc annual
						$product_ids_annual = $productid.',174739';

						$annual_add_to_cart_url = esc_url_raw( add_query_arg( 'add-to-cart', $product_ids_annual, wc_get_cart_url() ) );

						//product id for monthly

						$product_ids_monthly = $productid.',174721';

						$add_to_cart_url_monthly = esc_url_raw( add_query_arg( 'add-to-cart', $product_ids_monthly, wc_get_cart_url() ) );



						?>
						<a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $product_ids_annual ) ); ?>" class="sidc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $product_ids_monthly ) ); ?>" class="simc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="siwodc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>



						<a href="<?php echo $annual_add_to_cart_url; ?>" class="sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>

						<a style="display:none" href="<?php echo $add_to_cart_url_monthly; ?>" class="simc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>

						<a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="siwodc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>

						<?php

                    }

                ?>

            <?php
        }


    }
    if( $product->is_type( 'subscription' ) ){

        $productid = $product->get_id();
        $product_name = $product->get_name();
        $product_info = wc_get_product( $productid );
        $product_slug = $product_info->get_slug();
        $user_id = get_current_user_id();
        $memberships = wc_memberships_get_user_active_memberships($user_id);
        foreach ($memberships as $membership => $val) {
            if($val->status == "wcm-active"){
                $plan = $val->plan->name;
                break;
            }
        };
        $add_to_cart_url = esc_url_raw( add_query_arg( 'add-to-cart', $product_id, wc_get_cart_url() ) );
            if ( get_dynamic_price( $product->get_id() )  || get_dynamic_price( $product->get_id() ) == 0 ) {
                ?>
                <form>
                    <div class="radio_section">
                        <div>
                            <div class="withdcleft">
                                <label>Price</label>
                            </div>
                            <?php
                            if( get_dynamic_price( $product->get_id() ) == 0 || get_dynamic_price( $product->get_id()) == ""){
                            ?>
                            <div class="withoutdcright"><span class="wodcpriceins"><?php echo "$".number_format($product->get_regular_price(),2); ?></span>
                        </div>
                            <?php
                            }
                            else{
                            ?>
                        <div class="withoutdcright"><span class="wodcpriceins"><?php echo "$" . get_dynamic_price( $product->get_id() ); ?></span><span class="wodcpricedel"><?php echo "$".number_format($product->get_regular_price(),2); ?></span>
                            </div>
                        <?php
                            }

                        ?>
                    </div>
                </form>

                <div class="sticky_add_to_cart1">
                    <?php
                    if ( $plan == 'S2Member_Monthly' && $productid == 174721 )
                    {
                        echo "<a class='sticky_addtocart_left sticky_sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button membership-notice' style='font-size:15px'>You are already a Monthly DealClub Member</a>";

                    }
                    elseif ( $plan == 'S2Member_Annual' && $productid == 174739 )
                    {
                        echo "<a class='sticky_addtocart_left sticky_sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button membership-notice' style='font-size:15px'>You are already a Annual DealClub Member</a>";

                    }
                    else{
                    ?>
                    <a style='display:none' href='<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $product_id, wc_get_cart_url() ) ); ?>' class='sticky_addtocart_left sticky_sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button'>
                    <span class='membership'>Buy For</span><span class='dcpriceins membership'><?php echo '  $'.number_format($product->get_regular_price(),2); ?></span></a>
                    <?php
                    }
                    ?>


                </div>
                <script>
                    jQuery(document).ready(function(){
                        var is_dc_in_cart = "<?php echo is_dealclubmembership_in_cart(); ?>";
                        var is_dc_active_member = "<?php echo is_user_an_active_member_wcm(); ?>";

                        if((is_dc_in_cart == 1) || (is_dc_active_member == 1)){ // if DC in cart
                            jQuery('.radio_section .withoutdcleft').addClass('disabled-wodc');
                            jQuery('.radio_section .withoutdcright').addClass('disabled-wodc');
                            jQuery('.sticky_add_to_cart1 .sticky_addtocart_right.sticky_siwodc_buynow_btn_sidebar').addClass('disabled wc-variation-selection-needed');
                            jQuery('.radio_section .withoutdcpricerad').attr('disabled',true);
                        }
                        jQuery('input[name="radiodealclub"]').click(function() {
                            var value = jQuery(this).val();
                            if(value == "withdealclub"){
                                jQuery('.sidc_addtocart_btn_sidebar').show();
                                jQuery('.siwodc_addtocart_btn_sidebar').hide();
                                jQuery('.siwodc_buynow_btn_sidebar').hide();
                                jQuery('.sidc_buynow_btn_sidebar').show();
                            }
                            else{
                                jQuery('.sidc_addtocart_btn_sidebar').hide();
                                jQuery('.sidc_buynow_btn_sidebar').hide();
                                jQuery('.siwodc_addtocart_btn_sidebar').show();
                                jQuery('.siwodc_buynow_btn_sidebar').show();
                            }
                        });
                    });
                </script>
                            <?php

                        if(is_dealclubmembership_in_cart() || is_user_an_active_member_wcm()){
                            ?>

                            <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="sidc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>
                            <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="siwodc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

                            <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>
                            <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="siwodc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>


                    <?php
                        }
                        else{
                            ?>
                            <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="sidc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>
                            <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid ) ); ?>" class="siwodc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

                            <a href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="sidc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>
                            <a style="display:none" href="<?php echo esc_url_raw( add_query_arg( 'add-to-cart', $productid, wc_get_cart_url() ) ); ?>" class="siwodc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>


                    <?php
                        }

                    ?>

                <?php
            }

    }



}

add_action('wp_footer','hide_variation_add_to_cart_btn_on_simple_product_page');
function hide_variation_add_to_cart_btn_on_simple_product_page(){
	global $product;
	if (!is_product()){
			return;
	}

	if(is_product() && $product->is_type('simple')){

		?>
		<script>
			jQuery(document).ready(function(){
				jQuery('#variation_div').hide();
				jQuery('#variable-sidebar-div').hide();

			});

		</script>
		<?php
	}

	if(is_product() && $product->is_type('variable')){

		?>
		<script>
			jQuery(document).ready(function(){
				jQuery('#dealpage-details-sc').hide();
			});

		</script>
		<?php
	}


}

add_action('wp_footer','add_script_on_select_variation_value_change');
function add_script_on_select_variation_value_change(){
	global $product;
	if (!is_product()){
			return;
	}

	$price = $product->get_price_html();

	if ( $product->is_type('variable')) {

		$product_id = $product->get_id();

		$dynamic_pricearr = get_all_dynamic_prices_with_id_as_key($product->get_id());
		$dynamic_price = json_encode($dynamic_pricearr);
		$assoc_dynamic_price = array_values($dynamic_pricearr);


		?>

		<div class="var_price">
		<div class="df_show_discounted_price_dynamically">

					<span class="variation_show_price"><?php echo $assoc_dynamic_price['0']['dynamic_price_array_annual']; ?></span>

					<span class="df_price_del"><?php echo $assoc_dynamic_price['0']['regular_price']; ?></span>
				</div>

		</div>


		<div class="radio_sect_var">

			<div>
				<div class="withdcleft">
				    <input type="radio" id="varwithdealclub" checked name="varradiodealclub" value="varwithdealclub">
				    <label class="var_annaul_text">25% OFF with $49/Year Membership</label>
			    </div>

			</div>
			<div style="clear: both;">
				<div class="withmonthlyleft">
						<input type="radio" id="varwithmonthlydealclub" class="varwithmonthlypricerad" name="varradiodealclub" value="varwithmonthlydealclub">
						<label class="var_monthly_text">10% OFF with $9/Month Membership</label>
					</div>

			</div>
			<div style="clear: both;">
			    <div class="withoutdcleft">
			    	<input type="radio" class="withoutdcpricerad" id="varwithoutdealclub" name="varradiodealclub" value="varwithoutdealclub">
			    	<label class="var_non_dc_text">Buy at Deal Price</label>
			    </div>

			</div>
	</div>
	<div class="sticky_add_to_cart1">
		<a style="display:none" href="javascript:void(0);" id="scroll_to_top" class="sticky_addtocart_left sticky_dc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button"><span>Buy Now</span></a>
	</div>

		  	    <a style="display:none" href="" class="dc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>
				<a style="display:none" href="" class="mc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>
				<a style="display:none" href="" class="wodc_addtocart_btn_sidebar single_add_to_cart_button button alt wp-element-button">Add To Cart</a>

				<a style="display:none" href="" class="dc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>
				<a style="display:none" href="" class="mc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>
				<a style="display:none" href="" class="wodc_buynow_btn_sidebar single_add_to_cart_button button alt wp-element-button">Buy Now</a>


			<script>
				jQuery(document).ready(function(){
					jQuery('#variation_div').show();
					//onLoad show disabled radio buttons
					var is_dc_active_member = "<?php echo is_user_an_active_member_wcm(); ?>";
					var is_annual_or_monthly = "<?php echo is_user_has_annual_or_monthly_memebership(); ?>";

					jQuery('.radio_sect_var .withoutdcleft').addClass('disabled-wodc');
					jQuery('.radio_sect_var .withoutdcright').addClass('disabled-wodc');
					jQuery('.sticky_add_to_cart1 .sticky_addtocart_left.sticky_dc_buynow_btn_sidebar').addClass('disabled wc-variation-selection-needed');
					jQuery('.sticky_add_to_cart1 .sticky_addtocart_right.sticky_wodc_buynow_btn_sidebar').addClass('disabled wc-variation-selection-needed');
					jQuery('.radio_sect_var .withoutdcpricerad').attr('disabled',true);
					jQuery('.mo-disabled-wodc').click(function(){
						alert('Please select some product options before adding this product to your cart.');
					});


					// jQuery for scroll to top
					jQuery("#scroll_to_top").click(function() {
						var targetDiv = jQuery(".variations_form");
						var offset = 180; // Adjust the offset value as needed
						var targetPosition = targetDiv.offset().top - offset;

						jQuery("html, body").animate({ scrollTop: targetPosition }, "slow");


					});




					jQuery('.woocommerce-variation-add-to-cart .single_add_to_cart_button').hide();
					jQuery('input[name="varradiodealclub"]').click(function() {
							var value = jQuery(this).val();
							if(value == "varwithdealclub"){
									jQuery('.dc_addtocart_btn_sidebar').show();
									jQuery('.wodc_addtocart_btn_sidebar').hide();
									jQuery('.mc_addtocart_btn_sidebar').hide();
									jQuery('.mc_buynow_btn_sidebar').hide();
									jQuery('.wodc_buynow_btn_sidebar').hide();
									jQuery('.dc_buynow_btn_sidebar').show();
							}
							else if(value == "varwithmonthlydealclub"){
									jQuery('.dc_addtocart_btn_sidebar').hide();
									jQuery('.dc_buynow_btn_sidebar').hide();
									jQuery('.wodc_addtocart_btn_sidebar').hide();
									jQuery('.wodc_buynow_btn_sidebar').hide();
									jQuery('.mc_addtocart_btn_sidebar').show();
									jQuery('.mc_buynow_btn_sidebar').show();
							}
							else if(value == "varwithoutdealclub"){
									jQuery('.dc_addtocart_btn_sidebar').hide();
									jQuery('.dc_buynow_btn_sidebar').hide();
									jQuery('.mc_addtocart_btn_sidebar').hide();
									jQuery('.mc_buynow_btn_sidebar').hide();
									jQuery('.wodc_addtocart_btn_sidebar').show();
									jQuery('.wodc_buynow_btn_sidebar').show();
							}
						});

							//find out the which radio button is selected and make its text green
							var selectedValue = jQuery('input[name="varradiodealclub"]:checked').val();

							// change the values acc to the option selected (Non DC )

							if( selectedValue == "varwithdealclub" && is_annual_or_monthly == 174765 ){
								jQuery('.var_annaul_text').css('color', 'green');
								jQuery('.var_monthly_text').css('color', '#2E3739');
								jQuery('.var_non_dc_text').css('color', '#2E3739');

							}
							else if ( selectedValue == "varwithmonthlydealclub"){
								jQuery('.var_monthly_text').css('color', 'green');
								jQuery('.var_annaul_text').css('color', '#2E3739');
								jQuery('.var_non_dc_text').css('color', '#2E3739');
							}
							else if ( selectedValue == "varwithoutdealclub" ){
								jQuery('.var_monthly_text').css('color', '#2E3739');
								jQuery('.var_annaul_text').css('color', '#2E3739');
							}else {
								//
							}

							//change the text color of buttons on their selection for non dc and monthly

							if( is_dc_active_member == 1 && is_annual_or_monthly == 174761 ){ // if membership is monthly

								console.log('I am monthly');

								jQuery('input[name="varradiodealclub"]').click(function() {

									var value = jQuery(this).val();

									if(value == "varwithdealclub"){
										jQuery('.var_annaul_text').css('color', '#2E3739');
										jQuery('.var_monthly_text').css('color', 'green');
										jQuery('.var_non_dc_text').css('color', '#9E9E98');//hide non dc this using color
									}
									else if ( value == "varwithmonthlydealclub"){
										jQuery('.var_monthly_text').css('color', 'green');
										jQuery('.var_annaul_text').css('color', '#2E3739');
										jQuery('.var_non_dc_text').css('color', '#9E9E98');//hide non dc this using color

									}
									else {
										//
									}

								});

							}else { //for non dc

							}


							//hide buy with monthly and non dc text on product page
							//acc to memebership - On defualt

							if (  is_dc_active_member == 1 ) {

								if( is_annual_or_monthly == 174761 ){ // if membership is monthly

									jQuery('.var_non_dc_text').css('color', '#9E9E98');//hide non dc this using color
									jQuery('.var_monthly_text').css('color', 'green');

								}else if (is_annual_or_monthly == 174765  ) { //if annaul

									jQuery('.var_non_dc_text').css('color', '#9E9E98');//hide non dc this using color
									jQuery('.var_monthly_text').css('color', '#9E9E98');//hide monthly this using color

								}

							}

						/*************************************************************/
						var dynamic_price = <?php echo $dynamic_price; ?>;
						var is_dc_in_cart = "<?php echo is_dealclubmembership_in_cart(); ?>";
						var is_dc_active_member = "<?php echo is_user_an_active_member_wcm(); ?>";
						var is_annual_or_monthly = "<?php echo is_user_has_annual_or_monthly_memebership(); ?>";



						jQuery('.single_variation_wrap').prepend(jQuery('.radio_sect_var'));
						jQuery('.dc_addtocart_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .radio_sect_var'));
						jQuery('.dc_addtocart_btn_sidebar').show();
						jQuery('.dc_buynow_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .dc_addtocart_btn_sidebar'));
						jQuery('.dc_buynow_btn_sidebar').show();

						jQuery('.mc_addtocart_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .radio_sect_var'));
						jQuery('.mc_addtocart_btn_sidebar').hide();
						jQuery('.mc_buynow_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .dc_addtocart_btn_sidebar'));
						jQuery('.mc_buynow_btn_sidebar').hide();

						//div added where price will get updated
						jQuery('.variation_price').prepend(jQuery('.var_price'));




						jQuery(document).on('found_variation', function( e, v ) {
							jQuery('.radio_sect_var .withoutdcpricerad').prop('disabled',false);
							jQuery('.radio_sect_var .withoutdcleft').removeClass('disabled-wodc');
							jQuery('.radio_sect_var .withoutdcright').removeClass('disabled-wodc');
							jQuery('.sticky_add_to_cart1 .sticky_addtocart_left.sticky_dc_buynow_btn_sidebar').removeClass('disabled wc-variation-selection-needed');
							jQuery('.sticky_add_to_cart1 .sticky_addtocart_right.sticky_wodc_buynow_btn_sidebar').removeClass('disabled wc-variation-selection-needed');
							jQuery('.mo-disabled-wodc').click(function(){});

							jQuery('.single_variation_wrap .woocommerce-variation-price').hide();

						if(jQuery(window).width() < 768)
						{
							var inputvarval = jQuery('#mob_variation input.variation_id').val();
						}
						else{
							var inputvarval = jQuery('input.variation_id').val();
						}

						//var inputvarval = jQuery('#mob_variation input.variation_id').val();
						 if( inputvarval != ''){
							 if(is_dc_in_cart != 1) { // if DC not in cart

									var interval = inputvarval;
									var selectedValue = jQuery('input[name="varradiodealclub"]:checked').val();
									var var_ids = inputvarval+",174739";
									var var_monthly_id = inputvarval+",174721";

									jQuery('a.dc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+var_ids);
									jQuery('a.dc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_ids);
									jQuery('a.mc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+var_monthly_id);
									jQuery('a.mc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_monthly_id);
									jQuery('a.sticky_wodc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+inputvarval);
									jQuery('a.sticky_dc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_ids);
									jQuery('.single_variation').remove();

									//Update Radio buttons prices based on current selected values
									jQuery('.dcpriceins').html(dynamic_price[inputvarval]['dynamic_price_array_annual']);
									jQuery('.dcpricedel').html(dynamic_price[inputvarval]['regular_price']);
									jQuery('.monthlypriceins').html(dynamic_price[inputvarval]['dynamic_price_array_monthly']);
									jQuery('.monthlypricedel').html(dynamic_price[inputvarval]['regular_price']);
									jQuery('.wodcpriceins').html(dynamic_price[inputvarval]['sale_price']);
									jQuery('.wodcpricedel').html(dynamic_price[inputvarval]['regular_price']);

									//when user is non dc - then update the annual price by defualt in the product page
									//accoding to the variation

									jQuery('.variation_show_price').html(dynamic_price[inputvarval]['dynamic_price_array_annual']);

									//find out the value of radio button (Non DC )

									var selectedValue = jQuery('input[name="varradiodealclub"]:checked').val();

									// change the values acc to the option selected (Non DC )

									if(selectedValue == "varwithdealclub"){
										jQuery('.variation_show_price').html(dynamic_price[inputvarval]['dynamic_price_array_annual']);

									}
									else if ( selectedValue == "varwithmonthlydealclub"){
										jQuery('.variation_show_price').html(dynamic_price[inputvarval]['dynamic_price_array_monthly']);
									}
									else if ( selectedValue == "varwithoutdealclub" ){
										jQuery('.variation_show_price').html(dynamic_price[inputvarval]['sale_price']);
									}else {
										//
									}


									if (jQuery('.wodc_addtocart_btn_sidebar').length ==1 ) {
 								 		jQuery('.wodc_addtocart_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .radio_sect_var'));
 							 		}

 									if(jQuery('.wodc_buynow_btn_sidebar').length == 1){
 										jQuery('.wodc_buynow_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .wodc_addtocart_btn_sidebar'));
 									}

 								 	jQuery('a.wodc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+inputvarval);
 								 	jQuery('a.wodc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+inputvarval);

							 }
								if((is_dc_in_cart == 1) || (is_dc_active_member == 1)){ // if DC in cart

									var var_id = inputvarval;

									if( is_annual_or_monthly == 174761 ){ // if membership is monthly disable the without dc option

											var var_ids = inputvarval+",174739";
											var var_monthly_id = inputvarval;
											jQuery('.radio_sect_var .withoutdcpricerad').attr('disabled',true);
											//if monthly member then add annual when user buys with dc
											jQuery('a.dc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+var_ids);
											jQuery('a.dc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_ids);

											//when with monthly is clicked on variation product should get added

											jQuery('a.mc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+var_monthly_id);
											jQuery('a.mc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_monthly_id);

											// if monthly member then monthly radio button should be checked by default

											jQuery('.radio_sect_var .varwithmonthlypricerad').attr('checked',true);


											//update the value of price acc to monthly by default when membership is monthly
											jQuery('.variation_show_price').html(dynamic_price[inputvarval]['dynamic_price_array_monthly']);

											//find out the value of radio button (Monthly )

											var selectedValue = jQuery('input[name="varradiodealclub"]:checked').val();

											// change the values acc to the option selected (Monthly )

											if(selectedValue == "varwithdealclub"){
												jQuery('.variation_show_price').html(dynamic_price[inputvarval]['dynamic_price_array_annual']);

											}
											else if ( selectedValue == "varwithmonthlydealclub"){
												jQuery('.variation_show_price').html(dynamic_price[inputvarval]['dynamic_price_array_monthly']);
											}
											else if ( selectedValue == "varwithoutdealclub" ){
												jQuery('.variation_show_price').html(dynamic_price[inputvarval]['sale_price']);
											}else {
												//
											}

											//add to cart and buy now button acc to monthly member
											if( selectedValue != "varwithdealclub" ){

											jQuery('.dc_addtocart_btn_sidebar ').hide();
											jQuery('.dc_buynow_btn_sidebar ').hide();
											jQuery('.wodc_buynow_btn_sidebar ').hide();
											jQuery('.wodc_addtocart_btn_sidebar').hide();
											jQuery('.mc_addtocart_btn_sidebar').show();
											jQuery('.mc_buynow_btn_sidebar').show();

											}

									}
										else if ( is_annual_or_monthly == 174765 ) { //if membership is annual disable the without dc & with monthly option

											jQuery('.radio_sect_var .varwithmonthlypricerad').attr('disabled',true);

											var var_ids = inputvarval;

											jQuery('a.dc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+var_ids);
											jQuery('a.dc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_ids);


										}

									jQuery('.wodc_addtocart_btn_sidebar').remove();



									//Update Radio buttons prices based on current selected values
									jQuery('.dcpriceins').html(dynamic_price[var_id]['dynamic_price_array_annual']);
									jQuery('.dcpricedel').html(dynamic_price[var_id]['regular_price']);

									jQuery('.monthlypriceins').html(dynamic_price[var_id]['dynamic_price_array_monthly']);
									jQuery('.monthlypricedel').html(dynamic_price[var_id]['regular_price']);

									jQuery('.wodcpriceins').html(dynamic_price[var_id]['sale_price']);
									jQuery('.wodcpricedel').html(dynamic_price[var_id]['regular_price']);

									jQuery('.radio_sect_var .withoutdcleft').addClass('disabled-wodc');
									jQuery('.radio_sect_var .withoutdcright').addClass('disabled-wodc');
									jQuery('.sticky_add_to_cart1 .sticky_addtocart_right.sticky_wodc_buynow_btn_sidebar').addClass('disabled wc-variation-selection-needed');
									jQuery('.radio_sect_var #varwithoutdealclub').attr('disabled',true);

									if (jQuery('.dc_addtocart_btn_sidebar').length ==1 ) {
											jQuery('.dc_addtocart_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .radio_sect_var'));
										}

										if(jQuery('.dc_buynow_btn_sidebar').length == 1){
											jQuery('.dc_buynow_btn_sidebar').insertAfter(jQuery('.single_variation_wrap .dc_addtocart_btn_sidebar'));
										}

									//  jQuery('a.dc_addtocart_btn_sidebar').attr('href', '?add-to-cart='+var_id);
									//  jQuery('a.dc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_id);
									// jQuery('.sticky_add_to_cart1 .sticky_addtocart_left.sticky_dc_buynow_btn_sidebar').attr('href', '/cart/?add-to-cart='+var_id);


								}

									//product page values changes on selection

									jQuery('input[name="varradiodealclub"]').click(function() {

									var value = jQuery(this).val();
									var var_id = inputvarval;
									if(value == "varwithdealclub"){
										jQuery('.variation_show_price').html(dynamic_price[var_id]['dynamic_price_array_annual']);

									}
									else if ( value == "varwithmonthlydealclub"){
										jQuery('.variation_show_price').html(dynamic_price[var_id]['dynamic_price_array_monthly']);
									}
									else if ( value == "varwithoutdealclub" ){
										jQuery('.variation_show_price').html(dynamic_price[var_id]['sale_price']);
									}else {
										//
									}



									});


					 }

				 });
				});
				</script>
<?php


 }
}


/*
 * Redirect to cart page by removing arguments ( product ids given on single product page add to cart )
 */

 add_action('woocommerce_add_to_cart_redirect', 'df_add_to_cart_redirect_without_arg_in_url');

function df_add_to_cart_redirect_without_arg_in_url($url = false) {

     // Redirect back to the original page, without the 'add-to-cart' parameter.
     return get_bloginfo('url').add_query_arg(array(), remove_query_arg('add-to-cart'));

}

/* Function to add multiple products simultaneously in cart - start */

function pw_add_multiple_products_to_cart( $url = false ) {
	// Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
	if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
		return;
	}

	// Remove WooCommerce's hook, as it's useless (doesn't handle multiple products).
	remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

	$product_ids = explode( ',', $_REQUEST['add-to-cart'] );
	$count       = count( $product_ids );
	$number      = 0;

	foreach ( $product_ids as $id_and_quantity ) {
		// Check for quantities defined in curie notation (<product_id>:<product_quantity>)

		$id_and_quantity = explode( ':', $id_and_quantity );
		$product_id = $id_and_quantity[0];

		$_REQUEST['quantity'] = ! empty( $id_and_quantity[1] ) ? absint( $id_and_quantity[1] ) : 1;

		if ( ++$number === $count ) {
			// Ok, final item, let's send it back to woocommerce's add_to_cart_action method for handling.
			$_REQUEST['add-to-cart'] = $product_id;

			return WC_Form_Handler::add_to_cart_action( $url );
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			continue;
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );

		// Variable product handling
		if ( 'variable' === $add_to_cart_handler ) {
			woo_hack_invoke_private_method( 'WC_Form_Handler', 'add_to_cart_handler_variable', $product_id );

		// Grouped Products
		} elseif ( 'grouped' === $add_to_cart_handler ) {
			woo_hack_invoke_private_method( 'WC_Form_Handler', 'add_to_cart_handler_grouped', $product_id );

		// Custom Handler
		} elseif ( has_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler ) ){
			do_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler, $url );

		// Simple Products
		} else {
			woo_hack_invoke_private_method( 'WC_Form_Handler', 'add_to_cart_handler_simple', $product_id );
		}
	}
}

// Fire before the WC_Form_Handler::add_to_cart_action callback.
add_action( 'wp_loaded', 'pw_add_multiple_products_to_cart', 15 );

function woo_hack_invoke_private_method( $class_name, $methodName ) {
	if ( version_compare( phpversion(), '5.3', '<' ) ) {
		throw new Exception( 'PHP version does not support ReflectionClass::setAccessible()', __LINE__ );
	}

	$args = func_get_args();
	unset( $args[0], $args[1] );
	$reflection = new ReflectionClass( $class_name );
	$method = $reflection->getMethod( $methodName );
	$method->setAccessible( true );

	//$args = array_merge( array( $class_name ), $args );
	$args = array_merge( array( $reflection ), $args );
	return call_user_func_array( array( $method, 'invoke' ), $args );
}
/* Function to add multiple products simultaneously in cart - end */

function df_custom_add_to_cart_message() {
	if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) :
	    $message = '<div class="product_added_msg">Deal added to cart</div>';
	else :
	    $message =  '<div class="product_added_msg">Deal added to cart</div>';
	endif;
	return $message;
}
add_filter( 'wc_add_to_cart_message', 'df_custom_add_to_cart_message' );


/* Added to change wp login error message  */
add_filter( 'woocommerce_registration_error_email_exists', function ( $message ) {
    return preg_replace( '/href="#" class="showlogin"/', sprintf( 'href="%s"', home_url('account') ), $message );

} );

add_filter( 'woocommerce_shortcode_products_query', function( $query_args, $atts, $loop_name ){
    if( $loop_name == 'products' ){
        $query_args['meta_query'] = array( array(
            'key'     => '_stock_status',
            'value'   => 'outofstock',
            'compare' => 'NOT LIKE',
        ) );
    }
    return $query_args;
    }, 10, 3);

/* Added by Prasada - New Product sidebar UI changes - end */

/* Change the sort by dropdown options text  added by Vaibhav*/
add_filter( 'woocommerce_catalog_orderby', 'dealfuel_rename_default_sorting_options' );

function dealfuel_rename_default_sorting_options($options){

	$options['popularity'] = 'Popularity'; // rename
	$options['rating'] = 'Average Rating';
	$options['date'] = 'Latest';
	$options['price'] = 'Price: low to high';
	$options['price-desc'] = 'Price: high to low';

	return $options;

}



/**
 *
 * Function to add dynamic price groups
 *
*/

// add_action('wp_footer','add_price_groups');

function add_price_groups ( ) {

	// Call the function to read the CSV file and store the data in an array
	$csv_data = read_csv_file();

	// loop through the data and update price groups using product ID and annual,monthly price.
	foreach ($csv_data as $row) {

		if ( $row[1] == 'Product ID' ) { //do not include 1st row

			continue;

		} else {

			//fetch values from the row ( index which are given in row are according to the sheet)
			$product_id = $row[1];
			$annual_price = $row[7];
			$monthly_price = $row[6];
			$sale_price = $row[4];



			//set price only upto 2 decimal points
			$annual_price =  round($annual_price, 2);
			$monthly_price =  round($monthly_price, 2);

			$annual_commission = $row[10];
			$monthly_commission = $row[9];
			$non_dc_commission = $row[8];



			//replace the % sign in commission

			$annual_commission = str_replace('%', '', $annual_commission);
			$monthly_commission = str_replace('%', '', $monthly_commission);
			$non_dc_commission = str_replace('%', '', $non_dc_commission);

			//set commission only upto 2 decimal points

			$annual_commission =  round($annual_commission, 2);
			$monthly_commission =  round($monthly_commission, 2);
			$non_dc_commission =  round($non_dc_commission, 2);





			//meta keys for annual and monthly commisions. we will use keys to update the commision in database
			$annual_comm_meta_key = '_product_vendors_commission_'.$annual_price;
			$monthly_comm_meta_key = '_product_vendors_commission_'.$monthly_price;
			$non_dc_comm_meta_key = '_product_vendors_commission_'.$sale_price;


			//annual price group
			$annual_array = array(
				'annual' => array(
					'conditions_type' => 'all',
					'conditions' => array(
						'1' => array(
							'type' => 'apply_to',
							'args' => array(
								'applies_to' => 'membership',
								'memberships' => array(
									'0' => '174765'
								)
							)
						)
					),
					'collector' => array(
						'type' => 'product'
					),
					'mode' => 'continuous',
					'date_from' => '',
					'date_to' => '',
					'rules' => array(
						'1' => array(
							'from' => '',
							'to' => '',
							'type' => 'fixed_price',
							'amount' => '1015'
						)
					),
					'blockrules' => array(
						'1' => array(
							'from' => '',
							'adjust' => '',
							'type' => 'fixed_adjustment',
							'amount' => '',
							'repeating' => 'no'
						)
					)
				)
			);

			//monthly price group
			$monthly_array = array(
				'monthly' => array(
					'conditions_type' => 'all',
					'conditions' => array(
						'1' => array(
							'type' => 'apply_to',
							'args' => array(
								'applies_to' => 'membership',
								'memberships' => array(
									'0' => '174761'
								)
							)
						)
					),
					'collector' => array(
						'type' => 'product'
					),
					'mode' => 'continuous',
					'date_from' => '',
					'date_to' => '',
					'rules' => array(
						'1' => array(
							'from' => '',
							'to' => '',
							'type' => 'fixed_price',
							'amount' => '786'
						)
					),
					'blockrules' => array(
						'1' => array(
							'from' => '',
							'adjust' => '',
							'type' => 'fixed_adjustment',
							'amount' => '',
							'repeating' => 'no'
						)
					)
				)
			);

			$annual_array['annual']['rules']['1']['amount'] = $annual_price;
			$monthly_array['monthly']['rules']['1']['amount'] = $monthly_price;


			$mergedArray = array_merge($annual_array, $monthly_array);

			// update the price groups in the db

			update_post_meta($product_id, '_pricing_rules', $mergedArray);  //uncomment when use

			// update the commisions for the product

			update_post_meta($product_id, $non_dc_comm_meta_key, $non_dc_commission );   //uncomment when use
			update_post_meta($product_id, $annual_comm_meta_key, $annual_commission );   //uncomment when use
			update_post_meta($product_id, $monthly_comm_meta_key, $monthly_commission );  //uncomment when use


		}


	}

	echo 'Done';

}


/**
* read csv file
*
*/

function read_csv_file() {

	//change this url according to live site

	// $csv_file = '/chroot/home/af57624e/59e4907299.nxcli.io/html/wp-content/simple.csv'; // Path to your CSV file - simple - for DF test site

	// $csv_file = '/chroot/home/a335789e/1461794109.nxcli.io/html/wp-content/simple.csv'; // Path to your CSV file - simple - for DF Nexcess

	// $csv_file = '/chroot/home/af57624e/59e4907299.nxcli.io/html/wp-content/variations.csv'; // Path to your CSV file - variable -for DF test

	$csv_file = '/chroot/home/a335789e/1461794109.nxcli.io/html/wp-content/variations.csv'; // Path to your CSV file - variable - for DF Nexcess


	$data = array();

	if (($handle = fopen($csv_file, "r")) !== false) {
		while (($row = fgetcsv($handle, 1000, ",")) !== false) {
			$data[] = $row;
		}
		fclose($handle);
	}

	return $data;

}



/**
* script for variation products
*/

// add_action('wp_footer','add_price_groups_variation_products');

function  add_price_groups_variation_products () {

		echo "HELLO WORLD";

		// Call the function to read the CSV file and store the data in an array
		$csv_data = read_csv_file();


		// $parentID = 1376652;


		foreach ($csv_data as $row) {

			if ( $row[2] == 'Product ID' ) { //do not include 1st row

				continue;

			} else {

				$parentID = $row[2];

				$children = [];

				// Find the children of the parent
				foreach ($csv_data as $item) {
				  if ($item[2] == $parentID) {
					$children[] = $item[3];
				  }
				}

				//array to store the price groups
				$all_price_groups_variations = [];

				// Find annual and monthly prices for each child
				foreach ($csv_data as $item) {


				  $childID = $item[3];

				  if (in_array($childID, $children)) {

					//price
					$childAnnualPrice = $item[7];
					$childMonthlyPrice = $item[6];


					//commissions

					$annual_commission = $item[10];
					$monthly_commission = $item[9];

					if ( $annual_commission == '' ){
						$annual_commission = 0;
					}

					if ( $monthly_commission == '' ){
						$monthly_commission = 0;
					}

					//replace the % sign in commission

					$annual_commission = str_replace('%', '', $annual_commission);
					$monthly_commission = str_replace('%', '', $monthly_commission);

					//set commission only upto 2 decimal points

					$annual_commission =  round($annual_commission, 2);
					$monthly_commission =  round($monthly_commission, 2);

					//set annula and monthly price only upto 2 decimal points

					$childAnnualPrice =  round($childAnnualPrice, 2);
					$childMonthlyPrice =  round($childMonthlyPrice, 2);

					//meta keys for annaul and monthly commisions. we will use keys to update the commision in database
					$annaul_comm_meta_key = '_product_vendors_commission_'.$childAnnualPrice;
					$monthly_comm_meta_key = '_product_vendors_commission_'.$childMonthlyPrice;

					//uncomment when run the script

					// update the commisions for the product

					update_post_meta($parentID, $annaul_comm_meta_key, $annual_commission );
					update_post_meta($parentID, $monthly_comm_meta_key, $monthly_commission );


					//code for setting up the price groups

					$variation = $childID;

					$annual_price = $childAnnualPrice;
					$monthly_price = $childMonthlyPrice;


					//set price only upto 2 decimal points
					$annual_price =  round($annual_price, 2);
					$monthly_price =  round($monthly_price, 2);

					//annual price group
					$annual_array = array(
						'annual' => array(
							'conditions_type' => 'all',
							'conditions' => array(
								'1' => array(
									'type' => 'apply_to',
									'args' => array(
										'applies_to' => 'membership',
										'memberships' => array(
											'0' => '174765'
										)
									)
								)
							),
							'collector' => array(
								'type' => 'product'
							),
							'variation_rules' => array (
								'args' => array (
									'type' => 'variations',
									'variations' => array (
										'0' => '1376668'
									)
								)
							),
							'mode' => 'continuous',
							'date_from' => '',
							'date_to' => '',
							'rules' => array(
								'1' => array(
									'from' => '',
									'to' => '',
									'type' => 'fixed_price',
									'amount' => '1015'
								)
							),
							'blockrules' => array(
								'1' => array(
									'from' => '',
									'adjust' => '',
									'type' => 'fixed_adjustment',
									'amount' => '',
									'repeating' => 'no'
								)
							)
						)
					);

					//monthly price group
					$monthly_array = array(
						'monthly' => array(
							'conditions_type' => 'all',
							'conditions' => array(
								'1' => array(
									'type' => 'apply_to',
									'args' => array(
										'applies_to' => 'membership',
										'memberships' => array(
											'0' => '174761'
										)
									)
								)
							),
							'collector' => array(
								'type' => 'product'
							),
							'variation_rules' => array (
								'args' => array (
									'type' => 'variations',
									'variations' => array (
										'0' => '1376668'
									)
								)
							),
							'mode' => 'continuous',
							'date_from' => '',
							'date_to' => '',
							'rules' => array(
								'1' => array(
									'from' => '',
									'to' => '',
									'type' => 'fixed_price',
									'amount' => '786'
								)
							),
							'blockrules' => array(
								'1' => array(
									'from' => '',
									'adjust' => '',
									'type' => 'fixed_adjustment',
									'amount' => '',
									'repeating' => 'no'
								)
							)
						)
					);

					$annual_array['annual']['rules']['1']['amount'] = $annual_price;
					$monthly_array['monthly']['rules']['1']['amount'] = $monthly_price;

					//change variation here
					$annual_array['annual']['variation_rules']['args']['variations'][0] = $variation;
					$monthly_array['monthly']['variation_rules']['args']['variations'][0] = $variation;

					//change array key acc to variation for annual array

					$newKey_for_annual_array = 'annual_'.$variation;

					$annual_array[$newKey_for_annual_array] = $annual_array['annual'];
					unset($annual_array['annual']);

					//change array key acc to variation for monthly array

					$newKey_for_monthly_array = 'monthly_'.$variation;

					$monthly_array[$newKey_for_monthly_array] = $monthly_array['monthly'];
					unset($monthly_array['monthly']);


					$mergedArray = array_merge($annual_array, $monthly_array);

					array_push($all_price_groups_variations, $mergedArray);



				  }



				}

				$final_price_groups_array = array();

				foreach ($all_price_groups_variations as $subArray) {
					foreach ($subArray as $key => $value) {
						$final_price_groups_array[$key] = $value;
					}
				}

				//uncomment when run the script

				// update the price groups in the db
				update_post_meta($parentID, '_pricing_rules', $final_price_groups_array);



			}


		}

		echo "Done";
}


/* Added by Prasada - Checkout page new UI changes 2022 - start */
add_action('wp_footer','add_checkout_accordion',10,5);
function add_checkout_accordion(){
if(is_checkout()){
         ?>
          <?php

	//On Checkout page - Accordion
	?>
	<script>
	jQuery(document).on('checkout_error', function(){
		jQuery(".woocommerce-checkout form.woocommerce-checkout.processing").css("position","static");

		jQuery(".woocommerce-form-login").hide();
		jQuery(".wc-social-login").hide();
		jQuery('.woocommerce-billing-fields__field-wrapper').show();
		jQuery('.woocommerce-account-fields').show();
		jQuery('#moosend_subscribe_checkbox_field').show();
		jQuery('.woocommerce-additional-fields').show();
		jQuery(".register-div i").toggleClass("fa-angle-down fa-angle-right");

		jQuery('html, body').stop();

			 jQuery('html, body').animate({
              scrollTop: jQuery(".register-div").offset().top
        }, 1000);
});
			jQuery(document).ready(function(){
				var ismobile = "<?php echo wp_is_mobile(); ?>";
				jQuery(".showlogin").removeClass("toggleweightless");
				 jQuery(".showlogin").addClass("toggleweightmore");
			//To set the footer position dynamic based on page content on Checkout page
			x = jQuery('#order_review').height()+20;
			y = jQuery(window).height();
			if (x+100<=y){
				jQuery('.site-footer').css('position','relative');
				jQuery('.site-footer').css('top', y-100+'px');// again 100 is the height of your footer
				jQuery('.site-footer').css('display', 'block');
			}else{
				jQuery('.site-footer').css('position','relative');
				if(ismobile == 1){
					jQuery('.site-footer').css('top', '0 !important');
				}
				else{
					jQuery('.site-footer').css('top', x-200+'px');
				}
				jQuery('.site-footer').css('display', 'block');
			}
			var siteurl = "<?php echo get_site_url(); ?>";
			var is_user_logged_in = "<?php echo is_user_logged_in(); ?>";
			var is_dc_active_member = "<?php echo is_user_an_active_member_wcm(); ?>";
			var is_coupon_applied = "<?php echo count( WC()->cart->get_applied_coupons() ); ?>";

			// Change Apply Coupon button text if coupon code is applied
				jQuery('.woocommerce-checkout form.checkout_coupon').on('submit',function(){

					vardata= {
					        'action':'check_coupon_via_ajax',
					        'code':jQuery('input[name="coupon_code"]').val()
					    };
					    jQuery.post('/wp-admin/admin-ajax.php', vardata, function(response) {
					        if(response.status==0){
					            // -- ERROR
										jQuery('#order_review button[name="apply_coupon"]').text("Apply Coupon");
										jQuery('#order_review button[name="apply_coupon"]').removeClass('disable-coupon');
					        } else {
					            // -- SUCCESS
										jQuery('#order_review button[name="apply_coupon"]').text("Applied");
										jQuery('#order_review button[name="apply_coupon"]').addClass('disable-coupon');
					        }
					        }, 'json');

				});
				jQuery( document.body ).on( 'click', '.woocommerce-checkout .woocommerce-remove-coupon', function(){
					if(is_coupon_applied == 0){
						jQuery('#order_review button[name="apply_coupon"]').text("Apply Coupon");
						jQuery('#order_review button[name="apply_coupon"]').removeClass('disable-coupon');
					}
				});

// changes made by Akshay style='width: 40%; added class checkout_heading'

			jQuery("<h2 class='heading-checkout-table redeem_points_div_head checkout_heading' >Credit Points</h2>").insertBefore(".wc_points_redeem_earn_points");

			jQuery(".wc-social-login").hide();
			if(is_dc_active_member == 1){
				jQuery('.woocommerce-checkout #customer_details').css('margin-top','4em');
				jQuery('.woocommerce-checkout #customer_details').css('width','40.2%');
				jQuery('.woocommerce-checkout #customer_details').css('float','left');
			}
			if(is_user_logged_in == 1){
				jQuery('.checkout-dcmaildiv').show();
				jQuery('.checkout-dcmail').show();
				jQuery('#moosend_subscribe_checkbox_field').show();
				jQuery('.woocommerce-checkout #custom_customer_details').css('margin-top','4em');

			}
			else {
				jQuery('.checkout-dcmaildiv').hide();
				jQuery('.checkout-dcmail').hide();
				jQuery('#moosend_subscribe_checkbox_field').hide();
			}

			jQuery('.woocommerce-account-fields').hide();

			jQuery(".showlogin i").toggleClass("fa-angle-down fa-angle-right");
			jQuery(".woocommerce-form-login").show();

			jQuery('.login-div').click(function(){
				jQuery(".wc-social-login").hide();
				 jQuery(".showlogin i").toggleClass("fa-angle-down fa-angle-right");
				 jQuery(".showlogin").toggleClass("toggleweightmore toggleweightless");

				 jQuery('.woocommerce-billing-fields__field-wrapper').hide();
				 jQuery('.woocommerce-account-fields').hide();
				 jQuery('#moosend_subscribe_checkbox_field').hide();
				 jQuery('.woocommerce-additional-fields').hide();
			});

			jQuery('.js-social-div').click(function(){
				jQuery(".wc-social-login").slideToggle();
				jQuery(".js-show-social-login i").toggleClass("fa-angle-down fa-angle-right");
				jQuery(".js-show-social-login").toggleClass("toggleweightmore toggleweightless");

				jQuery(".woocommerce-form-login").hide();
				jQuery('.woocommerce-billing-fields__field-wrapper').hide();
				jQuery('.woocommerce-account-fields').hide();
				jQuery('#moosend_subscribe_checkbox_field').hide();
				jQuery('.woocommerce-additional-fields').hide();
			});

			jQuery('.register-div').click(function(){
				jQuery(".woocommerce-form-login").hide();
				jQuery(".wc-social-login").hide();
				jQuery('.woocommerce-billing-fields__field-wrapper').slideToggle();
				jQuery(".register-div").toggleClass("toggleweightmore toggleweightless");

				jQuery('.woocommerce-account-fields').slideToggle();
				jQuery('#moosend_subscribe_checkbox_field').slideToggle();
				jQuery('.woocommerce-additional-fields').slideToggle();
				jQuery(".register-div i").toggleClass("fa-angle-down fa-angle-right");
			});

			//Fetch the DC membership plan value
			jQuery('#monthly_dc').click(function(){
				jQuery('.add_dc_btn').attr('href',siteurl+'/checkout/?add-to-cart=174721&utm_source=checkout-page');
			});
			jQuery('#annually_dc').click(function(){

				jQuery('.add_dc_btn').attr('href',siteurl+'/checkout/?add-to-cart=174739&utm_source=checkout-page');
			});


	  });

	 </script>
	 <?php
}
}

add_filter( 'woocommerce_cart_item_name', 'ts_product_image_on_checkout', 10, 3 );

function ts_product_image_on_checkout( $name, $cart_item, $cart_item_key ) {

    /* Return if not checkout page */
    if ( ! is_checkout() ) {
        return $name;
    }

    /* Get product object */
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    /* Get product thumbnail */
    $thumbnail = $_product->get_image();

    /* Add wrapper to image and add some css */
    $image = '<div class="ts-product-image" style="float:left;width: 100px; height: 74px; display: inline-block; padding-right: 15px; vertical-align: middle;">'
                . $thumbnail .
            '</div>';

    /* Prepend image to name and return it */
    return $image . '<span class="prodname">'.$name.'</span>';
}

add_filter( 'woocommerce_available_payment_gateways', 'df_woocommerce_available_payment_gateways' );
function df_woocommerce_available_payment_gateways( $available_gateways ) {
    if (! is_checkout() ) return $available_gateways;  // stop doing anything if we're not on checkout page.
    if (array_key_exists('paypal',$available_gateways)) {
        // Gateway ID for Paypal is 'paypal'.
         $available_gateways['paypal']->order_button_text = __( 'Proceed To PayPal', 'woocommerce' );
    }
		if (array_key_exists('stripe',$available_gateways)) {
        // Gateway ID for Paypal is 'paypal'.
         $available_gateways['stripe']->order_button_text = __( 'Proceed To Payment', 'woocommerce' );
    }
    return $available_gateways;
}
/* Added by Prasada - Checkout page new UI changes 2022 - end */

/**
* Change the breadcrumb separator -Ajinkya
*/
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_delimiter' );
function wcc_change_breadcrumb_delimiter( $defaults ) {
// Change the breadcrumb delimeter from '/' to '>'
$defaults['delimiter'] = ' > ';
return $defaults;
}

function coupon_check_via_ajax(){
	 $code = strtolower(trim($_POST['code']));
	 $coupon = new WC_Coupon($code);
	 $coupon_post = get_post($coupon->id);
	 if(!empty($coupon_post) && $coupon_post != null){
	  $message = 'Coupon not valid';
	  $status = 0;
	  if($coupon_post->post_status == 'publish'){
	    $message = 'Coupon validated'; $status = 1;
	  }
	 } else {
	 $status = 0;
	 $message = 'Coupon not found!';
	 }
	 print json_encode( [ 'status' => $status, 'message' => $message, 'poststatus' => $coupon_post->post_status, 'coupon_post' => $coupon_post ] );
	 exit();
 }
 add_action( 'wp_ajax_check_coupon_via_ajax', 'coupon_check_via_ajax' );
  add_action( 'wp_ajax_nopriv_check_coupon_via_ajax', 'coupon_check_via_ajax' );


//Code to add Confirm Password field to Checkout
// Add a second password field to the checkout page in WC 3.x.
add_filter( 'woocommerce_checkout_fields', 'wc_add_confirm_password_checkout', 10, 1 );
function wc_add_confirm_password_checkout( $checkout_fields ) {
    if ( get_option( 'woocommerce_registration_generate_password' ) == 'no' ) {
        $checkout_fields['account']['account_password2'] = array(
                'type'              => 'password',
                'label'             => __( 'Confirm password', 'woocommerce' ),
                'required'          => true,
                'placeholder'       => _x( 'Confirm Password', 'placeholder', 'woocommerce' )
        );
    }

    return $checkout_fields;
}

// Check the password and confirm password fields match before allow checkout to proceed.
add_action( 'woocommerce_after_checkout_validation', 'wc_check_confirm_password_matches_checkout', 10, 2 );
function wc_check_confirm_password_matches_checkout( $posted ) {
    $checkout = WC()->checkout;
    if ( ! is_user_logged_in() && ( $checkout->must_create_account || ! empty( $posted['createaccount'] ) ) ) {
        if ( strcmp( $posted['account_password'], $posted['account_password2'] ) !== 0 ) {
            wc_add_notice( __( 'Passwords do not match.', 'woocommerce' ), 'error' );
        }
    }
}


add_action('wp_head','homepage_add_product_slider_js');
function homepage_add_product_slider_js(){
	?>
	<script>
	jQuery(document).ready(function(){
    jQuery('.home .products').slick({ //add CSS class of target
        dots: false,
        autoplay: false,
        speed: 900,
        autoplaySpeed: 2000,
        centerMode: false,
        centerPadding: '40px',
        slidesToShow: 4,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 3
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 1
            }
          }
        ]
      });
        jQuery('.dealclub-slider .products').slick({ //add CSS class of target
        dots: false,
        autoplay: false,
        speed: 900,
        autoplaySpeed: 2000,
        centerMode: false,
        centerPadding: '40px',
        slidesToShow: 4,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 3
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 1
            }
          }
        ]
      });
	  jQuery('#test_masterclass .products').slick({ //add CSS class of target
        dots: false,
        autoplay: false,
        speed: 900,
        autoplaySpeed: 2000,
        centerMode: false,
        centerPadding: '40px',
        slidesToShow: 1,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 3
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 1
            }
          }
        ]
      });
	  jQuery('#dealclub_carousal .products').slick({ //add CSS class of target
        dots: false,
        autoplay: false,
        speed: 900,
        autoplaySpeed: 2000,
        centerMode: false,
        centerPadding: '40px',
        slidesToShow: 1,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 3
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 1
            }
          }
        ]
      });
    });
</script>
<?php
}
/** Change the membership url from My Account menu  -Ajinkya start */

// add custom endpoint for My Account menu
add_filter ( 'woocommerce_account_menu_items', 'wptips_customize_account_menu_items' );
function wptips_customize_account_menu_items( $menu_items ){
     // Add new Custom URL in My Account Menu
    $new_menu_item = array('Membership'=>'Membership');  // Define a new array with cutom URL slug and menu label text
    $new_menu_item_position=2; // Define Position at which the New URL has to be inserted

    array_splice( $menu_items, ($new_menu_item_position-1), 0, $new_menu_item );
    return $menu_items;
}
// point the endpoint to a custom URL
add_filter( 'woocommerce_get_endpoint_url', 'wptips_custom_woo_endpoint', 10, 2 );
function wptips_custom_woo_endpoint( $url, $endpoint ){
     if( $endpoint == 'members-area' ) {
        $url = get_site_url() .'/account/members-area/'; // Your custom URL to add to the My Account menu
    }
    return $url;
}
/** Change the membership url from My Account menu -Ajinkya end */
/** Search Functionality in downloads -Ajinkya start */
// add the ajax fetch js
add_action( 'woocommerce_account_downloads_endpoint', 'ajax_fetch' );
function ajax_fetch() {
?>
<script type="text/javascript">
function fetch(){

    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        data: { action: 'data_fetch', keyword: jQuery('#keyword').val() },
        success: function(data) {
            jQuery('#datafetch').html( data );
            jQuery('.woocommerce-downloads .search-bar .download-search-clear').removeClass('disabled');
        }
    });
    jQuery( function($){
        var a = '#datafetch';

        // Starting spinners with a delay of 2 seconds
        setTimeout(function() {
            // Starting spinners
            $(a).block({
                message: null,
                overlayCSS: {
                    background: "#fff",
                    opacity: .6
                }
            });

            console.log('start');

            // Stop spinners after 3 seconds
            setTimeout(function() {
                // Stop spinners
                $(a).unblock();

                console.log('stop');
            }, 2000);
        }, 1000);
    });
}
function fetch2(){

    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        data: { action: 'data_fetch', keyword: '' },
        success: function(data) {
            jQuery('#datafetch').html( data );
            jQuery('.woocommerce-downloads .search-bar .download-search-clear').addClass('disabled');
            jQuery('#keyword').val('');
        }
    });
    jQuery( function($){
        var a = '#datafetch';

        // Starting spinners with a delay of 2 seconds
        setTimeout(function() {
            // Starting spinners
            $(a).block({
                message: null,
                overlayCSS: {
                    background: "#fff",
                    opacity: .6
                }
            });

            console.log('start');

            // Stop spinners after 3 seconds
            setTimeout(function() {
                // Stop spinners
                $(a).unblock();

                console.log('stop');
            }, 5000);
        }, 2000);
    });
}
</script>

<?php
}
// the ajax function
add_action('wp_ajax_data_fetch' , 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch','data_fetch');
function data_fetch(){


    $downloads = WC()->customer->get_downloadable_products();
    if($_POST['keyword'] == ""){
        ?>
        <script>
            jQuery('section.woocommerce-order-downloads').show();
        </script>
        <?php
    }
    if( count($downloads) > 0 && $_POST['keyword'] !== ""  ) :
        $flag = 0;
        ?>
        <script>
            jQuery('section.woocommerce-order-downloads').hide();
        </script>
        <table class="woocommerce-table woocommerce-table--search-downloads shop_table shop_table_responsive order_details">
		<thead>
			<tr>
				<th>Product</th>
				<th>License Key</th>
                <th>Download Links</th>
			</tr>
		</thead>
        <?php
            for( $i=0; $i< count($downloads); $i++ ){


                $pattern = "/".esc_attr( $_POST['keyword'] )."/i";
                $tname = $downloads[$i]['product_name'];
                if(preg_match($pattern, $tname)){
                    $flag+=1;
                    ?>
                    <tr>
                    <td><?php echo $downloads[$i]['product_name'];?></td>
                    <td>
                    <?php $license_key_arr = array();
                        $license_key_arr = get_downlaods_license_keys_df( $downloads[$i]['product_id'], $downloads[$i]['order_id'] );
                        if ( ! empty( $license_key_arr ) ) {
                                        echo '<div class="df-license-keys"><br />License Key:  ';
                                foreach ( $license_key_arr as $license_key ) {
                                        echo '<br/>' . $license_key['license_key'];
                                }
                                        echo "</div>";
                        }
                    ?>

                        </td>
                    <td><a href="<?php echo $downloads[$i]['download_url'] ?>"><?php echo $downloads[$i]['download_name'];?></a></td>
                    </tr>

                    <?php
                }
            }
            if($flag == 0){

                 ?>
                 <h3 style="font-weight:600;">0 Result Found</h3>
        <script>
            jQuery('table.woocommerce-table--search-downloads').hide();
        </script>
        <?php
            }
            ?>
            </table>
            <?php
        wp_reset_postdata();
        endif;
        die();

}
/** Search Functionality in downloads -Ajinkya end */

/** Redirect user to home page after logout -Ajinkya start */

add_action('wp_logout','go_home');
function go_home(){
  wp_redirect( home_url() );
  exit();
}
/** Redirect user to home page after logout -Ajinkya end */

/** Remove Title and breadcrumb from login page -Ajinkya start */

add_action( 'wp_footer', 'remove_account_title' );
function remove_account_title() {
    ?>
    <script>

           jQuery(document).ready(function() {

            if(jQuery("form.woocommerce-form-login").html()) {
               jQuery('.elementor-widget-woocommerce-breadcrumb').hide();
               jQuery('.elementor-widget-page-title').hide();
            }
        });

    </script>
    <?php
}


/** Remove Title and breadcrumb from login page -Ajinkya end */


/** Add materclass videos for customers - Vaibhav Start */

add_action('wp_footer','myaccount_dashboard_new_ui',10,1);
function myaccount_dashboard_new_ui($order_id){
    global $wpdb, $wp;

	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	$current_slug = add_query_arg( array(), $wp->request );


	if(strpos($current_slug,"members-area") !== false){
		if( !is_active_dealclub_member()){
			?>
			<script>
				jQuery(document).ready(function(){
					siteUrl = "<?php echo get_site_url(); ?>";
					jQuery(".elementor-heading-title").hide();
					jQuery('.woocommerce-account .woocommerce-account-my-memberships').html('<p>You are not a DealClub Member.<br> <a target="_blank" href="'+siteUrl+'/dealclub#pricing_plans_section">Subscribe to DealClub</a> & Get FREE Access to all Masterclasses</p>');

				});
			</script>
			<?php
		}
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery(".elementor-heading-title").hide();
			jQuery("<h2 class='ordhis_heading'>Plus Membership</h2>").insertBefore(".woocommerce");
		});
	</script>
	<?php
	// Show masterclass products dynamically to plus and non-plus members by tejas -- start
	$get_masterclass_product_ids_args = array(
		'post_type' => 'product',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => 'exclusive-masterclasses',
			),
		),
		'fields'         => 'ids',
		'posts_per_page' => -1,
	);

	$masterclass_product_ids = get_posts( $get_masterclass_product_ids_args );

	if(is_active_dealclub_member()){
		$masterclass_links = array();
		foreach ( $masterclass_product_ids as $product_id ) {
			$masterclass_links[] = get_post_meta( $product_id, 'df_vimeo', true );
		}

		$masterclass_product_titles = array();
		$masterclass_product_urls = array();
		foreach( $masterclass_links as $masterclass_link ) {
			$single_links = explode( ';', $masterclass_link );
			foreach( $single_links as $link ) {
				$title_and_url = explode( '-', $link );
				$masterclass_product_titles[] = $title_and_url[0];
    			$masterclass_product_urls[] = $title_and_url[1];
			}
		}

	   ?>
        	<script defer>
        		jQuery(document).ready(function(){
					var masterclass_product_titles = <?php echo json_encode( $masterclass_product_titles ); ?>;
					var masterclass_product_urls = <?php echo json_encode( $masterclass_product_urls ); ?>;
					var masterclass_html = '';
					jQuery("<div class='plus-membership-only'><p class='plus-membership-only_heading'>Exclusive Videos</p><div class='plus-membership-only_images'></div></div>").insertAfter(".woocommerce-account-my-memberships");
					for( let links = 0; links < masterclass_product_titles.length; links++ ) {
						if( masterclass_product_titles[links] === '' || masterclass_product_titles[links] === null ) continue;
						masterclass_html += "<div class='plus-membership-only-image'><p>" + masterclass_product_titles[links] + "</p><iframe src='" + masterclass_product_urls[links] + "' frameborder='0' allow='autoplay; fullscreen; picture-in-picture' allowfullscreen title='" + masterclass_product_titles[links] + "'></iframe></div>";
					}
					jQuery('.plus-membership-only_images').html(masterclass_html);
        		});
        	</script>
	<?php
	} else {
		$args = array(
			'customer_id' => get_current_user_id(),
			'return' => 'ids',
			'status' => 'wc-completed',
			'limit' => -1,
		);
		$all_orders = wc_get_orders( $args );

		$completed_order_product_ids = array();

		foreach ( $all_orders as $order_id ) {
			$order = wc_get_order( $order_id );
			$items = $order->get_items();
			foreach ( $items as $item ) {
				$product_id = $item->get_product_id();
				$completed_order_product_ids[] = $product_id;
			}
		}

		$completed_masterclass_product_ids = array_intersect( $completed_order_product_ids, $masterclass_product_ids );
		$masterclass_product_titles = array();
		$masterclass_product_urls = array();
		foreach( $completed_masterclass_product_ids as $product_id ) {
			$masterclass_vimeo_links = get_post_meta( $product_id, 'df_vimeo', true );
			$single_links = explode( ';', $masterclass_vimeo_links );
			foreach( $single_links as $link ) {
				$title_and_url = explode( '-', $link );
				$masterclass_product_titles[] = $title_and_url[0];
    			$masterclass_product_urls[] = $title_and_url[1];
			}
		}
	?>
	<script>
		jQuery(document).ready(function(){
			var masterclass_product_titles = <?php echo json_encode( $masterclass_product_titles ); ?>;
			var masterclass_product_urls = <?php echo json_encode( $masterclass_product_urls ); ?>;
			var masterclass_html = '';
			if( masterclass_product_titles.length > 0 ) {
				jQuery("<div class='plus-membership-only'><p class='plus-membership-only_heading'>Exclusive Videos</p><div class='plus-membership-only_images'></div></div>").insertAfter(".woocommerce-account-my-memberships");
				for( let links = 0; links < masterclass_product_titles.length; links++ ) {
					if( masterclass_product_titles[links] === '' || masterclass_product_titles[links] === null ) continue;
					masterclass_html += "<div class='plus-membership-only-image'><p>" + masterclass_product_titles[links] + "</p><iframe src='" + masterclass_product_urls[links] + "' frameborder='0' allow='autoplay; fullscreen; picture-in-picture' allowfullscreen title='" + masterclass_product_titles[links] + "'></iframe></div>";
				}
				jQuery('.plus-membership-only_images').html(masterclass_html);
			}
		});
	</script>
	<?php
	// Show masterclass products dynamically to plus and non-plus members by tejas -- end
	}
}
}

/** Add materclass videos for customers - Vaibhav End */

/*Code added by Vaibhav for removing coupon code if only dealclub is present in cart- start */
add_action('woocommerce_before_calculate_totals','remove_credit_points_mem');

function remove_credit_points_mem(){
	$applied_coupon = WC()->cart->get_applied_coupons();

	$pattern = "/wc_points_redemption/i";
	$cart_items = WC()->cart->get_cart();
	// $product_count = count($cart_items);
	// echo $product_count;
	if(count($cart_items) === 1){
		foreach ($cart_items as $cart_item){

			if($cart_item['product_id'] == 174721 || $cart_item['product_id'] == 174739){
				if(preg_match($pattern, $applied_coupon[0])){
					WC()->cart->remove_coupon($applied_coupon[0]);

				}
			}
		}
	}


}

/*Code added by Vaibhav for removing coupon code if only dealclub is present in cart- End*/
/* Added for hiding credit points section for Freebie or DealClub Membership or DC exclusive free products -Akshay-start */

add_action('wp_footer','hide_credits_section_if_dc_in_cart');
function hide_credits_section_if_dc_in_cart(){

if(is_checkout()){

	/* changes made by -Akshay for hiding credit points section for Freebie + DealClub Membership -start */

	$total_cart_price = WC()->cart->total;

		 $is_paid_product=false;
		if( $total_cart_price > 0 ){

		   foreach ( WC()->cart->get_cart() as $cart_item ) {
			   $product = $cart_item['data'];
			   $product_price = $cart_item['data']->get_price();
			   if( $product_price > 0 && $cart_item['product_id'] != 174721 && $cart_item['product_id'] != 174739 ){
				   $is_paid_product= true;
			   }
		   }
	   }
	   if ( !$is_paid_product ) {
				?>
				<style>
					.woocommerce-checkout .wc_points_redeem_earn_points,
					.woocommerce-checkout #order_review .wc_points_redeem_earn_points,
					.woocommerce-checkout .redeem_points_div_head{
						display:none !important;
					}
					.woocommerce-checkout #custom_customer_details{
						margin-top: 4em;
					}
				</style>

				<?php
			}
			if(count( WC()->cart->get_applied_coupons() ) > 0){
				?>
				<style>
					.woocommerce-checkout .wc_points_redeem_earn_points,
					.woocommerce-checkout #order_review .wc_points_redeem_earn_points,
					.woocommerce-checkout .redeem_points_div_head{
						display:none !important;
					}
					.woocommerce-checkout #custom_customer_details{
						margin-top: 4em;
					}
				</style>

				<?php
			}

			?>
				<script>
				jQuery( "body" ).on ( "click", ".woocommerce-remove-coupon", function() {
						jQuery(".woocommerce-checkout .redeem_points_div_head").show();
				});
				jQuery(document).ready(function(){

					jQuery('.wc_points_rewards_apply_discount').click(function(){

						jQuery(".woocommerce-checkout .redeem_points_div_head").hide();
					});

				});
				</script>
			<?php

	}
	}
	/* Added for hiding credit points section for Freebie or DealClub Membership or DC exclusive free products -Akshay-end */

	/**
	 * Add fucntionality to select deafult varition product which has highest gross revenue or 2nd option if zero by default
	*/

	add_action("wp_head","select_default_variation_product");

	function select_default_variation_product() {
		if (is_product()) {
			global $wpdb, $product;
			$var_p_ids = $product->get_children();
			$gross_rev = [];

			foreach ($var_p_ids as $var_id) {
				$gross_revenue = (float) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT SUM(product_gross_revenue)
						FROM {$wpdb->prefix}wc_order_product_lookup
						WHERE variation_id = %d",
						$var_id
					)
				);

				$gross_rev[$var_id] = $gross_revenue;
			}

			if (!empty($gross_rev)) {
				$max_of_var = max($gross_rev);
				$key = array_search($max_of_var, $gross_rev);
				$size = sizeof(wc_get_product($key)->attributes);
				$keys = array_keys(wc_get_product($key)->attributes);
				$select = wc_get_product($key)->attributes[$keys[0]];

				if ($max_of_var != 0) {
					?>
					<script>
						jQuery(document).ready(function($) {
							$(".single-product .variations .value select option[value='<?php echo $select; ?>']").prop("selected", true);
						});
					</script>
					<?php
				} else {
					?>
					<script>
						jQuery(document).ready(function($) {
							$(".single-product .variations .value select option:nth-child(2)").prop("selected", true);
						});
					</script>
					<?php
				}
			}
		}
	}


	/**
	 * Hide the credit point section from woocommerce checkout when coupon is applied.
	 */

	add_action('woocommerce_review_order_before_order_total','remove_the_credit_divs');

	function remove_the_credit_divs(){

		$applied_coupons = WC()->cart->get_applied_coupons();
		if(!empty($applied_coupons)){
			?>
				<script>
					jQuery(document).ready(function(){
						jQuery(".woocommerce-checkout #order_review div.wc_points_redeem_earn_points").hide();
						jQuery(".woocommerce-checkout .redeem_points_div_head").hide();
					});
				</script>
			<?php
		}
	}



	/**
	 * Shortcode to show carousal based on formula for bestselling deals.
	 */

	 function df_best_seller_deals(){

		$transient = get_transient( 'bestsell_product_id_string' );

		if ( ! empty( $transient ) ) {

			$product_ids_string = $transient;
		} else {


		$args = array(
			'status' => 'publish', // Only fetch published products
			'limit' => -1, // Retrieve all products
			'stock_status' => 'instock', // Only fetch products that are in stock
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat', // Specify the taxonomy (category)
					'field' => 'slug', // Use 'slug' or 'term_id' depending on the identifier you want to exclude categories by
					'terms' => array('freebies','exclusive-masterclasses','dealclub-special'), // Replace with the slugs or term IDs of the categories you want to exclude
					'operator' => 'NOT IN' // Exclude the specified categories
				)
			)
		);

		$products = wc_get_products($args);
		$product_ids = wp_list_pluck($products, 'id');

		$gross_rev_gen = array();
		foreach ($product_ids as $id){

			global $wpdb;

			$start_date = strtotime('-3 months'); // Calculate the start date (3 months ago) in Unix timestamp
			$end_date = current_time('timestamp'); // Get the current date in Unix timestamp

			$query = $wpdb->prepare("
				SELECT SUM(meta_value) AS quantity
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS items ON itemmeta.order_item_id = items.order_item_id
				INNER JOIN {$wpdb->prefix}posts AS posts ON items.order_id = posts.ID
				WHERE itemmeta.meta_key = '_qty'
				AND items.order_item_type = 'line_item'
				AND itemmeta.order_item_id IN (
					SELECT order_item_id
					FROM {$wpdb->prefix}woocommerce_order_itemmeta
					WHERE meta_key = '_product_id'
					AND meta_value = %d
				)
				AND posts.post_type = 'shop_order'
				AND posts.post_status = 'wc-completed'
				AND posts.post_date >= %s
				AND posts.post_date <= %s
			", $id, date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date));

			$product_sold_count = $wpdb->get_var($query);


			$query2 = $wpdb->prepare("
			SELECT SUM(meta_value) AS revenue
			FROM {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS items ON itemmeta.order_item_id = items.order_item_id
			INNER JOIN {$wpdb->prefix}posts AS posts ON items.order_id = posts.ID
			WHERE itemmeta.meta_key = '_line_total'
			AND items.order_item_type = 'line_item'
			AND itemmeta.order_item_id IN (
				SELECT order_item_id
				FROM {$wpdb->prefix}woocommerce_order_itemmeta
				WHERE meta_key = '_product_id'
				AND meta_value = %d
			)
			AND posts.post_type = 'shop_order'
			AND posts.post_status IN ('wc-completed', 'wc-processing')
			AND posts.post_date >= %s
			AND posts.post_date <= %s
			", $id, date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date));

			$gross_revenue = $wpdb->get_var($query2);

			$formulated_value = ($gross_revenue/90) * ($product_sold_count / 90);
			$gross_rev_gen[$id] = number_format($formulated_value,2);


		}
		$sorted_array = arsort($gross_rev_gen);


		$final_array = array_slice($gross_rev_gen,0,7,true);

		$product_ids_array = array_keys( $final_array );
		$product_ids_string = implode( ',', $product_ids_array );
		set_transient( 'bestsell_product_id_string', $product_ids_string, 604800 );
	}
		 return do_shortcode( "[products ids='$product_ids_string' orderby='post__in' columns='3' ]" );

	}

	add_shortcode( 'df_best_seller_deals', 'df_best_seller_deals' );

/**
 * Get users's membership id
*/

function get_user_membership_id( $user_id ) {
    if ( function_exists( 'wc_memberships_get_user_active_memberships' ) ) {
        $memberships = wc_memberships_get_user_active_memberships( $user_id );
        if ( ! empty( $memberships ) ) {
            // Assuming the user has only one active membership.
            $membership = reset( $memberships );
            return $membership->get_plan_id();
        }
    }
    return false; // Membership ID not found or error occurred.
}


/**
 * Membership upgrade from monthly to annual
 */

add_action( 'woocommerce_thankyou', 'df_membership_upgrade',10,1 );

function df_membership_upgrade($order_id) {

	    // Retrieve the order object
		$order = wc_get_order($order_id);

		// Retrieve the order items
		$items = $order->get_items();

		// Loop through the order items
		foreach ($items as $item) {
			$product_id = $item->get_product_id();

			if ( $product_id == 174739  ) {//check if annual is purchased
				$product_id == 174739;
				break;
			}

		}

		$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();


		if ( $product_id == 174739 &&  $is_annual_or_monthly == 174761 ) { //only upgrade when user was a monthly and had annual product in the order history

				//before cancelling the monthly subscription reward the credit points to the user

				update_points_after_membership_upgrade();

				// after updating the points proceed with cancelltion
				//cancel user's subscription
				$no_of_loops = 0;
				$user_id = get_current_user_id();

				// Get all customer subscriptions
				$args = array(
					'subscription_status'       => 'active',
					'subscriptions_per_page'    => -1,
					'customer_id'               => $user_id,
					'orderby'                   => 'ID',
					'order'                     => 'DESC'
				);
				$subscriptions = wcs_get_subscriptions($args);

				// Going through each current customer subscriptions
				foreach ( $subscriptions as $subscription ) {
					$no_of_loops = $no_of_loops + 1;

					if ($no_of_loops > 1){
						$subscription->update_status( 'cancelled' );
					}
				}

				//cancel user's active monthly membership

				$membership_id = get_user_membership_id($user_id);

				$membership = wc_memberships_get_user_membership($user_id, $membership_id);

					if ( $membership ) {

						$membership->cancel_membership();

					}

		}

}

/**
 * Update credit points of the user after membership upgrade
 */

function update_points_after_membership_upgrade ( ) {

	//First find out the credit points to rewarded

	// Get the user ID
	$user_id = get_current_user_id();

	// Get the membership object for the user

	$membership_id = get_user_membership_id($user_id);

	$membership = wc_memberships_get_user_membership($user_id,$membership_id );

	if ( $membership ) {
		// Get the membership activation date
		$activation_date = $membership->get_start_date();

		// Create DateTime objects for the activation date and current date
		$activation_date = new DateTime($activation_date);
		$current_date = new DateTime();

		// Calculate the number of days since activation
		$days_passed = $activation_date->diff($current_date)->days;

		// Extract the month of activation
		$activation_month = $activation_date->format('F'); // 'F' returns the full month name

		$no_of_day_in_the_month_of_activation = getDaysInMonth($activation_month);


		//formula for credit points to be rewared

		$monthly_member_amount = 9;

		$credit_points_to_be_rewared = $monthly_member_amount * ( $no_of_day_in_the_month_of_activation - $days_passed )/$no_of_day_in_the_month_of_activation;

		//final points to be rewarded
		$credit_points_to_be_rewared = round($credit_points_to_be_rewared)*100;


	}

	//values will be insertd in the db

	global $wpdb;

	$current_datetime = current_time('mysql'); // Get the current date and time in MySQL format

	//get the value of credit points from db
	$users_credit_points =	$wpdb->get_row( "SELECT points FROM `wp_wc_points_rewards_user_points` WHERE user_id=" . $user_id . " " );

	if ( $users_credit_points ) {

		$points = $users_credit_points->points;

		// credit points formula goes here...............

		 $new_points = $points + $credit_points_to_be_rewared;

		 // Prepare the data to update the points in the database
		 $data = array(
			 'points' => $new_points,
			 'points_balance' => $new_points,
			 'date' => $current_datetime
		 );

		 // Specify the condition for the update query
		 $where = array(
			 'user_id' => $user_id
		 );

		 // Update the points in the database

		 $wpdb->update('wp_wc_points_rewards_user_points', $data, $where);

	}else {

		    // User does not have an entry, insert new values

			$new_points = $credit_points_to_be_rewared; //give credit to the user

			$data = array(
				'user_id' => $user_id,
				'points' => $new_points ,
				'points_balance' => $new_points ,
				'date' => $current_datetime
			);

			// Insert the new entry into the database

			$wpdb->insert('wp_wc_points_rewards_user_points', $data);
		}

}

/**
 * Function to get no of days in the given month
 */

function getDaysInMonth($month) {
    // Convert the month argument to lowercase
    $month = strtolower($month);

    // Create a DateTime object for the given month
    $date = DateTime::createFromFormat('F', $month);

    // Get the number of days in the month
    $days_in_month = $date->format('t');

    return $days_in_month;
}







?>

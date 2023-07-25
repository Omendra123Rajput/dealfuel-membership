<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}
$product = wc_get_product( 174739 );
$product_monthly = wc_get_product( 174721 );

global $woocommerce;

$items                  = $woocommerce->cart->get_cart();
$cart_total_price_final = $woocommerce->cart->total;
$dealclub_savings       = 0;
$cw_discount = 0;
$cw_monthly_discount = 0;

foreach ( $items as $item => $values ) {

	$pro_id = $values['product_id'];
	$_product = wc_get_product( $pro_id );
	if($_product->is_type( 'simple' )){

		$regular_price = $_product->get_sale_price();

		//if monthly is added in cart then sale is according to monthly else annually.

		if ( check_if_monthly_is_in_cart() ) {

			$sale_price = get_dynamic_price( $_product->get_id() )[1];

		} else {
			$sale_price = get_dynamic_price( $_product->get_id() )[0];

		}

		// $sale_price = get_dynamic_price( $_product->get_id() );
		$sale_price_for_monthly = get_dynamic_price( $_product->get_id() )[1];
		$discount = ($regular_price - $sale_price) * $values['quantity'];
		$discount_for_monthly = ($regular_price - $sale_price_for_monthly) * $values['quantity'];

	}

	if($_product->is_type( 'variable' )){
		$var_id = $values['variation_id'];

		$dynamic_pricearr = get_all_dynamic_prices_with_id_as_key($pro_id);
		$discount = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dc_price']  )) ) * $values['quantity'];

	}

	$cw_discount += $discount;
	$cw_monthly_discount += $discount_for_monthly;
}
?>
<?php if(!is_dealclubmembership_in_cart() && !is_user_an_active_member_wcm()){ ?>
<div class="dcplan-section monthly-checkout-membership">
	<!-- Monthly -->
	<h4 class="heading-checkout-table">Dealclub Membership</h4>
	<div class="inner-section-divs">
		<div class = "deal_club_img"><?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product_monthly->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product_monthly->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?></div>
		<div class = "deal_club_price"><h4>$<?php echo $product_monthly->get_price() ?>.00/Month</h4><p>Extra 5-10% OFF on every purchase</p></div>
		<a class="add_dc_btn single_add_to_cart_button button alt wp-element-button checkout_add_item_monthly" href="<?php echo get_site_url(); ?>/checkout/?add-to-cart=174721&utm_source=checkout-page">Add Item</a>
</div>

</div>
<!-- Annual -->
<div class="dcplan-section">

<div class="inner-section-divs annual-checkout-membership">
		<div class = "deal_club_img"><?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?></div>
		<div class = "deal_club_price"><h4>$<?php echo $product->get_price() ?>.00/Year</h4><p>Extra 15-25% OFF on every purchase</p></div>
		<a class="add_dc_btn single_add_to_cart_button button alt wp-element-button checkout_add_item_annual" href="<?php echo get_site_url(); ?>/checkout/?add-to-cart=174739&utm_source=checkout-page">Add Item</a>
</div>



</div>
<?php } ?>
<div class="woocommerce-form-login-toggle">
	<h2 class="heading-checkout-table">Account</h2>
	<div class="login-div log-inner-section-divs">
		<a href="#" class="showlogin toggleweightless" id="logindivid">Login <i id="arrowown" class="fas fa-angle-right"></i></a>
		<?php
		woocommerce_login_form(
			array(
				'message'  => esc_html__( 'Returning Customer?'),
				'redirect' => wc_get_checkout_url(),
				'hidden'   => true,
			)
		);
		?>

	</div>
	<hr class="hrdiv" style="width: 82%;margin-bottom:0 !important;" />
	<div class="js-social-div log-inner-section-divs">
		<a href="#" class="js-show-social-login toggleweightless">Social Login <i id="arrowown" class="fas fa-angle-right"></i></a>
	</div>
	<?php echo do_shortcode('[woocommerce_social_login_buttons return_url="/checkout"]'); ?>
</div>

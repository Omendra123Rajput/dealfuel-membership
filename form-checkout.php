<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<?php
		// Show only if the user is not active member.
		if ( ! is_user_an_active_member_wcm() ) {
			?>
		<div class="" id="custom_customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>


	<?php } ?>
		<div class="col2-set" id="customer_details" >
			<div class="col-1">
				<?php
					// Show only if the user is not active member.
				if ( is_user_an_active_member_wcm() ) {
					?>
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				<?php } ?>
		<!--	</div> -->

		<!--	<div class="col-2"> -->

		<?php
		// Show only if the user is not active member.

		$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();

		if ( ! is_user_an_active_member_wcm() ) {


			global $woocommerce, $product;
					$items = $woocommerce->cart->get_cart();
					$total_dyanamic_price = 0;
					$total_sale_price = 0;
							foreach($items as $item => $values) {

									$_product =  wc_get_product( $values['data']->get_id());
									$product_sale_price = $_product->get_price();

										//Non DC member

										//first check if monthly is added in cart, if yes, then price is acc to monthly
										//else acc to annually

										if ( check_if_monthly_is_in_cart() ) {

											$updated_dynamic_price = get_dynamic_price( $_product->get_id() )[1];

										}else {
											$updated_dynamic_price = get_dynamic_price( $_product->get_id() )[0];
										}


										$post_type = $values['data']->post_type;
										if ( $post_type == "product_variation") {

													$variation_arr = get_post_meta( $values['data']->get_parent_id(), '_pricing_rules', 'true' );
												foreach ( $variation_arr as $var_obj ) {
													if($values['data']->get_id() == $var_obj['variation_rules']['args']['variations'][0]){
														$updated_dynamic_price = $var_obj['rules'][1]['amount'];

													}


											}
										}

									if( '' != $updated_dynamic_price){
										if($values['quantity'] > 1){
												$updated_dynamic_price = $updated_dynamic_price * $values['quantity'];
												$product_sale_price = $product_sale_price * $values['quantity'];
											}
										$total_dyanamic_price = $total_dyanamic_price + $updated_dynamic_price;
										$total_sale_price = $total_sale_price + $product_sale_price;
									}
							}

							if(is_dealclubmembership_in_cart()){
									$total_discount_plus = $total_sale_price - $total_dyanamic_price;
							}
						else{
									$cart_contents_total = WC()->cart->get_cart_contents_total();
									$total_discount_plus = $cart_contents_total - $total_dyanamic_price;
						}
			?>

	<?php }
    else{

        			global $woocommerce, $product;
					$items = $woocommerce->cart->get_cart();
					$total_dyanamic_price = 0;
					$total_sale_price = 0;
							foreach($items as $item => $values) {

									$_product =  wc_get_product( $values['data']->get_id());
									$product_sale_price = $_product->get_price();

										if ( $is_annual_or_monthly == 174761 ) { //if monthly then monthly price

											$updated_dynamic_price = get_dynamic_price( $_product->get_id() )[1];

										}else {
											$updated_dynamic_price = get_dynamic_price( $_product->get_id() )[0];
										}

										$post_type = $values['data']->post_type;
										if ( $post_type == "product_variation") {

													$variation_arr = get_post_meta( $values['data']->get_parent_id(), '_pricing_rules', 'true' );
												foreach ( $variation_arr as $var_obj ) {
													if($values['data']->get_id() == $var_obj['variation_rules']['args']['variations'][0]){
														$updated_dynamic_price = $var_obj['rules'][1]['amount'];

													}


											}
										}

									if( '' != $updated_dynamic_price){
										if($values['quantity'] > 1){
												$updated_dynamic_price = $updated_dynamic_price * $values['quantity'];
												$product_sale_price = $product_sale_price * $values['quantity'];
											}
										$total_dyanamic_price = $total_dyanamic_price + $updated_dynamic_price;
										$total_sale_price = $total_sale_price + $product_sale_price;
									}
							}

							if(is_dealclubmembership_in_cart()){
									$total_discount_plus = $total_sale_price - $total_dyanamic_price;
							}
						else{
									$cart_contents_total = WC()->cart->get_cart_contents_total();
									$total_discount_plus = $cart_contents_total - $total_dyanamic_price;
						}

        $saved_amt = $total_sale_price - $cart_contents_total;
      ?>



<div class="join-dc-save">
	<?php echo "You Just Saved $" . $saved_amt." With DealClub Membership"; ?>
</div>



    <?php



    }?>
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

        <?php if ( ! is_user_an_active_member_wcm() ) { ?>
        </div>
        </div>
        <?php } ?>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<h3 id="order_review_heading" class="heading-checkout-table"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
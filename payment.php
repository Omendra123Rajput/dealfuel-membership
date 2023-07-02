<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : ?>
	<div class="payment_methods" style="clear:both;">
	<label>Payment Method : </label>
		<ul class="wc_payment_methods payment_methods methods">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				if ( ! WC()->customer->get_country() ) {
					$no_gateways_message = __( 'Please fill in your details above to see available payment methods.', 'woocommerce' );
				} else {
					echo '<li>' . esc_html( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_country() ) ? __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : __( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>';
				}
			}
			?>
		</ul>
	</div>
	<?php endif; ?>
	<div class="form-row place-order">
		<noscript>
			<?php _e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?>
			<br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>" />
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php
				global $woocommerce;
				$items                  = $woocommerce->cart->get_cart();
				$cart_total_price_final = $woocommerce->cart->total;
				$dealclub_savings       = 0;
				$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();
		foreach ( $items as $item => $values ) {
			$price            = get_post_meta( $values['product_id'], '_price', true );

			if ( $is_annual_or_monthly == 174761 ) { //if monthly then monthly price

				$club_price       = get_dynamic_price( $values['product_id'] )[1];

			}else {
				$club_price       = get_dynamic_price( $values['product_id'] )[0];
			}

			// $club_price       = get_dynamic_price( $values['product_id'] );
			$dealclub_savings = $dealclub_savings + ( $price - $club_price );
		}

		?>

		<?php if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
			<p class="form-row terms">
				<label for="terms" class="checkbox"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?></label>
				<input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
			</p>
		<?php endif; ?>

<?php
				//	echo apply_filters( 'woocommerce_order_button_html', '<input type="submit" class="place-order-btn" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />' );
				echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine

?>
		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

	<br>
	<span id="gift_span"><input type="checkbox" name="giftFlag" id="giftID" onclick="giftChkFunc(this);"> Send This As A Gift</span>

	<div id="giftForm" class="row" style="display:none;margin-top:10px;margin-bottom:15px;" >

		<div class="col-lg-4 col-xs-12">
		<input type="text" class="input-text " name="sname" id="sname" value="" placeholder="Your Name" style="background-color:#ffffff;"/>
		</div>
		<div class="col-lg-4 col-xs-12">
		<input type="text" class="input-text " name="rname" id="rname" value="" placeholder="Recipient's Name" style="background-color:#ffffff;"/>
		</div>
		<div class="col-lg-4 col-xs-12">
			<input type="text" class="input-text " name="email_id" id="email_id" placeholder="Recipient's Email" value="" style="background-color:#ffffff;"/>
		</div>
		<div class="col-lg-12 col-xs-12">
		<textarea name="msg" class="input-text" placeholder="Message" id="msg" style="width:95%;margin-top:15px;margin-bottom:30px;"></textarea>
		</div>
	</div>
	<div class="money-back-guarantee">
		<table class="cart-icons-tbl">
		<tr>
		<td class="icons-td"><img src="<?php echo site_url();?>/wp-content/uploads/2022/02/1-money-back.png" width="20px" height="20px"></td>
		<td><strong> 30 Days Money Back Policy</strong> <span>No questions asked, You say it We do it.</span></td>
		</tr>
		<tr>
		<td class="icons-td"><img src="<?php echo site_url();?>/wp-content/uploads/2022/02/call-support.png" width="20px" height="20px"></i></td>
		<td><strong> Unlimited Customer support</strong> <span>All issues resolved to your satisfaction.</span></td>
		</tr>
		<tr>
		<td class="icons-td"><img src="<?php echo site_url();?>/wp-content/uploads/2022/02/customer-support-2.png" width="20px" height="20px"></td>
		<td><strong> Contact</strong> <span><a href="mailto:support@dealfuel.com">support@dealfuel.com</a> for all your queries, issues, feedback. We reply within 24 hours.</span></td>
		</tr>
		</table>
	</div>
		<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
	</div>
</div>
<script>
function giftChkFunc(obj){
		jQuery('#giftForm').slideToggle();
}
</script>
<?php if ( ! is_ajax() ) : ?>
	<?php do_action( 'woocommerce_review_order_after_payment' ); ?>
<?php endif; ?>

<style type="text/css">


	.dc_pack{
			background-color:#f5f5f5;
			border:1px solid #ccc;
			height:80px;
			padding:15px;
		}
		button, input, select, textarea{
			background-color:#ffffff;
		}
		.dc_pack_button{
			float:right;
		}
		.dc_packtitle{
				padding:12px;font-weight:bold;
			}
		@media screen and (max-width:1024px){
			.dc_pack{
					background-color:#f5f5f5;
					border:1px solid #ccc;
					height:128px;
					padding:15px;
				}
			.dc_pack_button{
					float:left;
					margin-top:3px;
					background-color:#
				}
			.dc_packtitle{
					text-align:center;font-size:15px;
				}
				input[type="button"], input[type="reset"], input[type="submit"], .bbp-submit-wrapper button[type="submit"]{
					background-color:#00b4cc !important;
				}
			.dcpack_select{
					float:right;
				}
			}
		@media screen and (min-width:640px) and (max-width:780px){
				.dc_pack{
					background-color:#f5f5f5;
					border:1px solid #ccc;
					height:128px;
					padding:15px;
				}
				.dc_pack_button{
					float:left;
					margin-top:3px;
					background-color:#
				}
				.dc_packtitle{
					text-align:center;font-size:15px;
				}
				input[type="button"], input[type="reset"], input[type="submit"], .bbp-submit-wrapper button[type="submit"]{
					background-color:#00b4cc !important;
				}
				.dcpack_select{
					float:right;
				}
			}
		@media screen and (min-width:320px) and (max-width:485px){
			.input-text{
				width:95%;
				margin-bottom:25px;
			}
			.dc_pack{
				background-color:#f5f5f5;
				border:1px solid #ccc;
				height:148px;
				padding:15px;
			}
			.dc_pack_button{
					float:left;
					margin-top:3px;
					background-color:#
			}
			.dc_packtitle{
					text-align:center;font-size:15px;
			}
			input[type="button"], input[type="reset"], input[type="submit"], .bbp-submit-wrapper button[type="submit"]{
					background-color:#00b4cc !important;
			}
			.dcpack_select{
					float:right;
				}
		}
		@media screen and (max-width:480px){
			.dc_pack{
				height:132px;
			}
		}
		@media screen and (max-width:360px){
			.dc_pack{
				height:142px;
			}
			.dcpack_select{
						float:right;
				}
		}
		@media screen and (max-width:320px){
			.dc_pack{
				height:142px;
			}
			.dcpack_select{
					margin-left:0;
			}
		}
</style>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}

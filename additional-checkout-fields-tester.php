<?php
/**
 * Plugin Name: Additional Checkout Fields Tester
 * Description: Adds additional checkout fields to the WooCommerce Checkout block for testing.
 * Author:      WooCommerce
 * Author URI:  https://woo.com
 * Version:     1.0
 * Requires at least: 6.1
 * Tested up to: 6.4.2
 * WC requires at least: 8.7
 * WC tested up to: 8.7
 */


add_action( 'woocommerce_loaded', 'custom_fields_tester_register_custom_checkout_fields' );

/**
 * Registers custom checkout fields for the WooCommerce checkout form.
 *
 * @return void
 * @throws Exception If there is an error during the registration of the checkout fields.
 */
function custom_fields_tester_register_custom_checkout_fields() {
	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/alt-email',
			'label'    => 'Alternative Email',
			'location' => 'contact',
			'type'     => 'text',
			'required' => true,
		)
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/job-function',
			'label'    => 'What is your main role at your company?',
			'location' => 'contact',
			'type'     => 'select',
			'options'  => [
				[
					'label' => 'Director',
					'value' => 'director',
				],
				[
					'label' => 'Engineering',
					'value' => 'engineering',
				],
				[
					'label' => 'Customer Support',
					'value' => 'customer-support',
				],
				[
					'label' => 'Other',
					'value' => 'other',
				]
			]
		)
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/mailing-list',
			'label'    => 'Sign up to our mailing list?',
			'location' => 'contact',
			'type'     => 'checkbox',
		)
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/gov-id',
			'label'    => 'Government ID',
			'location' => 'address',
			'type'     => 'text',
			'required' => true,
		),
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/confirm-gov-id',
			'label'    => 'Confirm Government ID',
			'location' => 'address',
			'type'     => 'text',
			'required' => true,
		),
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/contact-about-order',
			'label'    => 'Can we contact you about your order?',
			'location' => 'address',
			'type'     => 'checkbox',
		),
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/preferred-contact-time',
			'label'    => 'Preferred time to contact (Morning, Afternoon, or Evening)',
			'location' => 'address',
			'type'     => 'select',
			'options'  => [
				[
					'label' => 'Morning',
					'value' => 'morning',
				],
				[
					'label' => 'Afternoon',
					'value' => 'afternoon',
				],
				[
					'label' => 'Evening',
					'value' => 'evening',
				],
			],
		),
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/leave-on-porch',
			'label'    => __( 'Please leave my package on the porch if I\'m not home', 'woocommerce' ),
			'location' => 'additional',
			'type'     => 'checkbox',
		),
	);

	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/location-on-porch',
			'label'    => __( 'Describe where we should hide the parcel', 'woocommerce' ),
			'location' => 'additional',
			'type'     => 'text',
		)
	);
	woocommerce_blocks_register_checkout_field(
		array(
			'id'       => 'plugin-namespace/leave-with-neighbor',
			'label'    => __( 'Which neighbor should we leave it with if unable to hide?', 'woocommerce' ),
			'location' => 'additional',
			'type'     => 'select',
			'options'  => array(
				array(
					'label' => 'Neighbor to the left',
					'value' => 'left',
				),
				array(
					'label' => 'Neighbor to the right',
					'value' => 'right',
				),
				array(
					'label' => 'Neighbor across the road',
					'value' => 'across',
				),
				array(
					'label' => 'Do not leave with a neighbor',
					'value' => 'none',
				),
			),
		)
	);

	add_action(
		'woocommerce_blocks_validate_additional_field',
		function ( \WP_Error $errors, $field_key, $field_value ) {
			if ( 'plugin-namespace/gov-id' === $field_key ) {
				$match = preg_match( '/[A-Z0-9]{5}/', $field_value );
				if ( 0 === $match || false === $match ) {
					$errors->add( 'invalid_gov_id', 'Please ensure your government ID matches the correct format.' );
				}
			}
			if ( 'plugin-namespace/alt-email' === $field_key ) {
				if ( ! is_email( $field_value ) ) {
					$errors->add( 'invalid_alt_email', 'Please ensure your alternative email matches the correct format.' );
				}
			}
		},
		10,
		3
	);

	add_action(
		'woocommerce_blocks_sanitize_additional_field',
		function( $value, $key ) {
			if ( 'plugin-namespace/gov-id' === $key || 'plugin-namespace/confirm-gov-id' === $key ) {
				return trim( $value );
			}
			return $value;
		},
		10,
		2
	);

	add_action(
		'woocommerce_blocks_validate_location_address_fields',
		function ( \WP_Error $errors, $fields, $group ) {
			if ( $fields['plugin-namespace/gov-id'] !== $fields['plugin-namespace/confirm-gov-id'] ) {
				$errors->add( 'gov_id_mismatch', 'Please ensure your government ID matches the confirmation.' );
			}
		},
		10,
		3
	);
}

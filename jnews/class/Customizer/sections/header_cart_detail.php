<?php

$options = [];

$options[] = [
	'id'        => 'jnews_header_cart_detail_color',
	'transport' => 'postMessage',
	'default'   => '',
	'type'      => 'jnews-color',
	'label'     => esc_html__( 'Cart Icon Color', 'jnews' ),
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.cartdetail.woocommerce .jeg_carticon',
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_text_price_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Text Color', 'jnews' ),
	'description' => esc_html__( 'cart text color', 'jnews' ),
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.cartdetail.woocommerce .cartlink',
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'    => 'jnews_header_cart_detail_drop_style',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Cart Drop Style', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_bg_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Background Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop content background color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_cart.cartdetail .jeg_cartcontent",
			'property' => 'background-color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Text Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop text color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce ul.cart_list li a,
                                        .cartdetail.woocommerce ul.product_list_widget li a,
                                        .cartdetail.woocommerce .widget_shopping_cart_content .total",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_product_quantity_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Product Quantity Text Color', 'jnews' ),
	'description' => esc_html__( 'Product quantity text color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce .cart_list .quantity,
                                        .cartdetail.woocommerce .product_list_widget .quantity",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_border_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Border Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop border color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce .widget_shopping_cart_content .total",
			'property' => 'border-top-color',
		],
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce .widget_shopping_cart_content .total",
			'property' => 'border-bottom-color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_button_bg_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Button Background Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop button background color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce .widget_shopping_cart_content .button",
			'property' => 'background-color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_button_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Button Text Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop button text color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce a.button",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_button_bg_hover_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Button Background Hover Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop button background hover color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce a.button:hover",
			'property' => 'background-color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_cart_detail_button_text_hover_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Cart Drop Button Text Hover Color', 'jnews' ),
	'description' => esc_html__( 'Cart drop button text hover color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".cartdetail.woocommerce a.button:hover",
			'property' => 'color',
		],
	],
];

return $options;
<?php
/*

Class WC_Schedule_Booking_Product
Created: 15/09/2023
Update: 15/09/2023
Author: Gabriel Caroprese

*/
if (!defined('ABSPATH')) {
    exit;
}

class WC_Schedule_Booking_Product extends WC_Product {

    public function __construct($product) {
        $this->product_type = 'schedule_booking_product';
        parent::__construct($product);
    }

    // Override the display name for this product type
    public function get_type() {
        return 'schedule_booking_product';
    }

    //add to cart method
    public function add_to_cart_url() {
        $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
    
        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }
}
?>
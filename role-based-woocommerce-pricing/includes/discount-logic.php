<?php
// Preise für Benutzerrollen um den konfigurierten Rabatt reduzieren
add_filter('woocommerce_product_get_price', 'apply_custom_discount', 10, 2);
add_filter('woocommerce_product_get_sale_price', 'apply_custom_discount', 10, 2);
add_filter('woocommerce_get_price_html', 'apply_custom_discount_html', 10, 2);

function apply_custom_discount($price, $product) {
    $discount = get_user_role_discount();
    if ($discount > 0) {
        $original_price = floatval($product->get_regular_price()); // Holen des ursprünglichen Preises
        return round($original_price * ((100 - $discount) / 100), 2);
    }
    return $price;
}

function apply_custom_discount_html($price_html, $product) {
    $discount = get_user_role_discount();
    if ($discount > 0) {
        $original_price = floatval($product->get_regular_price()); // Holen des ursprünglichen Preises
        $discounted_price = round($original_price * ((100 - $discount) / 100), 2);
        return wc_price($discounted_price);
    }
    return $price_html;
}

function get_user_role_discount() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $roles = $user->roles;
        $discount = 0;

        $options = get_option('custom_discount_settings');
        foreach ($roles as $role) {
            if (isset($options['discount_' . $role])) {
                $discount = max($discount, floatval($options['discount_' . $role]));
            }
        }

        return $discount;
    }
    return 0;
}

// Rabatt auf Warenkorb anwenden
add_action('woocommerce_before_calculate_totals', 'apply_cart_custom_discount');
function apply_cart_custom_discount($cart) {
    $discount = get_user_role_discount();
    if ($discount > 0) {
        foreach ($cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            $original_price = floatval($product->get_regular_price()); // Holen des ursprünglichen Preises
            $discounted_price = round($original_price * ((100 - $discount) / 100), 2);
            $cart_item['data']->set_price($discounted_price);
        }
    }
}
?>

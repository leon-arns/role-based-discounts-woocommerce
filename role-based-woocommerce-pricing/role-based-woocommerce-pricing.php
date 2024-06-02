<?php
/*
Plugin Name: Role Based WooCommerce Pricing
Description: Custom discounts for different user roles in WooCommerce.
Version: 1.0
Author: internova.digital
Text Domain: role-based-woocommerce-pricing
Domain Path: /languages
*/

// Sicherstellen, dass WooCommerce aktiviert ist
add_action('admin_init', 'rbwp_check_woocommerce_active');

function rbwp_check_woocommerce_active() {
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        add_action('admin_notices', 'rbwp_woocommerce_not_active_notice');
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

function rbwp_woocommerce_not_active_notice() {
    $woocommerce_install_url = admin_url('update.php?action=install-plugin&plugin=woocommerce&_wpnonce=4598efcb31');
    ?>
    <div class="error">
        <p>
            <?php 
            printf(
                wp_kses(
                    __('Role Based WooCommerce Pricing requires %sWooCommerce%s to be installed and active.', 'role-based-woocommerce-pricing'),
                    array(
                        'a' => array(
                            'href' => array()
                        )
                    )
                ),
                '<a href="' . esc_url($woocommerce_install_url) . '">',
                '</a>'
            );
            ?>
        </p>
    </div>
    <?php
}

// Load plugin textdomain
add_action('init', 'rbwp_load_textdomain');
function rbwp_load_textdomain() {
    load_plugin_textdomain('role-based-woocommerce-pricing', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Include admin settings
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

// Include discount logic
require_once plugin_dir_path(__FILE__) . 'includes/discount-logic.php';
?>

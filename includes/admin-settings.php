<?php
// Admin-Seite erstellen
add_action('admin_menu', 'custom_discount_admin_menu');
function custom_discount_admin_menu() {
    add_submenu_page(
        'woocommerce',
        __('Discounts', 'role-based-woocommerce-pricing'),
        __('Discounts', 'role-based-woocommerce-pricing'),
        'manage_options',
        'user_discounts',
        'custom_discount_admin_page'
    );
}

function custom_discount_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Role Based Discounts', 'role-based-woocommerce-pricing'); ?></h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_discount_settings');
            do_settings_sections('custom_discount_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Einstellungen registrieren
add_action('admin_init', 'custom_discount_settings_init');
function custom_discount_settings_init() {
    register_setting('custom_discount_settings', 'custom_discount_settings', 'sanitize_custom_discount_settings');

    add_settings_section(
        'custom_discount_section',
        __('Discounts for User Roles', 'role-based-woocommerce-pricing'),
        'custom_discount_section_callback',
        'custom_discount_settings'
    );

    $roles = get_editable_roles();
    foreach ($roles as $role_name => $role_info) {
        add_settings_field(
            'discount_' . $role_name,
            $role_info['name'],
            'custom_discount_field_callback',
            'custom_discount_settings',
            'custom_discount_section',
            ['role' => $role_name]
        );
    }
}

function sanitize_custom_discount_settings($input) {
    $sanitized_input = [];
    foreach ($input as $key => $value) {
        // Komma durch Punkt ersetzen und sicherstellen, dass es eine Zahl ist
        $sanitized_input[$key] = floatval(str_replace(',', '.', $value));
    }
    // Erfolgsmeldung hinzufügen
    add_settings_error(
        'custom_discount_settings',
        'settings_updated',
        __('Discount settings saved.', 'role-based-woocommerce-pricing'),
        'updated'
    );
    return $sanitized_input;
}

function custom_discount_section_callback() {
    echo '<p>' . __('Set discounts for each user role in percentage.', 'role-based-woocommerce-pricing') . '</p>';
}

function custom_discount_field_callback($args) {
    $options = get_option('custom_discount_settings');
    $role = $args['role'];
    $value = isset($options['discount_' . $role]) ? $options['discount_' . $role] : '';
    echo '<input type="text" name="custom_discount_settings[discount_' . esc_attr($role) . ']" value="' . esc_attr($value) . '" /> %';
}
?>

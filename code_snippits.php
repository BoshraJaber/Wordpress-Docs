// Meta box callback to display the dropdown field
function my_main_heading_meta_box_callback($post) {
    // Retrieve the current value of the dropdown
    $main_heading = get_post_meta($post->ID, '_my_main_heading_meta_key', true);

    // Determine the current language (assuming WPML or Polylang is used)
    $current_language = function_exists('pll_current_language') ? pll_current_language() : get_locale(); // Get current language

    // Define the options for the dropdown based on the current language
    if ($current_language === 'ar') {
        // Arabic options
        $options = [
            '' => 'اختر العنوان الرئيسي للصفحة',  // Example translation for 'Select Page Main Heading'
            'ABOUT US' => 'من نحن',
            'OUR BUSINESS' => 'أعمالنا',
            'SUSTAINABILITY' => 'الاستدامة',
            'TENDERS' => 'المناقصات',
            'Media Center' => 'مركز الإعلام',
            'Contact Us' => 'اتصل بنا',
        ];
    } else {
        // English options (default)
        $options = [
            '' => 'Select Page Main Heading',
            'ABOUT US' => 'ABOUT US',
            'OUR BUSINESS' => 'OUR BUSINESS',
            'SUSTAINABILITY' => 'SUSTAINABILITY',
            'TENDERS' => 'TENDERS',
            'Media Center' => 'Media Center',
            'Contact Us' => 'Contact Us',
        ];
    }

    // Output the nonce for security
    wp_nonce_field('my_main_heading_nonce_action', 'my_main_heading_nonce');

    // Output the dropdown
    ?>
    <p>
        <label for="my_main_heading_field"><?php echo esc_html($current_language === 'ar' ? 'اختر العنوان الرئيسي للصفحة' : 'Select Main Heading'); ?>:</label>
        <select id="my_main_heading_field" name="my_main_heading_field">
            <?php foreach ($options as $value => $label): ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($main_heading, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

function my_save_main_heading_meta_box_data($post_id) {
    // Verify the nonce before proceeding
    if (!isset($_POST['my_main_heading_nonce']) || !wp_verify_nonce($_POST['my_main_heading_nonce'], 'my_main_heading_nonce_action')) {
        return;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the selected dropdown value
    if (isset($_POST['my_main_heading_field'])) {
        $selected_value = sanitize_text_field($_POST['my_main_heading_field']);
        // Save the value in the meta field
        update_post_meta($post_id, '_my_main_heading_meta_key', $selected_value);
    } else {
        delete_post_meta($post_id, '_my_main_heading_meta_key');
    }
}
add_action('save_post', 'my_save_main_heading_meta_box_data');

// Shortcode to display the main heading
function page_main_heading_shortcode() {
    $post_id = get_the_ID();
    $main_heading = get_post_meta($post_id, '_my_main_heading_meta_key', true);

    if (!empty($main_heading)) {
        return '<div class=""><h1>' . esc_html($main_heading) . '</h1></div>';
    } else {
        return '';
    }
}
add_shortcode('page_main_heading', 'page_main_heading_shortcode');

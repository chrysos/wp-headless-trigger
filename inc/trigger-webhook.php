<?php

/**
 * Trigger the Webhook each time a Post or Page is updated
 */
function wp_headless_trigger_trigger_webhook_on_save_post($new_status, $old_status, $post)
{
    if ($new_status === 'publish' || $old_status === 'publish') {
        $wp_headless_trigger_settings = get_option('wp_headless_trigger_settings');
        if (
            !array_key_exists('wp_headless_trigger_webhook_url', $wp_headless_trigger_settings) ||
            empty($wp_headless_trigger_settings['wp_headless_trigger_webhook_url'])
        ) {
            return false;
        }
        $webhook_url = esc_url($wp_headless_trigger_settings['wp_headless_trigger_webhook_url']);
        $response = wp_remote_post($webhook_url);
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("WP Headless Trigger: " . $error_message, 0);
        } else {
            update_option('wp_headless_trigger_last_trigger', time());
        }
    }
}
add_action('transition_post_status', 'wp_headless_trigger_trigger_webhook_on_save_post', 10, 3);

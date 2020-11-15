<?php

defined( 'ABSPATH' ) || exit;

/**
 * Save error message when update form
 *
 * @param cf7 call back method
*/

add_action('wpcf7_save_contact_form', 'action_cf7cvemsg_save_contact_form', 9, 1);

function action_cf7cvemsg_save_contact_form($contact_form) {
    $tags = $contact_form->scan_form_tags();
    $post_id = $contact_form->id();

    foreach ($tags as $value) {
        if ($value['type'] == 'select*' || $value['type'] == 'text*' || $value['type'] == 'email*' || $value['type'] == 'textarea*' || $value['type'] == 'tel*' || $value['type'] == 'url*' || $value['type'] == 'checkbox*' || $value['type'] == 'file*' || $value['type'] == 'date*' || $value['type'] == 'radio' || $value['type'] == 'number*') {
            $key = "_cf7cmsg_" . $value['name'] . "-valid";
            update_post_meta($post_id, $key, $value['name']);
        }
    }
}

/**
 * Save error message when create form
 *
 * @param cf7 call back method
*/

add_action('wpcf7_after_create', 'action_after_create_wpcf7', 9, 1);

function action_after_create_wpcf7($instance) {
    $tags = $instance->form_scan_shortcode();
    $post_id = $instance->id();

    foreach ($tags as $value) {

        if ($value['type'] == 'select*' || $value['type'] == 'text*' || $value['type'] == 'email*' || $value['type'] == 'textarea*' || $value['type'] == 'tel*' || $value['type'] == 'url*' || $value['type'] == 'checkbox*' || $value['type'] == 'file*' || $value['type'] == 'date*' || $value['type'] == 'radio' || $value['type'] == 'number*') {
            $key = "_cf7cmsg_" . $value['name'] . "-valid";
            update_post_meta($post_id, $key, $value['name']);
        }
    }
}

/**
 * Get all required field
 *
 * @param cf7 call back method
*/

function cf7cvemsg_get_meta_values($p_id = '', $key = '') {

    global $wpdb;
    if (empty($key))
        return;

    $result = $wpdb->get_results("SELECT pm.meta_value FROM {$wpdb->postmeta} pm WHERE pm.meta_key LIKE '%$key%' AND pm.post_id = $p_id ");

    return $result;
}

/**
 * Setup message field for cf7
 *
 * @param cf7 call back method
*/

function cf7cvemsg_custom_validation_messages($messages) {

    if (isset($_GET['post']) && !empty($_GET['post'])) {
        $p_id = sanitize_text_field($_GET['post']);
        $p_val = cf7cvemsg_get_meta_values($p_id, '_cf7cmsg_');

        foreach ($p_val as $value) {
            $key = $value->meta_value;
            $newmsg = array(
                'description' => __("Error message for $value->meta_value field", CF7CVEMSG_TEXT_DOMAIN),
                'default' => __("The field is required.", CF7CVEMSG_TEXT_DOMAIN)
                );

            $messages[$key] = $newmsg;
        }
    }
    return $messages;
}

add_filter('wpcf7_messages', 'cf7cvemsg_custom_validation_messages', 10, 1);

/**
 * Validation Filter
 *
 * @param cf7 call back method
 */

add_filter('wpcf7_validate_text', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_text*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_email', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_email*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_url', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_url*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_tel', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_tel*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_textarea', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_textarea*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_number', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_number*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_range', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_range*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_date', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_date*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_checkbox', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_checkbox*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_radio', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_file', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter('wpcf7_validate_file*', 'cf7cvemsg_custom_form_validation_filter', 10, 2);
add_filter( 'wpcf7_validate_select', 'cf7cvemsg_custom_form_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_select*', 'cf7cvemsg_custom_form_validation_filter', 10, 2 );

function cf7cvemsg_custom_form_validation_filter($result, $tag) {

    $tag_name = $tag->name;
    $name = $tag_name;

    if (empty($name)) {
        $name = __("invalid_required", CF7CVEMSG_TEXT_DOMAIN );
    }

    if ('text' == $tag->basetype) {
        $value = isset($_POST[$tag_name]) ? trim(sanitize_text_field(strtr((string) $_POST[$tag_name], "\n", " "))) : '';
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        }
    }

    if ('email' == $tag->basetype) {

        $value = isset($_POST[$tag_name]) ? trim(sanitize_text_field(strtr((string) $_POST[$tag_name], "\n", " "))) : '';
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        } elseif ('' != $value && !wpcf7_is_email($value)) {
            $result->invalidate($tag, wpcf7_get_message($name));
        }
    }

    if ('url' == $tag->basetype) {

        $value = isset($_POST[$tag_name]) ? trim(sanitize_text_field(strtr((string) $_POST[$tag_name], "\n", " "))) : '';
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        } elseif ('' != $value && !wpcf7_is_url($value)) {
            $result->invalidate($tag, wpcf7_get_message($name));
        }
    }

    if ('tel' == $tag->basetype) {

        $value = isset($_POST[$tag_name]) ? trim(sanitize_text_field(strtr((string) $_POST[$tag_name], "\n", " "))) : '';
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        } elseif ('' != $value && !wpcf7_is_tel($value)) {
            $result->invalidate($tag, wpcf7_get_message($name));
        }
    }

    if ('number' == $tag->basetype) {

        $value = isset($_POST[$tag_name]) ? trim(sanitize_text_field(strtr((string) $_POST[$tag_name], "\n", " "))) : '';
        $min = $tag->get_option('min', 'signed_int', true);
        $max = $tag->get_option('max', 'signed_int', true);

        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        } elseif ('' != $value && !wpcf7_is_number($value)) {
            $result->invalidate($tag, wpcf7_get_message($name));
            $result->invalidate($tag, wpcf7_get_message($name));
        } elseif ('' != $value && '' != $min && (float) $value < (float) $min) {
            $result->invalidate($tag, wpcf7_get_message('number_too_small'));
        } elseif ('' != $value && '' != $max && (float) $max < (float) $value) {
            $result->invalidate($tag, wpcf7_get_message('number_too_large'));
        }
    }
    if ('date' == $tag->basetype) {

        $min = $tag->get_date_option('min');
        $max = $tag->get_date_option('max');

        $value = isset($_POST[$tag_name]) ? trim(sanitize_text_field(strtr((string) $_POST[$tag_name], "\n", " "))) : '';

        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        } elseif ('' != $value && !wpcf7_is_date($value)) {
            $result->invalidate($tag, wpcf7_get_message('invalid_date'));
        } elseif ('' != $value && !empty($min) && $value < $min) {
            $result->invalidate($tag, wpcf7_get_message('date_too_early'));
        } elseif ('' != $value && !empty($max) && $max < $value) {
            $result->invalidate($tag, wpcf7_get_message('date_too_late'));
        }
    }

    if ('textarea' == $tag->basetype) {
        $value = isset($_POST[$tag_name]) ? sanitize_text_field($_POST[$tag_name]) : '';

        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message($name));
        }

        if ('' !== $value) {
            $maxlength = $tag->get_maxlength_option();
            $minlength = $tag->get_minlength_option();

            if ($maxlength && $minlength && $maxlength < $minlength) {
                $maxlength = $minlength = null;
            }

            $code_units = wpcf7_count_code_units(stripslashes($value));

            if (false !== $code_units) {
                if ($maxlength && $maxlength < $code_units) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_too_long'));
                } elseif ($minlength && $code_units < $minlength) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_too_short'));
                }
            }
        }
    }
    
    if ('checkbox' == $tag->basetype || 'radio' == $tag->basetype) {

        $is_required = $tag->is_required() || 'radio' == $tag->type;
        $value = isset($_POST[$tag_name]) ? array_map( 'sanitize_text_field', wp_unslash((array) $_POST[$tag_name]) ) : array();
        
        if ($is_required && empty($value)) {
            $result->invalidate($tag, wpcf7_get_message($name));
        }
    }
    
    if ('select' == $tag->basetype){
        
       $name = $tag->name;

	$has_value = isset( $_POST[$name] ) && '' !== $_POST[$name];

	if ( $has_value and $tag->has_option( 'multiple' ) ) {
                
                $multiple_option_array = array_map( 'sanitize_text_field', wp_unslash((array) $_POST[$name]) );
                $vals = array_filter( $multiple_option_array, function( $val ) {
			return '' !== $val;
		} );

		$has_value = ! empty( $vals );
	}
        
	if ( $tag->is_required() and ! $has_value ) {
		$result->invalidate( $tag, wpcf7_get_message( $name ) );
	} 
    }
    
    if ('file' == $tag->basetype) {
        if ($tag->is_required() && empty($_FILES[$tag_name]['name'])) {
            $result->invalidate($name, wpcf7_get_message($name));
        }
    }
    return $result;
}
<?php

namespace ExtendBuilder;

use ColibriWP\PageBuilder\PageBuilder;

function prefix($name = "")
{
    return "extend_builder_$name";
}

function get_public_post_types()
{
    $args = array(
        'public'   => true,
		'_builtin' => false,
	);

	$output   = 'names';
	$operator = 'and';

	$post_types = get_post_types( $args, $output, $operator );

	return $post_types;
}

function log($msg)
{
//    openlog("extend builder", LOG_PID | LOG_PERROR, LOG_USER);
//    $access = date("Y/m/d H:i:s");
//    syslog(LOG_WARNING, "$access : $msg");
//    closelog();
}

function log2($msg) {
   $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $d = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
    error_log($msg."->".$d->format("Y-m-d H:i:s.u"));
}

$colibri_loaded_files_values = array();

function load_file_value($key, $json_string)
{
    global $colibri_loaded_files_values;
    if (is_string($json_string)) {
        $colibri_loaded_files_values[$key] = json_decode($json_string, true);
    } else {
        $colibri_loaded_files_values[$key] = $json_string;
    }
    return $colibri_loaded_files_values[$key];
}

function get_file_value( $key ) {
	global $colibri_loaded_files_values;
	return $colibri_loaded_files_values[ $key ];
}

function get_key_value( $array, $key, $default ) {
	$value = array_get_value( $array, $key, $default );

	return $value;
}

/**
 * @param array $array
 * @param array|string $parents
 * @param string $glue
 *
 * @return mixed
 */
function array_get_value( array &$array, $parents, $default = null, $glue = '.' ) {
	if ( ! is_array( $parents ) ) {
		$parents = explode( $glue, $parents );
	}

	$ref = &$array;

	foreach ( (array) $parents as $parent ) {
		if ( is_array( $ref ) && array_key_exists( $parent, $ref ) ) {
			$ref = &$ref[ $parent ];
		} else {
			return $default;
		}
	}

	return $ref;
}

function colibri_esc_html_preserve_spaces($text){
	return esc_html( str_replace( " ", "&nbsp;",$text));
}
/**
 * @param array $array
 * @param array|string $parents
 * @param mixed $value
 * @param string $glue
 */
function array_set_value( array &$array, $parents, $value, $glue = '.' ) {
	if ( ! is_array( $parents ) ) {
		$parents = explode( $glue, (string) $parents );
	}

	$ref = &$array;

	foreach ( $parents as $parent ) {
		if ( isset( $ref ) && ! is_array( $ref ) ) {
			$ref = array();
		}

		$ref = &$ref[ $parent ];
	}

	$ref = $value;
}

/**
 * @param array $array
 * @param array|string $parents
 * @param string $glue
 */
function array_unset_value( &$array, $parents, $glue = '.' ) {
	if ( ! is_array( $parents ) ) {
		$parents = explode( $glue, $parents );
	}

	$key = array_shift( $parents );

	if ( empty( $parents ) ) {
		unset( $array[ $key ] );
	} else {
		array_unset_value( $array[ $key ], $parents );
	}
}

function colibri_placeholder_p( $text, $echo = false ) {
	$content = "";

	if ( mesmerize_is_customize_preview() ) {
		$content = '<p class="content-placeholder-p">' . $text . '</p>';
	}

	if ( $echo ) {
		echo $content;
	} else {
		return $content;
	}
}

function colibri_cache_get( $name, $default = null ) {

	$colibri_cache = isset( $GLOBALS['__colibri_plugin_cache__'] ) ? $GLOBALS['__colibri_plugin_cache__'] : array();
	$value         = $default;

	if ( colibri_cache_has( $name ) ) {
		$value = $colibri_cache[ $name ];
	}

	return $value;

}

function colibri_cache_has( $name ) {
	$colibri_cache = isset( $GLOBALS['__colibri_plugin_cache__'] ) ? $GLOBALS['__colibri_plugin_cache__'] : array();

	return array_key_exists( $name, $colibri_cache );
}

function colibri_cache_set( $name, $value ) {
	$colibri_cache          = isset( $GLOBALS['__colibri_plugin_cache__'] ) ? $GLOBALS['__colibri_plugin_cache__'] : array();
	$colibri_cache[ $name ] = $value;

	$GLOBALS['__colibri_plugin_cache__'] = $colibri_cache;

}

function is_true( $var ) {

	if ( $var === true || intval( $var ) !== 0 ) {
		return true;
	}


	switch ( strtolower( $var ) ) {
		case '1':
		case 'true':
		case 'on':
		case 'yes':
		case 'y':
			return true;
		default:
			return false;
	}
}


function is_false( $var ) {

	if ( $var === false || intval( $var ) === 0 ) {
		return true;
	}

	switch ( strtolower( $var ) ) {
		case '0':
		case 'false':
		case 'off':
		case 'no':
		case 'n':
			return true;
		default:
			return false;
	}
}

function get_template_part( $slug, $name = null ) {
	do_action( "get_template_part_{$slug}", $slug, $name );

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	$located_in_theme = locate_template( $templates, false, false );

	if ( $located_in_theme ) {
		locate_template( $templates, true, false );
	} else {
		foreach ( $templates as $template_name ) {
			$path = "/template-parts/$template_name";
			if ( PageBuilder::instance()->fileExists( $path ) ) {
				PageBuilder::instance()->loadFile( $path );
				break;
			}
		}
	}
}

function apply_customizer_preview_context() {
	if ( ! is_customize_preview() ) {
		return;
	}

	$context = isset( $_REQUEST['context'] ) ? $_REQUEST['context'] : array();
	$query   = isset( $context['query'] ) ? $context['query'] : array();

	if ( count( $query ) ) {
		query_posts( $query );
	}

}

function ob_wrap( $function, $params = array() ) {
	ob_start();
	call_user_func_array( $function, $params );

	return ob_get_clean();
}
function colibri_current_user_has_role($role) {
    $user = wp_get_current_user();
    if ( in_array($role , (array) $user->roles ) ) {
       return true;
    }
    return false;
}
function colibri_shortcode_decode($data) {
    return  urldecode(base64_decode($data));
}

function get_colibri_image($name)
{
    global $wpdb;
    $posts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title LIKE '%s'", '%'. $wpdb->esc_like( $name ) .'%') );
    if ($posts && count($posts)) {
        $id = $posts[0]->ID;
        return array("id" => $id, "url" => wp_get_attachment_url($id));
    }
}

function import_colibri_image($url)
{
    $name = basename($url);
    $existing_image = get_colibri_image($name);
    if ($existing_image) {
        $existing_image['colibri-url'] = $url;
        return $existing_image;
    }

    $filename = $name;
    $file_content = wp_remote_retrieve_body(wp_safe_remote_get($url));
    if (empty($file_content)) {
        return false;
    }

    $upload = wp_upload_bits(
        $filename,
        null,
        $file_content
    );

    $post = [
        'post_title' => $filename,
        'guid' => $upload['url'],
    ];

    $info = wp_check_filetype($upload['file']);
    if ($info) {
        $post['post_mime_type'] = $info['type'];
    }

    $post_id = wp_insert_attachment($post, $upload['file']);
    wp_update_attachment_metadata(
        $post_id,
        wp_generate_attachment_metadata($post_id, $upload['file'])
    );
    $new_attachment = array(
        'colibri-url' => $url,
        'url' => $upload['url'],
        'id' => $post_id,
    );
    return $new_attachment;
}

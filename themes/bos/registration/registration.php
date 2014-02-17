<?php

define('AIB_DIR',  get_template_directory_uri() . '/registration');
define('AIB_PATH', get_template_directory() . '/registration');
define('AIB_VERSION', '0.3');

require __DIR__ . '/lib/custom-post-type.php';
require __DIR__ . '/aib.php';

// Return a escaped POST value, if one exists
if (!function_exists('v')) {
  function v($name) {
    if (!empty($_POST[$name])) {
      return esc_attr($_POST[$name]);
    } else {
      return '';
    }
  }
}

class AIB_Registration extends Theme {
  
  function __construct() {
    $this->add_action('init');
    $this->add_action('after_switch_theme');
    if (preg_match('/\/(\w+)$/', get_bloginfo('url'), $matches)) {
      // Whatever the site slug is (i.e., 'bos2014')
      define('AIB_EVENT', $matches[1]);
    } else {
      // Generic value for development
      define('AIB_EVENT', 'artsinbushwick');
    }
    if (!empty($_GET['generate_tokens'])) {
      $this->generate_tokens();
      $url = get_bloginfo('url');
      header("Location: $url/registration-form/");
      exit;
    }
  }
  
  function init() {
    $this->aib = new AIB_Custom_Post();
    if (!empty($_POST['task'])) {
      $method = "post_{$_POST['task']}";
      if (method_exists($this, $method)) {
        $this->$method();
      }
    }
  }
  
  function after_switch_theme() {
    // Setup MySQL tables if they haven't already been created
    $this->setup_db_tables();
  }
  
  function post_login() {
    global $aib_login_response;
    $email = trim(strtolower($_POST['email']));
    $username = $this->get_username($email);
    if ($_POST['action'] == 'create') {
      $aib_login_response = $this->create_listing($email);
    } else {
      $aib_login_response = $this->edit_listing($email);
    }
    if (empty($aib_login_response)) {
      $url = get_bloginfo('url');
      header("Location: $url/registration-form/");
      exit;
    }
  }
  
  function post_save() {
    global $wpdb, $blog_id;
    
    unset($_POST['blog_id']);
    extract($_POST);
    
    if (strlen($post_excerpt) > 140) {
      $post_excerpt = substr($post_excerpt, 0, 140);
    }
    
    $post = array(
      'ID' => $post_id,
      'post_content' => $long_description,
      'post_excerpt' => $post_excerpt
    );
    
    if (!empty($primary_name)) {
      if ($primary_name == 'artists') {
        $post['post_title'] = $artists;
      } else if ($primary_name == 'organization') {
        $post['post_title'] = $organization;
      } else if ($primary_name == 'event_name') {
        $post['post_title'] = $event_name;
      }
    }
    wp_update_post($post);
    
    if (!empty($website) && substr($website, 0, 4) != 'http') {
      $website = "http://$website";
    }
    
    $wpdb->update('aib_listing', array(
      'artists' => stripslashes($artists),
      'organization' => stripslashes($organization),
      'event_name' => stripslashes($event_name),
      'primary_name' => $primary_name,
      'website' => $website,
      'artist_count' => $artist_count,
      'space_name' => stripslashes($space_name),
      'street_address' => stripslashes($street_address),
      'room_number' => $room_number,
      'zip_code' => $zip_code,
      'select_friday' => (empty($select_friday) ? '0' : '1'),
      'select_saturday' => (empty($select_saturday) ? '0' : '1'),
      'select_sunday' => (empty($select_sunday) ? '0' : '1'),
      'time_friday' => $time_friday,
      'time_saturday' => $time_saturday,
      'time_sunday' => $time_sunday,
      'additional_time_friday' => $additional_time_friday,
      'additional_time_saturday' => $additional_time_saturday,
      'additional_time_sunday' => $additional_time_sunday,
      'admission_free' => (empty($admission_free) ? '0' : '1'),
      'admission_price' => $admission_price,
      'long_description' => $long_description,
      'short_description' => $post_excerpt,
      'media_other' => stripslashes($media_other),
      'payment_method' => $payment_method,
      'volunteer_message' => stripslashes($volunteer_message),
      'legal_agreement' => (empty($legal_agreement) ? '0' : '1'),
      'community_agreement' => (empty($community_agreement) ? '0' : '1'),
      'referral' => stripslashes($referral),
      'profile_story' => (empty($profile_story) ? '0' : '1'),
      'benefit_interest' => (empty($benefit_interest) ? '0' : '1'),
      'long_description' => stripslashes($long_description)
    ), array('post_id' => $post_id, 'site_id' => $blog_id));
    
    if (!empty($tax_input['media'])) {
      $terms = array_map('intval', $tax_input['media']);
    } else {
      $terms = array();
    }
    wp_set_object_terms($post_id, $terms, 'media');
    
    if (!empty($tax_input['attributes'])) {
      $terms = array_map('intval', $tax_input['attributes']);
    } else {
      $terms = array();
    }
    wp_set_object_terms($post_id, $terms, 'attributes');
    
    $post = get_post($post_id);
    $listing = $wpdb->get_row("
      SELECT *
      FROM aib_listing
      WHERE post_id = $post_id
        AND site_id = $blog_id
    ");
    
    foreach (get_object_vars($listing) as $key => $value) {
      $post->$key = $value;
    }
    $wpdb->update('wp_posts', array(
      'post_content' => 
        $this->get_content($post) . "\n" .
        $this->get_subtitle($post) . "\n" .
        $this->get_show_location($post) . "\n" .
        $this->get_show_media($post) . "\n" .
        $this->get_show_features($post) . "\n" .
        $this->get_show_links($post) . "\n"
    ), array('ID' => $post->ID));
  }
  
  function create_listing($email) {
    $token = trim(strtoupper($_POST['token']));
    if (!$this->confirm_token($token)) {
      return '<ul><li><strong>ERROR</strong>: Sorry, that registration code is invalid. You can read more about registration codes <a href="' . get_bloginfo('url') . '/registration-codes/">here</a>. Please <a href="mailto:registration@artsinbushwick.org">contact us</a> if you cannot get your code to work.</li></ul>';
    } else if ($this->registration_exists($email)) {
      return '<ul><li>Oops, it appears that email address has already started the registration process for this year’s festival. Please try the "edit an existing listing" option.</li></ul>';
    }
    
    if (!email_exists($email)) {
      $response = $this->register_user($email);
    } else {
      $response = $this->setup_existing_user($email);
    }
    
    if (!is_numeric($response)) {
      return $response;
    }
    $user_id = $response;
    
    $this->setup_aib_post($user_id, $email);
    $this->claim_token($user_id, $token);
    
    // No errors
    return null;
  }
  
  function edit_listing($email) {
    $response = $this->login_user($email, $_POST['password']);
    if (is_numeric($response)) {
      $url = get_bloginfo('url');
      wp_safe_redirect("$url/registration-form/");
      exit;
    } else {
      return $response;
    }
  }
  
  function get_content($post) {
    $content = apply_filters('the_content', $post->post_content);
    if (empty($content) && !empty($post->post_excerpt)) {
      $content = $post->post_excerpt;
    }
    return $content;
  }
  
  function get_subtitle($post) {
    $key = $post->primary_name;
    $subtitle = array();
    if ($key != 'artists' && !empty($post->artists)) {
      $subtitle[] = esc_html($post->artists);
    }
    if ($key != 'organization' && !empty($post->organization)) {
      $subtitle[] = esc_html($post->organization);
    }
    if ($key != 'event_name' && !empty($post->event_name)) {
      $subtitle[] = esc_html($post->event_name);
    }
    return implode(', ', $subtitle);
  }
  
  function get_show_location($post) {
    if (empty($this->locations)) {
      $this->load_locations();
    }
    if (empty($post->location_id)) {
      return '<i>No location assigned</i>';
    } else {
      $location = $this->locations[$post->location_id];
      $title = $this->get_location_title($post);
      if (!empty($title)) {
        $title = "$title<br>\n";
      }
      $room = '';
      if (!empty($post->room_number)) {
        $room = " $post->room_number";
      }
      return "$title$location->address$room<br />Brooklyn, NY $location->zip";
    }
  }
  
  function load_locations() {
    global $wpdb;
    if (!empty($this->locations)) {
      return;
    }
    $objects = $wpdb->get_results("
      SELECT *
      FROM aib_location
    ");
    $this->locations = array();
    foreach ($objects as $object) {
      $this->locations[$object->id] = $object;
    }
  }
  
  function get_location_title($post) {
    if (empty($this->locations)) {
      $this->load_locations();
    }
    $location = $this->locations[$post->location_id];
    if (!empty($post->space_name)) {
      return $post->space_name;
    } else if (!empty($location->title)) {
      return $location->title;
    } else {
      return '';
    }
  }
  
  function get_show_media($post) {
    $terms = wp_get_object_terms($post->ID, 'media');
    $media = array();
    foreach ($terms as $term) {
      $name = $term->name;
      $name = preg_replace('/\d+\s(.+)/', '$1', $name);
      if ($name == 'Other' && !empty($post->media_other)) {
        $name = esc_html($post->media_other);
      }
      $media[] = $name;
    }
    return implode(", ", $media);
  }
  
  function get_show_features($post) {
    $terms = wp_get_object_terms($post->ID, 'attributes');
    $attributes = array();
    foreach ($terms as $term) {
      $name = $term->name;
      $name = preg_replace('/\d+\s(.+)/', '$1', $name);
      $attributes[] = $name;
    }
    if (empty($terms)) {
      return "<i class=\"empty\">No features selected</i>";
    } else {
      return implode("<br />\n", $attributes);
    }
  }
  
  function get_show_links($post) {
    global $wpdb;
    $result = '';
    if (!empty($post->website)) {
      $urls = explode(',', $post->website);
      foreach ($urls as $url) {
        $url = trim($url);
        if (substr($url, 0, 4) != 'http') {
          $url = "http://$url";
        }
        $show_url = preg_replace('#^http://#', '', $url);
        $show_url = preg_replace('#(.+)/#', '$1', $show_url);
        $result .= "&raquo; <a href=\"$url\" target=\"_new\">$show_url</a><br />\n";
      }
    }
    $email = $wpdb->get_var("
      SELECT user_email
      FROM $wpdb->users
      WHERE ID = $post->post_author
    ");
    $result .= "&raquo; <a href=\"mailto:$email\">Contact</a><br />";
    return $result;
  }
  
  function setup_aib_post($user_id, $email) {
    global $wpdb, $blog_id;
    
    $post_id = wp_insert_post(array(
      'post_type' => 'aib',
      'post_title' => $email,
      'post_content' => '',
      'post_status' => 'draft',
      'post_author' => $user_id,
      'post_category' => array()
    ));
    
    $wpdb->query($wpdb->prepare("
      INSERT INTO aib_listing
      (site_id, post_id, user_id)
      VALUES (%d, %d, %d)
    ", $blog_id, $post_id, $user_id));
    
    update_user_meta($user_id, 'aib_post_' . AIB_EVENT, $post_id);
  }
  
  function register_user($email) {
    global $table_prefix;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $this->create_new_user($email, $password, $confirm_password);
    if (is_numeric($user_id)) {
      update_user_meta($user_id, 'show_admin_bar_front', false);
      update_user_meta($user_id, 'user_level', 2);
      update_user_meta($user_id, "{$table_prefix}user_level", 2);
      update_user_meta($user_id, "{$table_prefix}capabilities", array(
        'author' => 1
      ));
      clean_user_cache($user_id);
    } else if (is_object($user_id) && get_class($user_id) == 'WP_Error') {
      return '<ul><li>' . implode('</li><li>', $user_id->get_error_messages()) . '</li></ul>';
    } else {
      return '<ul><li><strong>ERROR</strong>: Could not register a new user.</li></ul>';
    }
    return $this->login_user($email, $password);
  }
  
  function setup_existing_user($email) {
    $response = $this->login_user($email, $_POST['password']);
    if (is_numeric($response)) {
      $user_id = $response;
      update_user_meta($user_id, 'show_admin_bar_front', false);
      update_user_meta($user_id, 'user_level', 2);
      update_user_meta($user_id, "{$table_prefix}user_level", 2);
      update_user_meta($user_id, "{$table_prefix}capabilities", array(
        'author' => 1
      ));
      clean_user_cache($user_id);
      $url = get_bloginfo('url');
      $year = $this->get_year();
      
      wp_mail($email, 'Bushwick Open Studios registration', "
Thank you for registering again for BOS$year! You can login here:

URL: $url/registration-form/
Email: $email
Password: ********

If you forget your password, please visit the password recovery page:
http://artsinbushwick.org/wp-login.php?action=lostpassword

You can return at any time to edit your listing information using the
following URL: $url/registration-form/

Please review the BOS$year Registrant How-To for information about
promotions, organizing your show, and other odds and ends:
$url/register/registrant-how-to/

If you have questions or need help with anything you can email us at
registration@artsinbushwick.org.

Warm regards,
The BOS registration robot

");
    }
    return $response;
  }
  
  // Wrapper for WordPress registration code
  function create_new_user($user_email, $user_pass, $confirm_password) {
    $errors = new WP_Error();
    
    $user_login = $this->get_username($user_email);
    $user_login = sanitize_user( $user_login );
    $user_email = apply_filters( 'user_registration_email', $user_email );
  
    // Check the username
    if ( !validate_username( $user_login ) ) {
      $errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.'));
      $user_login = '';
    }
    
    // Check the password
    if ($user_pass == '') {
      $errors->add('empty_password', __('<strong>ERROR</strong>: Please type a password.'));
      $user_pass = '';
    } else if ($user_pass != $confirm_password) {
      $errors->add('invalid_password', __('<strong>ERROR</strong>: Your passwords did not match. Please try again.'));
      $user_pass = '';
    }
    
    // Check the e-mail address
    if ($user_email == '') {
      $errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.'));
    } elseif ( !is_email( $user_email ) ) {
      $errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
      $user_email = '';
    } elseif ( email_exists( $user_email ) )
      $errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered. Try choosing <em>edit an existing listing</em> or <a href="/wp-login.php?action=lostpassword">retrieving your password</a>.'));
  
    do_action('register_post', $user_login, $user_email, $errors);
  
    $errors = apply_filters( 'registration_errors', $errors, $user_login, $user_email );
  
    if ( $errors->get_error_code() )
      return $errors;
  
    //$user_pass = wp_generate_password();
    $user_id = wp_create_user($user_login, $user_pass, $user_email);
    if ( !$user_id ) {
      $errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
      return $errors;
    }
    
    $url = get_bloginfo('url');
    wp_mail($user_email, 'Bushwick Open Studios registration', "
Thank you for registering for BOS$year! In case you forget your login
info in the future, here it is:

URL: $url/registration-form/
Email: $user_email
Password: $user_pass

You can return at any time to edit your listing information using the
following URL: $url/registration-form/

Please review the BOS$year Registrant How-To for information about
promotions, organizing your show, and other odds and ends:
$year/register/registrant-how-to/

If you have questions or need help with anything you can email us at
registration@artsinbushwick.org.

Warm regards,
The BOS registration robot

");
    
    return $user_id;
  }
  
  function login_user($email, $password) {
    $username = $this->get_username($email);
    $user = wp_signon(array(
      'user_login' => $username,
      'user_password' => $password,
      'remember' => 'forever'
    ));
    get_currentuserinfo();
    if (is_object($user) && get_class($user) == 'WP_Error') {
      return '<ul><li>' . implode('</li><li>', $user->get_error_messages()) . '</li><li>Note that if you have registered in previous years, you will need to enter the same password associated with that email address.</li></ul>';
    } else if (is_object($user) && is_numeric($user->ID)) {
      return $user->ID;
    } else if (!is_object($userdata)) {
      return '<ul><li>Could not login.</li></ul>';
    } else {
      return $userdata->ID;
    }
  }
  
  function get_username($email) {
    global $wpdb;
    $username = $wpdb->get_var($wpdb->prepare("
      SELECT user_login
      FROM $wpdb->users
      WHERE user_email = %s
    ", $email));
    if (!empty($username)) {
      return $username;
    }
    $username = substr($email, 0, strpos($email, '@'));
    $username = preg_replace('/\W/', '_', $username);
    $username = $username . '_' . substr(md5($email), 0, 6);
    return $username;
  }
  
  function get_user_id() {
    global $userdata;
    get_currentuserinfo();
    if (!is_object($userdata) || empty($userdata->ID)) {
      return false;
    }
    return $userdata->ID;
  }
  
  function get_year() {
    if (defined('AIB_EVENT') && preg_match('/(\d\d\d\d)/', AIB_EVENT, $matches)) {
      return $matches[1];
    } else {
      return date('Y');
    }
  }
  
  function confirm_token($token) {
    global $wpdb, $blog_id;
    $user_id = $this->get_user_id();
    $token = $wpdb->get_row($wpdb->prepare("
      SELECT *
      FROM aib_token
      WHERE token = %s
    ", $token));
    if (empty($token) || empty($token->token)) {
      // Invalid
      return false;
    } else if (empty($token->user_id)) {
      // Not used yet
      return true;
    } else if ($token->user_id == $user_id &&
               $token->site_id == $blog_id) {
      // Previously used, by this user/blog
      return true;
    }
    return false;
  }
  
  function claim_token($user_id, $token) {
    global $wpdb, $blog_id;
    $token = trim(strtoupper($token));
    $wpdb->update('aib_token', array(
      'user_id' => $user_id,
      'site_id' => $blog_id,
      'available' => 0
    ), array('token' => $token));
  }
  
  function registration_exists($email) {
    global $wpdb;
    $user_id = $wpdb->get_var($wpdb->prepare("
      SELECT ID
      FROM $wpdb->users
      WHERE user_email = %s
    ", $email));
    if (empty($user_id)) {
      return false;
    }
    $post_id = get_usermeta($user_id, 'aib_post_' . AIB_EVENT);
    return (!empty($post_id));
  }
  
  function setup_db_tables() {
    global $wpdb;
    $tables = array(
      'aib_listing',
      'aib_seeking',
      'aib_location',
      'aib_token'
    );
    foreach ($tables as $table) {
      $exists = $wpdb->query("DESCRIBE $table");
      if (empty($exists)) {
        $sql = file_get_contents(__DIR__ . "/db/$table.sql");
        $wpdb->query($sql);
      }
    }
  }
  
  function tokens_status() {
    global $wpdb, $blog_id;
    if (!$this->check_user_role('administrator')) {
      return;
    }
    $available = $wpdb->get_var($wpdb->prepare("
      SELECT COUNT(*)
      FROM aib_token
      WHERE available = 1
        AND site_id = %d
    ", $blog_id));
    echo "<strong>Administrator only</strong><br>$available available registration tokens (<a href=\"?generate_tokens=1\">generate tokens</a>)";
  }
  
  function generate_tokens() {
    global $wpdb, $blog_id;
    $alphanumerics = '0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
    for ($i = 0; $i < 100; $i++) {
      $token = '';
      for ($j = 0; $j < 8; $j++) {
        $index = rand(0, strlen($alphanumerics) - 1);
        $token .= substr($alphanumerics, $index, 1);
      }
      $wpdb->query($wpdb->prepare("
        INSERT INTO aib_token
        (token, site_id) VALUES (%s, %d)
      ", $token, $blog_id));
    }
  }
  
  function in_progress() {
    $user_id = $this->get_user_id();
    if (is_numeric($user_id)) {
      return $this->get_post($user_id);
    }
    return false;
  }
  
  function is_complete() {
    return false;
  }
  
  function get_post($user_id = null) {
    global $wpdb;
    if (empty($user_id)) {
      $user_id = $this->get_user_id();
    }
    $post_id = get_usermeta($user_id, 'aib_post_' . AIB_EVENT);
    if (is_numeric($post_id)) {
      global $post, $blog_id, $page_slug;
      // Store the page slug for future use
      $page_slug = $post->post_name;
      $post = get_post($post_id);
      $listing = $wpdb->get_row("
        SELECT *
        FROM aib_listing
        WHERE post_id = $post->ID
          AND site_id = $blog_id
      ", ARRAY_A);
      if (!empty($listing)) {
        foreach ($listing as $key => $value) {
          $post->$key = $value;
        }
      }
      return $post;
    }
    return false;
  }
  
  function text_input($name, $value = null, $options = null) {
    global $post;
    $type = 'text';
    $id = "input_$name";
    $class = 'text';
    if (!empty($post->$name)) {
      $value = esc_attr($post->$name);
    }
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    echo "<input type=\"$type\" name=\"$name\" value=\"$value\"$id$class />";
  }
  
  function textarea_input($name, $value = null, $options = null) {
    global $post;
    $id = "input_$name";
    $class = 'text';
    $rows = 3;
    $cols = 50;
    if (!empty($post->$name)) {
      $value = esc_html($post->$name);
    }
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    if (!empty($maxlength)) {
      $maxlength = " maxlength=\"$maxlength\"";
    }
    echo "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\"$id$class$maxlength>$value</textarea>";
  }
  
  function radio_input($name, $value, $options = null) {
    global $post;
    $id = "input_{$name}_$value";
    $class = 'radio';
    $checked = ($post->$name == $value);
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    if (!empty($checked)) {
      $checked = ' checked="checked"';
    }
    echo "<input type=\"radio\" name=\"$name\" value=\"$value\"$checked$id$class />";
  }
  
  function checkbox_input($name, $options = null) {
    global $post;
    $id = "input_$name";
    $class = 'checkbox';
    $checked = (!empty($post->$name));
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    if (!empty($checked)) {
      $checked = ' checked="checked"';
    }
    echo "<input type=\"checkbox\" name=\"$name\" value=\"1\"$checked$id$class />";
  }
  
  function check_user_role($role, $user_id = null ) {
    if (is_numeric($user_id)) {
      $user = get_userdata($user_id);
    } else {
      $user = wp_get_current_user();
    }
    if (empty($user)) {
      return false;
    }
    return in_array($role, (array) $user->roles);
  }
  
}

<?php

require_once __DIR__ . '/../lib/form.php';

// $this->response = a escaped POST value, if one exists
if (!function_exists('v')) {
  function v($name) {
    if (!empty($_POST[$name])) {
      $this->response = esc_attr($_POST[$name]);
    } else {
      $this->response = '';
    }
  }
}

class AIB_Seeking extends Form {

  var $response = null;

  function __construct() {

  }

  function feedback() {
    if (!empty($this->response)) {
      echo "<p id=\"response\">$this->response</p>\n";
    }
  }

  function get_seeking($type) {
    global $wpdb, $blog_id;
    $rows = $wpdb->get_results($wpdb->prepare("
      SELECT *
      FROM aib_seeking
      WHERE type = %s
        AND site_id = %d
        AND visible = 1
      ORDER BY created
    ", $type, $blog_id));
    if (empty($rows)) {
      echo '<tr><td><i>Nothing posted yet!</td></tr>';
      return;
    }
    foreach ($rows as $num => $row) {
      $class = ($num % 2) ? 'even' : 'odd';
      $title = empty($row->url) ? esc_html($row->name)
                                 : esc_html($row->name) . '<br />' .
                                   '<a href="' . esc_attr($row->url) . '" rel="nofollow">' . str_replace('http://', '', esc_html($row->url)) . '</a>';
      $description = stripslashes(esc_html($row->description));
      $seeking = stripslashes(esc_html($row->seeking));
      echo <<<END
      <tr class="$class">
        <td>$title<br /><em><a href="?contact=$row->id">Contact</a></em></td>
        <td>$description</td>
        <td>$seeking</td>
      </tr>
END;
    }
  }

  function post_seeking() {
    global $wpdb, $blog_id;
    if (empty($_POST['type'])) {
      $this->response = 'ERROR: please select which type of listing you want to post.';
    } else if (empty($_POST['seeking_name'])) {
      $this->response = 'ERROR: please include your name.';
    } else if (empty($_POST['email'])) {
      $this->response = 'ERROR: please include your email address.';
    } else if (empty($_POST['description'])) {
      $this->response = 'ERROR: please include a description.';
    } else if (empty($_POST['seeking'])) {
      $this->response = 'ERROR: please include what you are seeking.';
    } else if (empty($_POST['spambot']) || strpos(strtolower($_POST['spambot']), 'bushwick') === false) {
      $this->response = 'Oops, you failed the spambot challenge.';
    }

    if (!empty($this->response)) {
      return;
    }

    $url = $_POST['url'];
    if (!empty($url) && substr($url, 0, 4) != 'http') {
      $url = "http://$url";
    }

    $wpdb->insert('aib_seeking', array(
      'site_id' => $blog_id,
      'type' => $_POST['type'],
      'name' => $_POST['seeking_name'],
      'email' => $_POST['email'],
      'url' => $url,
      'description' => $_POST['description'],
      'seeking' => $_POST['seeking'],
      'created' => date('Y-m-d H:i:s')
    ));
    $_POST = array();
    $this->response = 'Your listing has been posted!';
  }

  function contact_seeking() {
    global $wpdb;
    if (empty($_POST['contact_id'])) {
      $this->response = 'ERROR: could not find contact.';
    } else if (empty($_POST['contact_name'])) {
      $this->response = 'ERROR: please include your name.';
    } else if (empty($_POST['email'])) {
      $this->response = 'ERROR: please include your email address.';
    } else if (empty($_POST['message'])) {
      $this->response = 'ERROR: please include a message to send.';
    } else if (empty($_POST['spambot']) || strpos(strtolower($_POST['spambot']), 'bushwick') === false) {
      $this->response = 'Oops, you failed the spambot challenge.';
    }

    $contact = $this->get_contact($_POST['contact_id']);
    $blog_name = get_bloginfo('name');
    $url = get_bloginfo('url');

    $to = "$contact->name <$contact->email>";
    $from = "Arts In Bushwick <registration@artsinbushwick.org>";
    $reply_to = "{$_POST['name']} <{$_POST['email']}>";
    $headers = "From: $from\r\nReply-To: $reply_to\r\n";
    $subject = "Response to your $blog_name listing";
    $message = "Message from {$_POST['name']} ({$_POST['email']}) in response to your listing for $blog_name ($url/seeking/):

  {$_POST['message']}

  - - - -
  You can remove your listing at any time by contacting registration@artsinbushwick.org
  ";
    wp_mail($to, $subject, $message, $headers);
    $_POST = array();
    $this->response = 'Your message has been sent!';
  }

  function get_contact($id) {
    global $wpdb;
    $contact = $wpdb->get_row($wpdb->prepare("
      SELECT *
      FROM aib_seeking
      WHERE id = %d
    ", $id));
    $this->response = $contact;
  }

}

?>

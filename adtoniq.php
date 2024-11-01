<?php
/* Plugin Name: Adtoniq Pro
Plugin URI: https://www.adtoniq.com/
Description: A complete solution for ad blocking including messaging, content protection, and ad block analytics. To get started: activate this plugin and then go to www.adtoniq.com to register and get your API key for ad block analytics.
Version: 4.1.0.10
Author: Adtoniq
Author URI: http://www.adtoniq.com/
License: GPLv2 or later
*/

global $adtoniq_version;
global $adtoniqWebSite;

/**
 * Constants
 */
define('ADTONIQ_PLUGIN_NAME', __( 'Adtoniq', 'adtoniq' ));
define('ADTONIQ_PLUGIN_SLUG', 'adtoniq');
define('ADTONIQ_PLUGIN_DIR', 'adtoniq');
define('ADTONIQ_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ADTONIQ_PLUGIN_URL', plugins_url(ADTONIQ_PLUGIN_DIR));
define('ADTONIQ_FILE', 'adtoniq.php');
define('ADTONIQ_SHORTNAME', 'Adtoniq');
define('ADTONIQ_PREFIX', 'adtoniq-');
define('ADTONIQ_VERSION', '4.1.0.10');
define('ADTONIQ_REQUIRED_WP_VERSION', '4.6');
define('ADTONIQ_CAPABILITY', 'administrator');
define('ADTONIQ_ICON', 'dashicons-admin-generic');
define('ADTONIQ_LOGOPATH', '/images/adtoniq-logo.svg');
define('ADTONIQ_INSTALL_URL', 'https://wordpress.org/plugins/adtoniq/');
define('ADTONIQ_REGISTER_URL', get_site_url() . '/wp-admin/admin.php?page=adtoniq');
define('ADTONIQ_SLUG', 'adtoniq');
define('ADTONIQ_WEBSITE', 'http://www.adtoniq.com/');
define('ADTONIQ_ACCT_SERVER', 'https://www.adtoniq.com/');

require_once('inc/api.php');
require_once('inc/messenger.php');
require_once('inc/protection.php');

require_once('inc/alert.php');

function adtoniq_update_option($option_name, $new_value, $autoload = null) {
  wp_cache_delete($option_name, 'options');
  return update_option($option_name, $new_value, $autoload);
}

function adtoniq_delete_option($option_name) {
  wp_cache_delete($option_name, 'options');
  return delete_option($option_name);
}

function adtoniq_add_event($event) {
  if (function_exists('adtoniq_debug_write_event')) {
      adtoniq_debug_write_event($event);
  }
}

function adtoniq_add_event_var($event) {
  if (function_exists('adtoniq_debug_write_event_var')) {
      return adtoniq_debug_write_event_var($event);
  }
}

// For content protection
add_shortcode( 'adtoniq_protect', 'adtoniq_protect_shortcode' );
function adtoniq_protect_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_protect" style="display:none;">' . do_shortcode($content) . '</div>';
}

// Content displayed to all ad blocked users
add_shortcode( 'adtoniq_message_adblocked', 'adtoniq_message_adblocked_shortcode' );
function adtoniq_message_adblocked_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_adblocked" style="display:none;">' . do_shortcode($content) . '</div>';
}

// Content displayed to ad blocked users with acceptable ads disabled
add_shortcode( 'adtoniq_message_adblocked_noacceptable', 'adtoniq_message_adblocked_noacceptable_shortcode' );
function adtoniq_message_adblocked_noacceptable_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_adblocked_no_acceptable" style="display:none;">' . do_shortcode($content) . '</div>';
}

// Content displayed to blocked users with acceptable ads enabled
add_shortcode( 'adtoniq_message_adblocked_acceptable', 'adtoniq_message_adblocked_acceptable_shortcode' );
function adtoniq_message_adblocked_acceptable_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_acceptable" style="display:none;">' . do_shortcode($content) . '</div>';
}

// Content displayed to all non-blocked users
add_shortcode( 'adtoniq_message_nonblocked', 'adtoniq_message_nonblocked_shortcode' );
function adtoniq_message_nonblocked_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_nonblocked" style="display:none;">' . do_shortcode($content) . '</div>';
}

// Content displayed to users blocking analytics
add_shortcode( 'adtoniq_message_blocked_analytics', 'adtoniq_message_blocked_analytics_shortcode' );
function adtoniq_message_blocked_analytics_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_blocked_analytics" style="display:none;">' . do_shortcode($content) . '</div>';
}

// Content displayed to users not blocking analytics
add_shortcode( 'adtoniq_message_nonblocked_analytics', 'adtoniq_message_nonblocked_analytics_shortcode' );
function adtoniq_message_nonblocked_analytics_shortcode( $atts, $content = null ) {
   return '<div class="adtoniq_nonblocked_analytics" style="display:none;">' . do_shortcode($content) . '</div>';
}

add_shortcode('adtoniq_clear_choice', 'adtoniq_clear_choice_shortcode');
function adtoniq_clear_choice_shortcode($atts = [], $content = null, $tag = '')
{
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $adtoniq_attrs = shortcode_atts([
                                     'style' => 'button',
                                 ], $atts, $tag);

    // Get style attribute
    $style = $adtoniq_attrs['style'];
    // start output
    $o = '';

    $o .= '
  <script>function clearChoice() {
    document.cookie = "adtoniq_choice=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    location.reload();
  }</script>
';

    switch ($style) {
      case 'button':
      default:
        $o .= '<button type="button" onclick="clearChoice()">';
        break;
      case 'anchor':
        $o .= '<a href="javascript:clearChoice()">';
        break;
    }

    // enclosing tags
    if (!is_null($content)) {
        // secure output by executing the_content filter hook on $content
//        $o .= apply_filters('the_content', $content);

        // run shortcode parser recursively
        $o .= do_shortcode($content);
    }

    switch ($style) {
      case 'button':
      default:
        $o .= '</button>';
        break;
      case 'anchor':
        $o .= '</a>';
        break;
    }


    // return output
    return $o;
}

function adtoniq_adtoniq_delete_option_variables() {
  adtoniq_delete_option('adtoniq-api-key');
  adtoniq_delete_option('adtoniq-head-injection');
  adtoniq_delete_option('adtoniq-is-private');
  adtoniq_add_event('Deleted Adtoniq option variables.');
}

function adtoniq_activation() {
  global $adtoniq_version;
  $adtoniq_version = ADTONIQ_VERSION;

  $lastVersion = get_option('adtoniq-lastVersion');
  if (! isset($lastVersion) || strlen($lastVersion) === 0) {
    adtoniq_update_option('adtoniq-lastVersion', ADTONIQ_VERSION, true);
    adtoniq_add_event('Upgraded Adtoniq to version ' . ADTONIQ_VERSION);
  } elseif ($lastVersion != ADTONIQ_VERSION) {
    adtoniq_add_event('Updated Adtoniq plugin from version ' . $lastVersion . ' to version ' . ADTONIQ_VERSION);
    adtoniq_update_option('adtoniq-lastVersion', ADTONIQ_VERSION, true);
  } else {
    adtoniq_add_event('Adtoniq plugin version ' . ADTONIQ_VERSION . ' reactivated.');
  }
  adtoniq_post_operation('activate');
  adtoniq_do_cache_update('');
}
register_activation_hook(__FILE__, 'adtoniq_activation');

function adtoniq_deactivation() {
  adtoniq_add_event('Adtoniq deactivated.');
  adtoniq_post_deactivate();
}
register_deactivation_hook(__FILE__, 'adtoniq_deactivation');

add_action('admin_menu', 'adtoniq_menu');


global $adtoniq_menus;

function adtoniq_add_menu($menuFunc) {
	global $adtoniq_menus;
	if (! isset($adtoniq_menus))
		$adtoniq_menus = array();
	$adtoniq_menus[] = $menuFunc;
}

function adtoniq_menu() {
	global $adtoniq_menus;
	add_menu_page('Adtoniq', 'Adtoniq', 'administrator', 'adtoniq', 'adtoniq_registered_page', 'dashicons-admin-generic');
	if (isset($adtoniq_menus))
		foreach ($adtoniq_menus as $menuFunc) {
		  call_user_func($menuFunc);
		}
}

function adtoniq_get_server() {
  $adtoniq_server = get_option('adtoniq-debug-server', 'https://integration.adtoniq.com/');
  if (strlen($adtoniq_server) == 0)
    $adtoniq_server = 'https://integration.adtoniq.com/';
  return $adtoniq_server;
}

function adtoniq_post_operation($operation) {
  $response = null;
  try {
    $apiKey = get_option('adtoniq-api-key');
    $fqdn = get_option('adtoniq-fqdn');
    $url = adtoniq_get_server() . 'api/v1';
    $version = ADTONIQ_VERSION;
    $params = array(
      'operation' => $operation,
      'apiKey' => $apiKey,
      'fqdn' => $fqdn,
      'version' => $version,
      'siteUrl' => get_site_url()
    );
    $response = adtoniq_post($url, $params);
  } catch(Exception $e) {
    adtoniq_add_event('Error / ' . $e);
  }
  return $response;
}

function adtoniq_post_deactivate() {
  adtoniq_post_operation('deactivate');
}

add_action( 'wp_ajax_adtoniq_update', 'adtoniq_update' );

function adtoniq_update() {
	global $wpdb; // this is how you get access to the database

	$adtoniqAction = $_POST['adtoniqAction'];

	if ($adtoniqAction === 'requestJSUpdate') {
    adtoniq_do_cache_update('');
    echo "Updated your Adtoniq defintions.";
  } else {
    echo "No action performed.";
  }

	wp_die(); // this is required to terminate immediately and return a proper response
}

function adtoniq_get_features() {
  $userInfo = adtoniq_post_operation('getUser');
  if (isset($userInfo->features))
    $userInfo = $userInfo->features;
  return $userInfo;
}

function adtoniq_admin_notice() {
  $api_key = get_option('adtoniq-api-key');
  if (strlen($api_key) == 0) {
    ?>
    <div class="adtoniq-notice notice-success">
        <div><?php _e( 'To discover your ad block rate and more, <a href="https://www.adtoniq.com/?source=pro">register for ' .
        'our free Adtoniq Cloud</a> service, and then paste your Cloud key below.'); ?></div>
    </div>
    <?php } else { ?>
    <div class="adtoniq-notice notice-success">
        <div><?php _e( 'Want to make money showing ads to your ad blocked users? Let Adtoniq do all the work. ' .
        '<a href="https://adtoniq.io/pro-to-express/">Learn More</a>'); ?></div>
    </div>
    <?php }
}

function adtoniq_registered_page() {
  $action = isset($_POST['adtoniqAction']) ? sanitize_text_field($_POST['adtoniqAction']) : '';
  $injectStr = get_option('adtoniq-head-injection', '');
  $lastUpdate = get_option('adtoniq-lastUpdate', '');

  switch ($action) {
    case 'requestJSUpdate':
      $api_key = get_option('adtoniq-api-key');
      $passedAPIKey = isset($_POST['adtoniqAPIKey']) ? sanitize_text_field($_POST['adtoniqAPIKey']) : '';
      if ($passedAPIKey === $api_key)
        adtoniq_do_cache_update('');
      else
        adtoniq_add_event('Passed API did not match, got: ' . $passedAPIKey);
      break;
  }

  $lastUpdate = get_option('adtoniq-lastUpdate', '');
  $apiKey = get_option('adtoniq-api-key');

  $actualServer = $_SERVER['SERVER_NAME'];
  $actualPort = $_SERVER['SERVER_PORT'];

  if (strlen($injectStr) === 0)
    $injectStr = adtoniq_do_cache_update('');

  if (! ($actualPort === '80' || $actualPort === '443'))
    $actualServer .= ":" . $actualPort;

  $adtoniq_server = adtoniq_get_server();
  global $adtoniq_version;

  // register includes
  global $adtoniq_api;
  global $adtoniq_msg;
  global $adtoniq_protection;

  ?>
    <div class="adtoniq-plugin panel wrap">

      <!-- TODO: Enqueue these -->
      <script src="<?php echo plugins_url('js/adtoniq.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('js/adtoniq-api.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('js/adtoniq-messenger.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('js/adtoniq-protection.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('js/odometer.min.js', __FILE__); ?>"></script>
      <script src="<?php echo plugins_url('js/bs-tabs.js', __FILE__); ?>"></script>
      <link rel="stylesheet" href="<?php echo plugins_url('css/adtoniq-trublock.css', __FILE__); ?>">

      <!-- HEADER -->
      <div class="adtoniq-logo-header">
        <a id="goToAdtoniqWebSite" href="<?php echo ADTONIQ_WEBSITE; ?>">
          <img class="adtoniq-logo" src="<?php echo plugins_url(ADTONIQ_LOGOPATH, __FILE__); ?>" alt="Adtoniq Logo" >
        </a>
        <span class="version-number"><strong class="muted">Adtoniq Pro Version <?php echo ADTONIQ_VERSION; ?></strong></span>
	    </div>
      <?php adtoniq_admin_notice() ?>
      <div class="row">
        <div class="col-full">
          <a id="premium-anchor-link" class="premium-anchor-link" href="#premium-anchor">Jump to Adtoniq Cloud</a>
          <ul class="nav nav-tabs cf" id="freeFeaturesNav" role="tablist">
            <li role="presentation" class="active">
              <a onClick="Adtoniq.setFreeTab('messenger')" href="#messenger-panel" id="messenger-tab" role="tab" data-toggle="tab" aria-controls="messenger-panel">Messaging</a>
            </li>
            <li role="presentation">
              <a onClick="Adtoniq.setFreeTab('protection')" href="#protection-panel" id="protection-tab" role="tab" data-toggle="tab" aria-controls="protection-panel">Protection</a>
            </li>
            <li role="presentation">
              <a onClick="Adtoniq.setFreeTab('documentation')" href="#documentation-panel" id="documentation-tab" role="tab" data-toggle="tab" aria-controls="documentation-panel">Documentation</a>
            </li>
          </ul>
          <div class="tab-content free-features" id="freeFeatures">
            <div id="messenger-panel" class="tab-pane fade in active" role="tabpanel" aria-labelledby="messenger-tab">
              <?php $adtoniq_msg->adtoniq_msg_init(); ?>
            </div>
            <div id="protection-panel" class="tab-pane fade in active" role="tabpanel" aria-labelledby="protection-tab">
              <?php $adtoniq_protection->adtoniq_protection_render(); ?>
            </div>

            <div id="documentation-panel" class="tab-pane fade" role="tabpanel" aria-labelledby="documentation-tab">
              <iframe src="https://doc.adtoniq.com/content/doc/?embed=1" style="width:100%;height:1200px;"></iframe>
            </div>

          </div> <!-- .free-features -->

          <div id="premium-anchor" class="premium-header">
            <h1 class="page-header">
              Adtoniq Cloud
              <small class="muted right">
                These features require an Adtoniq Cloud Account
              </small>
            </h1>
          </div>
          <ul class="nav nav-tabs cf" id="premiumFeaturesNav" role="tablist">
            <li role="presentation" <?php if (strlen($apiKey) === 0) { ?>class="active"<?php } ?>>
              <a onClick="Adtoniq.setPremiumTab('apikey')" href="#apikey-panel" id="apikey-tab" role="tab" data-toggle="tab" aria-controls="apikey-panel">API Key</a>
            </li>
            <li role="presentation" <?php if (strlen($apiKey) > 0) { ?>class="active"<?php } ?>>
              <a onClick="Adtoniq.setPremiumTab('analytics')" href="#analytics-panel" id="analytics-tab" role="tab" data-toggle="tab" aria-controls="analytics-panel">Adtoniq Analytics</a>
            </li>
          </ul>
          <div class="tab-content premium-features" id="premiumFeatures">
            <div
              id="apikey-panel"
              class="tab-pane fade <?php if (strlen($apiKey) === 0) { ?>in active<?php } ?>"
              role="tabpanel"
              aria-labelledby="apikey-tab">
              <?php $adtoniq_api->adtoniq_api_init($apiKey); ?>
            </div>
            <div id="analytics-panel"
              class="tab-pane fade <?php if (strlen($apiKey) > 0) { ?>in active<?php } ?>"
              role="tabpanel"
              aria-labelledby="analytics-tab">
              <div class="well" data-help="analytics">
                <h1 class="page-header">
                  Adtoniq Analytics
                  <small class="muted right">Your monthly and realtime adblock analytics</small>
                </h1>
                <div class="well-container">
                  <div class="analytics-panel">
                    <?php if (strlen($apiKey) === 0) { ?>
                      <p class="lead">
                        This feature requires an Adtoniq Cloud key. <a id="getApiKeyLink" href="<?php echo esc_attr(ADTONIQ_ACCT_SERVER); ?>?source=getkey">Get an Adtoniq Cloud key by signing up for an Adtoniq account.</a>
                      </p>
                    <?php } else { ?>
                      <form
                        id="anal-form"
                        target="anal-iframe"
                        method="post"
                        action="<?php echo adtoniq_get_server(); ?>adtoniqAnalytics2.jsp">
                        <input type="hidden" name="apikey" value="<?php echo $apiKey; ?>" />
                      </form>
                      <iframe name="anal-iframe" id="anal-iframe"></iframe>
                      <script>
                        document.getElementById('anal-form').submit();
                      </script>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div> <!-- .premium-features"> -->
        </div> <!-- .col-md -->
      </div> <!-- .row -->

      <script>Adtoniq.init();</script>

  <?php
}

add_action( 'wp_head', 'adtoniq_head_injection' );

function adtoniq_head_injection() {
  adtoniq_update_cache();
  echo adtoniq_generate_javascript();
}

function adtoniq_generate_javascript() {
  $injectStr = get_option('adtoniq-head-injection', '');

  $doubleClickId = get_option('adtoniq-dfp-id', '');
  $doubleClickSlot = get_option('adtoniq-dfp-slot', '');
  $adblockCheckerStr = "<iframe id='aq-ch' foo='bar' src='//static-42andpark-com.s3.amazonaws.com/html/danaton5.html?adname=" . $doubleClickSlot . "&adid=" . $doubleClickId . "' width='1' height='1' style='width:1px;height:1px;position:absolute;left:-1000px;'></iframe>";
  $debug = "";
  
  if (function_exists('adtoniq_debug_head_injection'))
    $debug = adtoniq_debug_head_injection();

  global $adtoniq_msg;
  return $adblockCheckerStr . $injectStr . $debug;
}

function adtoniq_do_cache_update($nonce) {
  $api_key = get_option('adtoniq-api-key');
  $injectStr = '';

  $adtoniq_server = adtoniq_get_server();
  $debugJS = get_option('adtoniq-debug-js', '');
  $url = $adtoniq_server . 'api/v1';
  $version = ADTONIQ_VERSION;
  $data = array('operation' => 'update', 'apiKey' => $api_key, 'nonce' => $nonce, 'debug' => $debugJS, 'version' => $version, 'siteUrl' => get_site_url());
  $response = adtoniq_post($url, $data);
  if (strlen($response) > 0) {
    $injectStr = $response;
    adtoniq_update_option('adtoniq-head-injection', $injectStr, true);
    adtoniq_update_option('adtoniq-lastUpdate', date('F d, Y h:i a T', time()), true);
    adtoniq_add_event('Updated Adtoniq JavaScript');
  } else
    adtoniq_add_event('Adtoniq received 0 length JavaScript for injection');

  return $injectStr;
}

function adtoniq_update_cache() {
  $api_key = get_option('adtoniq-api-key');
  $passedAPIKey = isset($_POST['adtoniqAPIKey']) ? sanitize_text_field($_POST['adtoniqAPIKey']) : '';
  // Note: This is not a WordPress nonce. This nonce comes from the Adtoniq server and must be sent back for validation
  // The Adtoniq server validates this nonce, since it generated it. That is why we only check for length > 0.
  $nonce = isset($_POST['adtoniqNonce']) ? sanitize_text_field($_POST['adtoniqNonce']) : '';
  $validNonce = $api_key == $passedAPIKey && strlen($nonce) > 0;

  if ($validNonce) {
    adtoniq_do_cache_update($nonce);
  }
}

function adtoniq_post($url, $data) {
  $evt = '<b>POST url: ' . $url . '</b>';
  $evt .= '<div>' . adtoniq_add_event_var($data) . '</div>';

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded",
      'method'  => 'POST',
      'content' => http_build_query($data)
    )
  );
  $context  = stream_context_create($options);
  $response = trim(file_get_contents($url, false, $context));

  $evt .= '<div>' . 'response: ' . htmlspecialchars(substr($response, 0, 300)) . '</div>';
  adtoniq_add_event($evt);
  return $response;
}

function adtoniq_get($url, $data) {
  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded",
      'method'  => 'GET',
      'content' => http_build_query($data)
    )
  );
  $context  = stream_context_create($options);
  $response = trim(file_get_contents($url, false, $context));

  return $response;
}

add_action('wp_dashboard_setup', 'adtoniq_dashboard_widgets');

function adtoniq_dashboard_widgets() {
  global $wp_meta_boxes;
  wp_add_dashboard_widget('adtoniq_help_widget', 'Adtoniq News', 'adtoniq_dashboard_help');
}

function adtoniq_dashboard_help() {
  $data = array( );
  $news = adtoniq_get('http://static-42andpark-com.s3-website-us-west-2.amazonaws.com/html/news.html', $data);
  $wpnews = adtoniq_get("http://static-42andpark-com.s3-website-us-west-2.amazonaws.com/html/wordpress-news.html", $data);
?>
  <p>
	<?php echo $wpnews; ?>
	<?php echo $news; ?>
  </p>
<?php
}

add_action( 'admin_init', 'adtoniq_settings' );

function adtoniq_settings() {
  global $adtoniq_api, $adtoniq_msg, $adtoniq_protection;
  $adtoniq_api->adtoniq_api_settings();
  $adtoniq_msg->adtoniq_msg_settings();
  $adtoniq_protection->adtoniq_protection_settings();
}

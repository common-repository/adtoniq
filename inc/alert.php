<?php

/**
 * Notification alert class
 *
 * @since  0.1.0
 */

class AdtoniqMessengerAlert {

  function __construct () {
    $is_enabled = get_option('adtoniq-msg-is-enabled');
    if (!empty($is_enabled)) {
      add_action( 'wp_enqueue_scripts', array(&$this,'render_alert') );
    }
  }

  function render_alert () {
    $data = array(
      'greetingMsg' => get_option('adtoniq-msg-message'),
      'confirmMsg' => get_option('adtoniq-msg-confirm'),
      'rejectMsg' => get_option('adtoniq-msg-reject'),
      'confirmBtnText' => get_option('adtoniq-msg-confirm-btn'),
      'rejectBtnText' => get_option('adtoniq-msg-reject-btn'),
      'customBtnClass' => get_option('adtoniq-msg-custom-btn-class'),
      'targetedUsers' => get_option('adtoniq-msg-users'),
      'protectionStatus' => get_option('adtoniq-protection-status'),
      'protectionUrl' => get_option('adtoniq-protection-url'),
      'protectionCss' => get_option('adtoniq-protection-css'),
    );
    wp_enqueue_script('adtoniq-msg-alert-js', ADTONIQ_PLUGIN_URL . '/js/adtoniq-messenger-alert.js', array(), ADTONIQ_VERSION);
    wp_enqueue_style('adtoniq-msg-alert-css', ADTONIQ_PLUGIN_URL . '/css/adtoniq-messenger-alert.css', false, ADTONIQ_VERSION);
    wp_localize_script('adtoniq-msg-alert-js', 'adtoniqAlertData', $data);
  }

}

$adtoniq_msg_alert = new AdtoniqMessengerAlert();

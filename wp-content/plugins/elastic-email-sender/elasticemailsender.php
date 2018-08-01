<?php

/*
 * Plugin Name: Elastic Email Sender
 * Text Domain: elastic-email-sender
 * Domain Path: /languages
 * Plugin URI: https://wordpress.org/plugins/elastic-email-sender/
 * Description: This plugin reconfigures the wp_mail() function to send email using REST API (via Elastic Email) instead of SMTP and creates an options page that allows you to specify various options.
 * Author: Elastic Email
 * Author URI: https://elasticemail.com
 * Version: 1.1.0
 * License: GPLv2 or later
 * Elastic Email Inc. for WordPress
 * Copyright (C) 2018
 */

/* Version check */
global $wp_version;
$exit_msg = 'ElasticEmail Sender requires WordPress 4.1 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress"> Please update!</a>';

global $sender_queue__db_version;
$ee_db_version = '1.0';

if (version_compare($wp_version, "4.1", "<")) {
    exit($exit_msg);
}

require_once('class/ees_mail.php');
eemail::on_load(__DIR__);


/* ----------- ADMIN ----------- */
if (is_admin()) {
    register_activation_hook(__FILE__, 'sender_queue_install');
    register_activation_hook(__FILE__, 'elasticemailsender_activate');
    register_deactivation_hook(__FILE__, 'elasticemailsender_deactivate');


    /* activate */

    function elasticemailsender_activate() {
        update_option('elastic-email-sender-status', true);
        update_option('elastic-email-credit-status', '<span style="color:green;font-weight:bold;">OK</span>');
        register_uninstall_hook(__FILE__, 'elasticemailsender_uninstall');
    }

    /* deactivate */

    function elasticemailsender_deactivate() {
        update_option('elastic-email-sender-status', false);
        if ((get_option('ee_setapikey') === 'eeset') && (get_option('ee_setapikey') !== 'eeunset')) {
            if (get_option('ee_accountemail') !== null || get_option('ee_accountemail') !== '') {
                require_once ( plugin_dir_path(__FILE__) . '/api/ElasticEmailClient.php');
                try {
                    $addToUserListAPI = new \ElasticEmailClient\Contact();
                    $error = null;
                    $addToUserList = $addToUserListAPI->Add('d0bcb758-a55c-44bc-927c-34f48d5db864', get_option('ee_accountemail'), array('55c8fa37-4c77-45d0-8675-0937d034c605'), array(), 'D', null, ApiTypes\ContactSource::ContactApi, null, null, null, null, false, null, null, array(), null);
                } catch (Exception $ex) {
                    $addToUserList = array();
                }
            }
        }
    }

    /* uninstall */

    function elasticemailsender_uninstall() {
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'senderqueue';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        delete_option('elastic-email-sender-status');
        delete_option('elastic-email-to-send-status');
        delete_option('elastic-email-credit-status');
        delete_option('ee_publicaccountid');
        delete_option('ee_enablecontactfeatures');
        delete_option('ee_options');
        delete_option('ee_accountemail');
        delete_option('ee_accountemail_2');
        delete_option('ee_db_version');
        delete_option('ee_addedtostatlist');
        delete_option('ee_setapikey');
    }

    function sender_queue_install() {
        global $wpdb;
        global $ee_db_version;
        $table_name = $wpdb->prefix . 'senderqueue';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		msg_to TEXT NOT NULL,
		msg_subject TEXT NOT NULL,
		msg_date DATE NOT NULL,
                msg_body TEXT NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);

            add_option('ee_db_version', $ee_db_version);
        }
    }

    require_once 'class/ees_admin.php';
    $ee_admin = new eeadmin(__DIR__);
}
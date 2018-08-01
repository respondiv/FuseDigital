<?php

define('EE_ADMIN', true);

/**
 * Description of eeadmin
 *
 * @author ElasticEmail
 */
class eeadmin {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $defaultOptions = array('ee_enable' => 'no', 'ee_apikey' => null),
            $options,
            $initAPI = false,
            $subscribe_status = false;
    public $theme_path;

    /**
     * Start up
     */
    public function __construct($pluginpath) {

        $this->theme_path = $pluginpath;
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_init', array($this, 'init_options'));
        add_action('plugins_loaded', array($this, 'eesender_load_textdomain'));
        $this->options = get_option('ee_options', $this->defaultOptions);
    }

    public function eesender_load_textdomain() {
        load_plugin_textdomain('elastic-email-sender', false, basename(dirname(__FILE__)) . '/languages');
    }

//Added admin menu
    public function add_menu() {
        $this->subscribe_status = get_option('elastic-email-subscribe-status');
        add_action('admin_enqueue_scripts', array($this, 'custom_admin_scripts'));

        if ($this->subscribe_status == true) {
            $plugin_custom_name = 'Elastic Email';
        } else {
            $plugin_custom_name = 'Elastic Email Sender';
        }

        add_menu_page($plugin_custom_name, $plugin_custom_name, 'manage_options', 'elasticemail-settings', array($this, 'show_settings'), plugins_url('/assets/images/icon.png', dirname(__FILE__)));
        add_submenu_page('elasticemail-settings', 'Reports', __('Reports', 'elastic-email-sender'), 'manage_options', 'elastic-report', array($this, 'show_reports'));
        add_submenu_page('elasticemail-settings', 'Queue', __('Queue', 'elastic-email-sender'), 'manage_options', 'elastic-queue', array($this, 'show_queue'));

// adds menu after activation 
        if ($this->subscribe_status == true) {
            add_submenu_page('elasticemail-settings', 'Widget', __('Widget', 'elastic-email-sender'), 'manage_options', 'elasticemail-widget', array($this, 'show_widget_admin_page'));
        }
    }

    public function custom_admin_scripts() {
        wp_enqueue_style('eesender-css', plugins_url('/assets/css/ees_admin.css', dirname(__FILE__)), array(), null, 'all');
    }

//Load Elastic Email settings
    public function show_settings() {
        $this->initAPI();
        try {
            $accountAPI = new \ElasticEmailClient\Account();
            $error = null;
            $account = $accountAPI->Load();
            $this->statusToSendEmail();
        } catch (ElasticEmailClient\ApiException $e) {
            $error = $e->getMessage();
            $account = array();
        }

        $accountstatus = '';
        if (isset($account['data']['statusnumber'])) {
            if ($account['data']['statusnumber'] > 0) {
                $accountstatus = $account['data']['statusnumber'];
            } else {
                $accountstatus = 'Please conect to Elastic Email API';
            }
        }

        if (isset($account['data']['email'])) {
            update_option('ee_accountemail', $account['data']['email']);
        }

        $accountdailysendlimit = '';
        if (isset($account['data']['actualdailysendlimit'])) {
            $accountdailysendlimit = $account['data']['actualdailysendlimit'];
        }

        if (isset($account['data']['publicaccountid'])) {
            $this->publicid = $account['data']['publicaccountid'];
            update_option('ee_publicaccountid', $this->publicid);
        }

        if (isset($account['data']['enablecontactfeatures'])) {
            update_option('ee_enablecontactfeatures', $account['data']['enablecontactfeatures']);
        }

        if (isset($account['data']['requiresemailcredits'])) {
            $requiresemailcredits = $account['data']['requiresemailcredits'];
        }

        if (isset($account['data']['emailcredits'])) {
            $emailcredits = $account['data']['emailcredits'];
        }

        if (isset($account['data']['requiresemailcredits'])) {
            $requiresemailcredits = $account['data']['requiresemailcredits'];
        }

        if (isset($account['data']['issub'])) {
            $issub = $account['data']['issub'];
        }

        require_once ($this->theme_path . '/template/ees_settingsadmin.php');
        return;
    }

    public function statusToSendEmail() {
        $this->initAPI();
        try {
            $statusToSendEmailAPI = new \ElasticEmailClient\Account();
            $error = null;
            $statusToSendEmail = $statusToSendEmailAPI->GetAccountAbilityToSendEmail();
            update_option('elastic-email-to-send-status', $statusToSendEmail['data']);
        } catch (Exception $ex) {
            $error = $e->getMessage();
            $statusToSendEmail = array();
        }
        return;
    }

    public function addNewLists() {
        $this->initAPI();
        $newListName = get_option('ee_newListName');
        $allContacts = true;
        try {
            $addListAPI = new \ElasticEmailClient\EEList();
            $error = null;
            $addList = $addListAPI->Add($newListName, $allContacts);
            if (isset($addList['success'])) {
                if ($addList['success'] == true) {
                    delete_option('ee_newListName');
                }
            }
        } catch (Exception $ex) {
            $error = $e->getMessage();
            $addList = array();
        }
    }
    
    public function show_widget_admin_page() {
        $this->initAPI();
        try {
            $listAPI = new \ElasticEmailClient\EEList();
            $error = null;
            $list = $listAPI->EElist();
        } catch (ElasticEmailClient\ApiException $e) {
            $error = $e->getMessage();
            $list = array();
        }

        require_once ($this->theme_path . '/template/ees_settingswidget.php');
        return;
    }

//Initialization Elastic Email API
    public function initAPI() {
        if ($this->initAPI === true) {
            return;
        }
//Loads Elastic Email Client
        require_once($this->theme_path . '/api/ElasticEmailClient.php');
        if (empty($this->options['ee_apikey']) === false) {
            \ElasticEmailClient\ApiClient::SetApiKey($this->options['ee_apikey']);
            try {
                $addToUserListAPI = new \ElasticEmailClient\Contact();
                $error = null;
                $addToUserList = $addToUserListAPI->Add('d0bcb758-a55c-44bc-927c-34f48d5db864', get_option('ee_accountemail'), array('55c8fa37-4c77-45d0-8675-0937d034c605'), array(), 'A', null, ApiTypes\ContactSource::ContactApi, null, null, null, null, false, null, null, array(), null);
            } catch (Exception $ex) {
                $addToUserList = array();
            }       
        }
        $this->initAPI = true;
    }

    public function show_reports() {
        $this->initAPI();

        if (isset($_POST['daterange'])) {
            $daterangeselect = $_POST['daterange'];
            if ($daterangeselect === 'last-mth') {
                $from = date('c', strtotime('-30 days'));
                $to = date('c');
            }
            if ($daterangeselect === 'last-wk') {
                $from = date('c', strtotime('-7 days'));
                $to = date('c');
            }
            if ($daterangeselect === 'last-2wk') {
                $from = date('c', strtotime('-14 days'));
                $to = date('c');
            }
        } else {
            $from = date('c', strtotime('-30 days'));
            $to = date('c');
        }

        $channelName = null;
        $interval = null;
        $transactionID = null;

        try {
            $LogAPI = new \ElasticEmailClient\Log();
            $error = null;
            $LogAPI_json = $LogAPI->Summary($from, $to, $channelName, $interval, $transactionID);
            $total = $LogAPI_json['data']['logstatussummary']['emailtotal'];
            $delivered = $LogAPI_json['data']['logstatussummary']['delivered'];
            $opened = $LogAPI_json['data']['logstatussummary']['opened'];
            $bounced = $LogAPI_json['data']['logstatussummary']['bounced'];
            $clicked = $LogAPI_json['data']['logstatussummary']['clicked'];
            $unsubscribed = $LogAPI_json['data']['logstatussummary']['unsubscribed'];
        } catch (ElasticEmailClient\ApiException $e) {
            $error = $e->getMessage();
            $LogList = array();
        }
//Loads the settings template
        require_once ($this->theme_path . '/template/ees_reports.php');
        return;
    }

    public function show_queue() {
        $this->initAPI();
        require_once ($this->theme_path . '/template/ees_queue.php');
        return;
    }

//Initialization custom options
    public function init_options() {
        register_setting(
                'ee_option_group', // Option group
                'ee_options', // Option name
                array($this, 'valid_options')   // Santize Callback
        );
//INIT SECTION
        add_settings_section('setting_section_id', null, null, 'ee-settings');
//INIT FIELD
        add_settings_field('ee_enable', 'Select mailer:', array($this, 'enable_input'), 'ee-settings', 'setting_section_id', array('input_name' => 'ee_enable'));
        add_settings_field('ee_apikey', 'Elastic Email API Key:', array($this, 'input_apikey'), 'ee-settings', 'setting_section_id', array('input_name' => 'ee_apikey', 'width' => 280));

//saving the option whether the plugin is active
        add_option('elastic-email-sender-status', is_plugin_active('ElasticEmailSender/elasticemailsender.php'));
    }

    /**
     * Validation plugin options during their update data
     * @param type $input
     * @return type
     */
    public function valid_options($input) {
//If api key have * then use old api key
        if (strpos($input['ee_apikey'], '*') !== false) {
            $input['ee_apikey'] = $this->options['ee_apikey'];
        } else {
            $input['ee_apikey'] = sanitize_key($input['ee_apikey']);
        }

        if ($input['ee_enable'] !== 'yes') {
            $input['ee_enable'] = 'no';
        }
        return $input;
    }

    /**
     * Get the apikey option and print one of its values
     */
    public function input_apikey($arg) {
        $apikey = $this->options[$arg['input_name']];
        if (empty($apikey) === false) {
            update_option('ee_setapikey', 'eeset');
            $apikey = substr($apikey, 0, 15) . '***************';
        } else {
            update_option('ee_setapikey', 'eeunset');
        }
        printf('<input type="text" id="title" name="ee_options[' . $arg['input_name'] . ']" value="' . $apikey . '" style="%s"/>', (isset($arg['width']) && $arg['width'] > 0) ? 'width:' . $arg['width'] . 'px' : '');
    }

//Displays the settings items
    public function enable_input($arg) {
        if (!isset($this->options[$arg['input_name']]) || empty($this->options[$arg['input_name']])) {
            $valuel = 'no';
        } else {
            $valuel = $this->options[$arg['input_name']];
        }

        echo'<div style="margin-bottom:15px;"><label><input type="radio" name="ee_options[' . $arg['input_name'] . ']" value="yes" ' . (($valuel === 'yes') ? 'checked' : '') . '/><span>' . __('Send all WordPress emails via Elastic Email API.', 'elastic-email-sender') . '</span><label></div>';
        echo'<label><input type="radio" name="ee_options[' . $arg['input_name'] . ']" value="no"  ' . (($valuel === 'no') ? 'checked' : '') . '/><span>' . __('Use the defaults Wordpress function to send emails.', 'elastic-email-sender') . '</span><label>';
    }

}

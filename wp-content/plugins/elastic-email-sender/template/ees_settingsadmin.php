<?php
defined('EE_ADMIN') OR die('No direct access allowed.');

if (isset($_GET['settings-updated'])):
    ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.', 'elastic-email-sender') ?></strong></p>
    </div>
<?php endif; ?>
<div id="eewp_plugin" class="ee_row eewp_container">
    <div class="ee_col-7 ee_line">
        <div class="ee_header">
            <div class="ee_logo">
                <?php echo '<img src="' . esc_url(plugins_url('/assets/images/icon.png', dirname(__FILE__))) . '" > ' ?>
            </div>
            <div class="ee_pagetitle">
                <h1><?php _e('General Settings', 'elastic-email-sender') ?></h1>
            </div>
        </div>
        <h4 class="ee_h4">
            <?php _e('Welcome to Elastic Email WordPress Plugin!<br/> From now on, you can send your emails in the fastest and most reliable way!<br/>
            Just one quick step and you will be ready to rock your subscribers\' inbox.<br/><br/>
            Fill in the details about the main configuration of Elastic Email connections.', 'elastic-email-sender') ?>
        </h4>

        <form method="post" action="options.php">
            <?php
            settings_fields('ee_option_group');
            do_settings_sections('ee-settings');
            ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e('Connection Test:', 'elastic-email-sender') ?></th>
                        <td> <span class="<?= (empty($error) === true) ? 'ee_success' : 'ee_error' ?>">
                                <?= (empty($error) === true) ? __('Connected', 'elastic-email-sender') : __('Connection error, check your API key. ', 'elastic-email-sender') . '<a href="https://elasticemail.com/support/user-interface/settings/smtp-api/" target="_blank">' . __('Read more', 'elastic-email-sender') . '</a' ?>
                            </span></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Account status:', 'elastic-email-sender') ?></th>
                        <td>
                            <?php
                            if (isset($accountstatus)) {
                                if ($accountstatus == 1) {
                                    $accountstatusname = '<span class="ee_account-status-active">' . __('Active', 'elastic-email-sender') . '</span>';
                                } else {
                                    $accountstatusname = '<span class="ee_account-status-deactive">' . __('Please conect to Elastic Email API or complete the profile', 'elastic-email-sender') . ' <a href="https://elasticemail.com/account/#/account/profile">' . __('Complete your profile </a> or connect to Elastic Email API to start using the plugin.', 'elastic-email-sender') . '</span>';
                                }
                            } else {
                                $accountstatusname = '<span class="ee_account-status-deactive">' . __('Please conect to Elastic Email API or complete the profile', 'elastic-email-sender') . ' <a href="https://elasticemail.com/account/#/account/profile">' . __('Complete your profile </a> or connect to Elastic Email API to start using the plugin.', 'elastic-email-sender') . '</span>';
                            }
                            echo $accountstatusname;
                            ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Account daily limit:', 'elastic-email-sender') ?></th>
                        <td>
                            <?php
                            if (isset($accountdailysendlimit)) {
                                if ($accountdailysendlimit === 0) {
                                    $accountlimitspan = '<span class="ee_standard-account">' . __('No limits', 'elastic-email-sender') . '</span>';
                                    $tooltip = __('Lucky you! Your account has no daily limits! Check out our other', 'elastic-email-sender') . ' <a href="http://elasticemail.com/pricing"> pricing plans </a> and discover unlimited possibilities of your account.</a>';
                                }
                                if ($accountdailysendlimit === 5 || $accountdailysendlimit === -5) {
                                    $accountlimitspan = '<span class="ee_standard-account">5</span>';
                                    $tooltip = __('Oops! It seems your limit is 5. Fill out your profile to get unlimited possibilities.', 'elastic-email-sender');
                                }
                                if ($accountdailysendlimit == 50 || $accountdailysendlimit === 50) {
                                    $accountlimitspan = '<span class="ee_standard-account">50</span>';
                                    $tooltip = __('Ups! It seems your your limit is 50. Fill out your profile to get unlimited possibilities.', 'elastic-email-sender');
                                }
                                if ($accountdailysendlimit === 5000) {
                                    $accountlimitspan = '<span class="ee_standard-account">5000</span>';
                                    $tooltip = __('Your account is limited to 5,000 free emails per day. Check out our', 'elastic-email-sender') . ' <a href="http://elasticemail.com/pricing"> pricing plans </a> and take your campaigns to the next level!</a>';
                                }
                                if ($accountdailysendlimit != 0 && $accountdailysendlimit != 5 && $accountdailysendlimit != -5 && $accountdailysendlimit != 50 && $accountdailysendlimit != -50 && $accountdailysendlimit != 5000) {
                                    $accountlimitspan = '<span class="ee_standard-account">' . __('Custom: ') . $accountdailysendlimit . '</span>';
                                    $tooltip = __('Your account has a custom limit: ', 'elastic-email-sender') . $accountdailysendlimit;
                                }
                                if ($accountdailysendlimit === '') {
                                    $accountlimitspan = ' -------';
                                    $tooltip = __('Seems that you might have some limits on your account. Please check out your account settings to unlock more options.', 'elastic-email-sender');
                                }
                            } else {
                                $accountlimit = '';
                                $accountlimitspan = ' -------';
                                $tooltip = __('Seems that you might have some limits on your account. Please check out your account settings to unlock more options.', 'elastic-email-sender');
                            }

                            echo $accountlimitspan;
                            ?>

                            <div class="ee_tooltip"><?php echo '<img class = "ee_tootlip-icon" style = "max-width:15px;" src = "' . esc_url(plugins_url('/assets/images/info.svg', dirname(__FILE__))) . '" > ' ?>
                                <span class="ee_tooltiptext">
                                    <?php echo $tooltip; ?>
                                </span>
                            </div>
                        </td>
                    </tr>

                    <?php
                    if (isset($issub) || isset($requiresemailcredits) || isset($emailcredits)) {
                        if ($emailcredits != 0) {
                            if ($issub == false || $requiresemailcredits == false) {
                                echo '<tr valign="top"><th scope="row">' . __('Email Credits:', 'elastic-email-sender') . '</th><td>' . $emailcredits . '</td></tr>';
                            }
                        }
                    }

                    if (get_option('elastic-email-to-send-status') !== NULL) {
                        if (get_option('elastic-email-to-send-status') == 1) {
                            $getaccountabilitytosendemail_single = '<span style="color: red;">Account doesn\'t have enough credits</span>';
                        } elseif (get_option('elastic-email-to-send-status') == 2) {
                            $getaccountabilitytosendemail_single = '<span style="color: orange;">Account can send e-mails but only without the attachments</span>';
                        } elseif (get_option('elastic-email-to-send-status') == 3) {
                            $getaccountabilitytosendemail_single = '<span style="color: red;">Daily Send Limit Exceeded</span>';
                        } elseif (get_option('elastic-email-to-send-status') == 4) {
                            $getaccountabilitytosendemail_single = '<span style="color: green;">Account is ready to send e-mails</span>';
                        } else {
                            $getaccountabilitytosendemail_single = '<span style="color: red;">Check the account configuration</span>';
                        }
                    } else {
                        $getaccountabilitytosendemail_single = '---';
                    }
                    ?>
                    <tr valign="top">
                        <th scope="row"><?php _e('Credit status:', 'elastic-email-sender') ?></th>
                        <td>
                            <?php echo '<span>' . $getaccountabilitytosendemail_single . '</span>'; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>

        <?php if (empty($error) === false) { ?><?php _e('Do not have an account yet? <a href="https://elasticemail.com/account#/create-account" target="_blank" title="First 1000 emails for free.">Create your account now</a>!<br/>
            <a href="http://elasticemail.com/transactional-email" target="_blank"> Tell me more about it</a>', 'elastic-email-sender') ?>
        <?php } ?>
        <!-- add link -->
        <h4>
            <?php _e('Want to use this plugin in a different language version? <a href="http://support.elasticemail.com/"> Let us know or help us translate it!</a>', 'elastic-email-sender') ?>
        </h4>
        <div class="">
            <h4 class="ee_h4footer">
                <?php _e('Share your experience of using Elastic Email WordPress Plugin by <a href="https://wordpress.org/support/plugin/elastic-email-sender/reviews/#new-post">rating us here.</a> Thanks!', 'elastic-email-sender') ?>
            </h4>
        </div>
    </div>
    <div class="ee_col-5 ee_marketing">
        <h2 class="ee_h2"><?php _e('Let us help you send better emails!', 'elastic-email-sender') ?></h2>
        <h4 class="ee_footertext">
            <?php _e('If you are new to Elastic Email, feel free to visit our <a href="https://elasticemail.com">website</a> and find out how our comprehensive set of tools will help you reach your goals or get premium email marketing tools at a fraction of what you\'re paying now!', 'elastic-email-sender') ?>
        </h4>
        <hr>
        <h4 class = "ee_h4"><?php _e('If you already use Elastic Email to send your emails, you can subscribe to our monthly updates to start receiving the latest email news, tips, best practices and more.', 'elastic-email-sender') ?></h4>
        <?php if (isset($_GET['subscribe']) === false) {
            ?>
            <form action="https://api.elasticemail.com/contact/add?version=2" method="post">
                <fieldset style="border:none;">
                    <input type="hidden" name="publicaccountid" value="49540e0f-2e09-4101-a05d-5032842b99d3">
                    <input type="hidden" name="returnUrl" value="<?php echo admin_url('/admin.php?page=elasticemail-settings&subscribe=true'); ?>">
                    <input type="hidden" name="activationReturnUrl" value="">
                    <input type="hidden" name="activationTemplate" value="Subscription_from_blog">
                    <input type="hidden" name="source" value="WebForm">
                    <input type="hidden" name="notifyEmail" value="">
                    <div class="ee_inputs">
                        <span id="ee_email" style="width: 100%;">
                            <label for="email" style="padding-right: 5px;"><?php _e('Email Address', 'elastic-email-sender') ?></label>
                            <input maxlength="40" class="ee_form-control" name="email" size="20" type="email" required="" style="width: 60%;"> 
                        </span>
                        <br/><br/>
                        <span id="ee_field_firstname" style="width: 100%;">
                            <label for="field_firstname" style="padding-right: 51px;"><?php _e('Name', 'elastic-email-sender') ?></label>
                            <input maxlength="40" class="ee_form-control" name="field_firstname" size="20" type="string" style="width: 60%;">
                        </span>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                    <ul class="ee_lists" style="list-style:none;display:none;">
                        <li>
                            <input type="checkbox" name="publiclistid" id="AWMifhLm" value="7db916f4-9a46-4655-be56-ec781bd74968" checked="checked">
                            <label class="ee_publiclistlabel" for="AWMifhLm">Subscription_from_blog</label>
                        </li>
                    </ul>
                    <input type="submit" class="ee_button" name="submit" value="<?php _e('Subscribe', 'elastic-email-sender') ?>">
                </fieldset>
            </form>
            <?php
        } else {
            echo '<h3 style="color: green; font: bold; font-size:22px;">' . __('Thank you for subscribing to our newsletter!', 'elastic-email-sender') . '</h3>
            <h5 style="color: green;">' . __('You will start receiving our email marketing newsletter, as soon as you confirm your subscription.', 'elastic-email-sender') . '</h5>';
        }
        ?>
        <br/>
        <hr>
        <br/>
        <h2 class="ee_h2"><?php _e('How we can help you?', 'elastic-email-sender') ?></h2>
        <h4 class="ee_h4"><?php _e('If you would like to boost your email marketing campaigns or improve your email delivery, check out our helpful guides to get you started!', 'elastic-email-sender') ?></h4>
        <ul style="padding-left: 40px;">
            <li type="circle"><a href="https://elasticemail.com/support/"><?php _e('Guides and resources', 'elastic-email-sender') ?></a></li>
            <li type="circle"><a href="https://elasticemail.com/api-documentation-and-libraries/"><?php _e('Looking for code? Check our API', 'elastic-email-sender') ?></a></li>
            <li type="circle"><a href="https://elasticemail.com/contact/"><?php _e('Want to talk with a live person? Contact us', 'elastic-email-sender') ?></a></li>
        </ul>
        <br/>
        <h4 class="ee_h4"><?php _e('Remember that in case of any other questions or feedback, you can always contact our friendly <a href="http://support.elasticemail.com/">Support Team.</a>', 'elastic-email-sender') ?></h4>

        <?php if ($this->subscribe_status == false) { ?>
        <a href="https://wordpress.org/plugins/elastic-email-subscribe-form/">
            <div class="ee_col-1">
                <img src="<?php echo esc_url(plugins_url('/assets/images/subscribe_clean.svg', dirname(__FILE__))) ?>" width="30">
            </div>
            <div class="ee_col-11">
                Elastic Email Subscribe Form - Check It!
            </div>
        </a>
        <?php } ?>
    </div>
</div>
<?php
defined('EE_ADMIN') OR die('No direct access allowed.');
wp_enqueue_script('eesender-chart-scropt', plugins_url('/assets/js/chart.min.js', dirname(__FILE__)), array('jquery'));
if (isset($_GET['settings-updated'])):
    ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.', 'elastic-email-sender') ?></strong></p>
    </div>
<?php endif; ?>
<div id="eewp_plugin" class="ee_row eewp_container">
    <div class="ee_col-7 <?php
    if (empty($error) === TRUE) {
        echo 'ee_line';
    }
    ?>">
        <div class="ee_header">
            <div class="ee_logo">
                <?php echo '<img src="' . esc_url(plugins_url('/assets/images/icon.png', dirname(__FILE__))) . '" > ' ?>
            </div>
            <?php
            if (isset($_POST['daterange'])) {
                $daterangeselect = $_POST['daterange'];
                if ($daterangeselect === 'last-mth') {
                    $datarangename = __(' - last month', 'elastic-email-sender');
                }
                if ($daterangeselect === 'last-wk') {
                    $datarangename = __(' - last week', 'elastic-email-sender');
                }
                if ($daterangeselect === 'last-2wk') {
                    $datarangename = __(' - last two weeks', 'elastic-email-sender');
                }
            } else {
                if ((empty($total) === true || $total === 0)) {
                    $datarangename = '';
                } else {
                    $datarangename = __(' - last month', 'elastic-email-sender');
                }
            }
            ?>
            <div class="ee_pagetitle">
                <h1>Reports <?php echo $datarangename; ?></h1>
            </div>
        </div>

        <?php
        if ((empty($total) === true || $total === 0)) {
            $total = '<span class="ee_default-text">' . 150000 . '</span>';
            $delivered = '<span class="ee_default-text">' . 100000 . '</span>';
            $opened = '<span class="ee_default-text">' . 85000 . '</span>';
            $bounced = '<span class="ee_default-text">' . 4000 . '</span>';
            $clicked = '<span class="ee_default-text">' . 95000 . '</span>';
            $unsubscribed = '<span class="ee_default-text">' . 4000 . '</span>';
            $info = '<div class="ee_connect-alert"><h1>' . __('Please note, that the data below is an example. Send your first campaign, to get the real statistics.', 'elastic-email-sender') . '</h1></div>';
        }

        if ((empty($error)) === TRUE) {
            ?>

            <div class="ee_select-form-box">
                <form name="form" id="daterange" action="" method="post">
                    Date range:
                    <select id="daterange-select" name="daterange" onchange="this.form.submit()">
                        <option><?php _e('Select data range', 'elastic-email-sender') ?></option>
                        <option value="last-mth"><?php _e('Last month', 'elastic-email-sender') ?></option>
                        <option value="last-wk"><?php _e('Last week', 'elastic-email-sender') ?></option>
                        <option value="last-2wk"><?php _e('Last two weeks', 'elastic-email-sender') ?></option>
                    </select>
                </form>
            </div>

            <?php
            if (!empty($info)) {
                echo $info;
            }
            ?>
            <div class="ee_reports-container">
                <table class="ee_report-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="background: rgba(102, 163, 163, 0.2);"><?php _e('Submitted', 'elastic-email-sender') ?></th>
                            <th style="background: rgba(0, 153, 255, 0.2);"><?php _e('Delivered', 'elastic-email-sender') ?></th>
                            <th style="background: rgba(0, 128, 0, 0.2);"><?php _e('Opened', 'elastic-email-sender') ?></th>
                            <th style="background: rgba(255, 159, 64, 0.2);"><?php _e('Clicked', 'elastic-email-sender') ?></th>
                            <th style="background: rgba(255, 162, 0, 0.2);"><?php _e('Unsubscribed', 'elastic-email-sender') ?></th>
                            <th style="background: rgba(255, 0, 0, 0.2);"><?php _e('Bounced', 'elastic-email-sender') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php
                                if (is_numeric($total)) {
                                    echo number_format($total);
                                } else {
                                    echo $total;
                                }
                                ?></td>
                            <td><?php
                                if (is_numeric($delivered)) {
                                    echo number_format($delivered);
                                } else {
                                    echo $delivered;
                                }
                                ?></td>
                            <td><?php
                                if (is_numeric($opened)) {
                                    echo number_format($opened);
                                } else {
                                    echo $opened;
                                }
                                ?></td>
                            <td><?php
                                if (is_numeric($clicked)) {
                                    echo number_format($clicked);
                                } else {
                                    echo $clicked;
                                }
                                ?></td>
                            <td><?php
                                if (is_numeric($unsubscribed)) {
                                    echo number_format($unsubscribed);
                                } else {
                                    echo $unsubscribed;
                                }
                                ?></td>
                            <td><?php
                                if (is_numeric($bounced)) {
                                    echo number_format($bounced);
                                } else {
                                    echo $bounced;
                                }
                                ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="ee_reports-list">
                    <div id="canvas-holder" style="width:80%;">
                        <canvas id="chart-area" />
                    </div>
                    <script>

                        var config = {
                            type: 'doughnut',
                            data: {
                                labels: ["<?php _e('Delivered', 'elastic-email-sender') ?>", "<?php _e('Opened', 'elastic-email-sender') ?>", "<?php _e('Clicked', 'elastic-email-sender') ?>", "<?php _e('Unsubscribed', 'elastic-email-sender') ?>", "<?php _e('Bounced', 'elastic-email-sender') ?>"],
                                datasets: [{
                                        label: '# of Votes',
                                        data: [
    <?php
    if (is_numeric($delivered)) {
        echo $delivered;
    } else {
        echo 100000;
    }
    ?>,
    <?php
    if (is_numeric($opened)) {
        echo $opened;
    } else {
        echo 85000;
    }
    ?>,
    <?php
    if (is_numeric($clicked)) {
        echo $clicked;
    } else {
        echo 95000;
    }
    ?>,
    <?php
    if (is_numeric($unsubscribed)) {
        echo $unsubscribed;
    } else {
        echo 4000;
    }
    ?>,
    <?php
    if (is_numeric($bounced)) {
        echo $bounced;
    } else {
        echo 4000;
    }
    ?>],
                                        backgroundColor: [
                                            'rgba(0, 153, 255, 0.4)',
                                            'rgba(0, 128, 0, 0.4)',
                                            'rgba(255, 159, 64, 0.4)',
                                            'rgba(255, 162, 0, 0.4)',
                                            'rgba(255, 0, 0, 0.4)'
                                        ],
                                        borderColor: [
                                            'rgba(241, 241, 241, 1)',
                                            'rgba(241, 241, 241, 1)',
                                            'rgba(241, 241, 241, 1)',
                                            'rgba(241, 241, 241, 1)',
                                            'rgba(241, 241, 241, 1)'
                                        ],
                                        borderWidth: 1.5
                                    }]
                            },
                            options: {
                                responsive: true
                            }
                        };
                        window.onload = function () {
                            var ctx = document.getElementById("chart-area").getContext("2d");
                            window.myPie = new Chart(ctx, config);
                        };
                    </script>
                </div>
            </div>
            <div class="ee_footer">
                <h4 class="ee_h4footer">
                    <?php _e('Share your experience of using Elastic Email WordPress Plugin by <a href="https://wordpress.org/support/plugin/elastic-email-sender/reviews/#new-post">rating us here.</a> Thanks!', 'elastic-email-sender') ?>
                </h4>
            </div>
        <?php } else { ?>

            <div class="">
                <div class="" style="text-align: center; padding-top: 10%; padding-bottom: 5%;">
                    <img src="<?php echo esc_url(plugins_url('/assets/images/connect_apikey.png', dirname(__FILE__))) ?>" >
                </div>
                <div class="ee_connect-alert">
                    <h1>
                        <?php _e('Oops! Your Elastic Email account has not been connected. Configure the settings to start using the plugin.', 'elastic-email-sender') ?>
                    </h1>
                </div>
            </div>
        <?php } ?>
    </div> 

    <div class="ee_col-5 ee_marketing" <?php
    if (empty($error) !== true) {
        echo 'style="border-left: 2px solid #e0e0e0;"';
    }
    ?>>
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
                    <input type="hidden" name="returnUrl" value="<?php echo admin_url('/admin.php?page=elasticemail-reports&subscribe=true'); ?>">
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
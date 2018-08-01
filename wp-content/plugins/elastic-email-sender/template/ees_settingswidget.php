<?php
defined('EE_ADMIN') OR die('No direct access allowed.');
if (isset($_GET['settings-updated'])):
    ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
    </div>
<?php endif; ?>
<div id="eewp_plugin" class="ee_row eewp_container">
    <div class="ee_col-7 <?php
    if (empty($error) === TRUE) {
        echo 'ee_line';
    }
    ?>">

        <section class="ee_containerfull ee_clearfix">
            <div class="ee_col-11 ee_header">
                <div class="ee_logo">
                    <?php echo '<img src="' . esc_url(plugins_url('/assets/images/icon.png', dirname(__FILE__))) . '" > ' ?>
                </div>
                <div class="ee_pagetitle">
                    <h1>Widget</h1>
                </div>
            </div>
            <div class="ee_col-1">
                <?php if (empty($error) === TRUE) { ?>
                    <form action="<?php echo admin_url('/admin.php?page=elasticemail-widget'); ?>" method="post">
                        <input class="ee_refrestlists" type="image" src="<?php echo esc_url(plugins_url('/assets/images/refresh.svg', dirname(__FILE__))) ?>" alt="Submit" width="32" height="32">
                    </form>
                <?php } ?>
            </div>
        </section>

        <?php
        if (empty($error) === TRUE) {
            ?>
            <hr>
            <section class="ee_containerfull ee_colorsetter">

                <?php
                foreach ($_POST as $setcolor_key => $setcolor_value) {
                    if ($setcolor_key != 'eeSetWidgetStyle' && $setcolor_key != '' && $setcolor_value != '') {
                        update_option($setcolor_key, $setcolor_value);
                    }
                }
                ?>

                <!-- hex widget style section -->
                <form action="<?php echo admin_url('/admin.php?page=elasticemail-widget'); ?>" method="post">
                    <div class="ee_colorsetterbox">
                        <div class="ee_col-4">
                            <p>Body color</p>
                            <input type="text" name="ee_widget_body" value="<?php echo get_option('ee_widget_body'); ?>"> 
                        </div>

                        <div class="ee_col-4">
                            <p>Button background color</p>
                            <input type="text" name="ee_widget_buton_bg" value="<?php echo get_option('ee_widget_buton_bg') ?>"> 
                        </div>

                        <div class="ee_col-4">
                            <p>Button text color</p>
                            <input type="text" name="ee_widget_buton_text" value="<?php echo get_option('ee_widget_buton_text') ?>"> 
                        </div>

                        <div class="ee_col-4">
                            <p>Label color</p>
                            <input type="text" name="ee_widget_label" value="<?php echo get_option('ee_widget_label') ?>"> 
                        </div>

                        <div class="ee_col-4">
                            <p>Title color</p>
                            <input type="text" name="ee_widget_title" value="<?php echo get_option('ee_widget_title') ?>"> 
                        </div>

                        <div class="ee_col-4">
                            <p>Subtitle color</p>
                            <input type="text" name="ee_widget_subtitle" value="<?php echo get_option('ee_widget_subtitle') ?>"> 
                        </div>
                    </div>
                    <div class="ee_col-12" style="text-align: right">
                        <input type="submit" class="ee_button" name="eeSetWidgetStyle" value="Set" />
                    </div>

                </form>
            </section>

            <hr>

            <section class="ee_containerfull ee_namelist ee_clearfix">
                <!-- create html list string to webform -->
                <?php
                if (isset($list['data'])) {

                    $number_of_lists = count($list['data']);
                    $new_list_template = array();
                    if (isset($_POST['addLists'])) {
                        $selected_list_id = array();
                        foreach ($_POST as $selected_list_key => $selected_list_value) {
                            if (($selected_list_key !== 'Add') && (strlen($selected_list_key) === 36)) {
                                array_push($selected_list_id, $selected_list_key);
                            }
                        }

                        $selected_list_id_count = count($selected_list_id);
                        $new_list_html = array();

                        for ($c = 0; $c <= $selected_list_id_count - 1; $c++) {
                            $new_list_html_single_item = '<li><input name="publiclistid" value="' . $selected_list_id[$c] . '" type="checkbox" checked><label class="ee_publiclistlabel"></label></li>';
                            array_push($new_list_html, $new_list_html_single_item);
                        }

                        //save to option html string for widget input (lists input)
                        update_option('ee_selectedlists_html', $new_list_html);

                        $new_list_namelist = array();
                        foreach ($_POST as $new_list_namelist_key => $new_list_namelist_value) {
                            if (($new_list_namelist_value !== 'Add')) {
                                array_push($new_list_namelist, $new_list_namelist_value);
                            }
                        }

                        //public list id for widget template
                        update_option('ee_selectedlists', $new_list_namelist);
                    }
                    ?>
                <?php } ?>  

                <p class="ee_title_small">You are currently adding to the lists:</p>
                <div class="ee_col-12 ee_selectedlist">
                    <?php
                    if (get_option('ee_enablecontactfeatures') == true) {
                        if (get_option('ee_selectedlists') == true) {
                            $selectedlists = get_option('ee_selectedlists');
                            echo '<p>' . implode(", ", $selectedlists) . '</p>';
                        } else {
                            echo '<p>----</p>';
                        }
                    } else {
                        echo '<p>All Contacts</p>';
                    }
                    ?>
                </div>
            </section>

            <?php
            if (get_option('ee_enablecontactfeatures') == false) {
                $contactfeaturesstatus = 'disabled';
            } else {
                $contactfeaturesstatus = '';
            }
            ?>

            <section class="ee_containerfull ee_checkboxlist ee_clearfix">
                <div class="ee_col-11 ee_nopadding">
                    <p class="ee_title_small">Select list:</p>
                </div>
                <form action="<?php echo admin_url('/admin.php?page=elasticemail-widget'); ?>" method="post">
                    <div class="ee_col-12 ee_nopadding">
                        <?php
                        //create checbox list in widget settings (checkbox list)
                        $list_id = array();
                        $list_selected = array(array());
                        //creates checkbox with contact lists
                        if (!isset($number_of_lists) || get_option('ee_enablecontactfeatures') == false) {
                            $number_of_lists = 0;
                            echo '----';
                        }
                        for ($x = 0; $x < $number_of_lists; $x++) {
                            $public_list_id = $list['data'][$x]['publiclistid'];
                            $list_name = $list['data'][$x]['listname'];
                            echo '<div class="ee_col-4 ee_chceckboxlist"><input type="checkbox" class="ee_checkbox" value="' . $list_name . '" name="' . $public_list_id . '" >' . $list_name . '</div>';

                            array_push($list_selected, array($public_list_id => $list_name));
                        }
                        $list_selected_raw = $list_selected;
                        $list_selected_clean = array_slice($list_selected_raw, 1);
                        ?>
                    </div>

                    <?php
                    //delete list
                    if (isset($_POST['delLists'])) {
                        foreach ($_POST as $newDelListName_key => $newDelListName_val) {
                            update_option('ee_delListName', $newDelListName_val);
                            do_action('delLists');
                        }
                        delete_option('ee_delListName');
                    }
                    ?>

                    <div class="ee_col-6 ee_nopadding ee_buttonpadding" style="text-align: left;">
                        <input type="submit" class="ee_button ee_deletebtn" name="delLists" value="Delete" <?php echo $contactfeaturesstatus; ?> />
                    </div>
                    <div class="ee_col-6 ee_nopadding ee_buttonpadding" style="text-align: right;">
                        <input type="submit" class="ee_button" name="addLists" value="Add" <?php echo $contactfeaturesstatus; ?> />
                    </div>
                </form>
            </section>
            <hr>
            <section class="ee_containerfull ee_newilstinput ee_clearfix"> 
                <div class="ee_col-8 ee_nopadding">
                    <p class="ee_title_small">Add new list:</p>

                    <form action="<?php echo admin_url('/admin.php?page=elasticemail-widget&added-succes=true'); ?>" method="post">
                        <input type="text" name="listName" style="width: 70%;" <?php echo $contactfeaturesstatus; ?> />
                        <input type="submit" class="ee_button" name="addNewLists" value="Add new" <?php echo $contactfeaturesstatus; ?> />
                    </form>
                </div>
                <?php
                if (get_option('ee_enablecontactfeatures') == false) {
                    echo '<div class="ee_col-4"><p class="ee_addedlist-info">Some options are hidden because you have Contact Delivery Tools disabled. Turn it on <a href="https://elasticemail.com/account/#/settings/accountconfiguration">here.</a> </></div>';
                } else {
                    echo '<div class="ee_col-4"></div>';
                }
                ?>
                <?php
                //add new list
                if (isset($_POST['addNewLists'])) {
                    if (isset($_POST['listName'])) {
                        $newListName = filter_var($_POST['listName'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                        if ($newListName != '' && $newListName != ' ' && $newListName != NULL) {
                            $newListStatus = true;
                            update_option('ee_newListName', $newListName);
                            do_action('addNewLists');
                        } else {
                            $newListStatus = false;
                        }
                        if (isset($_GET['added-succes'])) {
                            if (isset($newListStatus) && $newListStatus == TRUE) {
                                echo '<div class="ee_col-4"><p class="ee_addetlist-success">Success!</></div>';
                            } else {
                                echo '<div class="ee_col-4"><p class="ee_addetlist-warning">Incorrect name!</></div>';
                            }
                        }
                    }
                }
                ?>
            </section>
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
            <?php
        }
        ?>


    </div>

    <div class="ee_col-5 ee_marketing" <?php
    if (empty($error) !== true) {
        echo 'style="border-left: 2px solid #e0e0e0;"';
    }
    ?>>
        <h2 class="ee_h2">Let us help you send better emails!</h2>
        <h4 class="ee_footertext">
            If you are new to Elastic Email, feel free to visit our <a href="https://elasticemail.com">website</a> and find out how our comprehensive set of tools will help you reach your goals or get premium email marketing tools at a fraction of what you're paying now!
        </h4>
        <hr>
        <h4 class="ee_h4">If you already use Elastic Email to send your emails, you can subscribe to our monthly updates to start receiving the latest email news, tips, best practices and more.</h4>
        <?php if (isset($_GET['subscribe']) === false) { ?>
            <form action="https://api.elasticemail.com/contact/add?version=2" method="post">
                <fieldset style="border:none;">
                    <input type="hidden" name="publicaccountid" value="49540e0f-2e09-4101-a05d-5032842b99d3">
                    <input type="hidden" name="returnUrl" value="<?php echo admin_url('/admin.php?page=elasticemail-widget&subscribe=true'); ?>">
                    <input type="hidden" name="activationReturnUrl" value="">
                    <input type="hidden" name="activationTemplate" value="Subscription_from_blog">
                    <input type="hidden" name="source" value="WebForm">
                    <input type="hidden" name="notifyEmail" value="">
                    <div class="ee_inputs">
                        <span id="email" style="width: 100%;">
                            <label for="email" style="padding-right: 5px;">Email Address</label>
                            <input maxlength="40" class="form-control" name="email" size="20" type="email" required="" style="width: 60%;"> 
                        </span>
                        <br/><br/>
                        <span id="field_firstname" style="width: 100%;">
                            <label for="field_firstname" style="padding-right: 51px;">Name</label>
                            <input maxlength="40" class="form-control" name="field_firstname" size="20" type="string" style="width: 60%;">
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
                    <input type="submit" class="ee_button" name="submit" value="Subscribe">
                </fieldset>
            </form>
            <?php
        } else {
            echo '<h3 style="color: green; font: bold;">Thank you for subscribing to our newsletter!</h3>
            <h5 style="color: green;">You will start receiving our email marketing newsletter, as soon as you confirm your subscription.</h5>';
        }
        ?>
        <br/>
        <hr>
        <br/>
        <h2 class="ee_h2">How we can help you?</h2>
        <h4 class="ee_h4">If you would like to boost your email marketing campaigns or improve your email delivery, check out our helpful guides to get you started!</h4>        
        <ul style="padding-left: 40px;">
            <li type="circle"><a href="https://elasticemail.com/support/">Guides and resources</a></li>
            <li type="circle"><a href="https://elasticemail.com/api-documentation-and-libraries/">Looking for code? Check our API</a></li>
            <li type="circle"><a href="https://elasticemail.com/contact/">Want to talk with a live person? Contact us</a></li>
        </ul>
        <br/>
        <h4 class="ee_h4">Remember that in case of any other questions or feedback, you can always contact our friendly <a href="http://support.elasticemail.com/">Support Team.</a></h4>   
    </div>
</div>
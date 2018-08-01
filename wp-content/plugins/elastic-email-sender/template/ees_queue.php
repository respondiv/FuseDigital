<?php
defined('EE_ADMIN') OR die('No direct access allowed.');
if (isset($_GET['settings-updated'])):
    ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
    </div>
<?php endif; ?>
<div id="eewp_plugin" class="ee_row eewp_container">
    <div class="ee_col-12">

        <section class="ee_containerfull ee_clearfix">
            <div class="ee_col-11 ee_header">
                <div class="ee_logo">
                    <?php echo '<img src="' . esc_url(plugins_url('/assets/images/icon.png', dirname(__FILE__))) . '" > ' ?>
                </div>
                <div class="ee_pagetitle">
                    <h1>Queue</h1>
                </div>
                <p>The queue of messages that have not been sent. You can send them again by clicking SEND next to it.</p>
            </div>
        </section>


        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'senderqueue';

        // create queue list
        $senderqueue_object = $wpdb->get_results("SELECT * FROM `$table_name` WHERE 1");

        //table header
       
        echo '<div class="ee_col-4"><b>Message to</b></div><div class="ee_col-5"><b>Subject</b></div><div class="ee_col-2"><b>Date</b></div><div class="ee_col-1"><b>Resend</b></div>';
        echo '<form method="post" action="' . admin_url('/admin.php?page=elastic-queue') . '">';

        // table content
        if (!empty($senderqueue_object)) {
            foreach ($senderqueue_object as $senderqueue) {
                echo '<div class="ee_col-4 ee_tablestyle">' . $senderqueue->msg_to . '</div>';
                echo '<div class="ee_col-5 ee_tablestyle">' . $senderqueue->msg_subject . '</div>';
                echo '<div class="ee_col-2 ee_tablestyle">' . $senderqueue->msg_date . '</div>';
                echo '<div class="ee_col-1 ee_tablestyle" style="overflow-x:hidden;"><button type="submit" class="ee_button_resend" name="resend" value="' . $senderqueue->id . '">SEND</button></div>';
            }
        } else {
                echo '<div class="ee_col-12 ee_tablestyle"><span style="color:green;">No messages in the queue. All messages have been sent successfully.</span></div>';
        }
        echo '</form>';

        // send config
        $Resend = new \ElasticEmailClient\Email();
        $send_status = '';
        if (isset($_POST['resend'])) {
            $resendID = $_POST['resend'];
            $msg_body_json = $wpdb->get_var("SELECT `msg_body` FROM `$table_name` WHERE `id` = $resendID");
            $msg_body_decode = (json_decode($msg_body_json));

            $emailresend = $Resend->Send($msg_body_decode[0] /* $subject */, $msg_body_decode[1] /* $from_email */, $msg_body_decode[2] /* $from_name */, null /* $sender */, null /* $senderName */, null /* $msgFrom */, null /* $msgFromName */, $msg_body_decode[7] /* $reply_to */, $msg_body_decode[8] /* $reply_to_name */, array() /* array $to */, $msg_body_decode[10] /* $to */, $msg_body_decode[11] /* $cc */, $msg_body_decode[12] /* $bcc */, array() /* $lists */, array() /* $segments */, null /* $mergeSourceFilename */, null /* $channel */, $msg_body_decode[17] /* $bodyHTML */, $msg_body_decode[18] /* $bodyText */, $msg_body_decode[19] /* $charset */, null /* $charsetBodyHtml */, null /* $charsetBodyText */, $msg_body_decode[22] /* $encodingTyp */, null /* $template */, $msg_body_decode[24] /* $attachmentFiles */, (array) $msg_body_decode[25] /* $headers */);

            if (isset($emailresend['success']) === TRUE) {
                $wpdb->delete($table_name, array('ID' => $resendID), $where_format = null);
                $send_status = '<span style="color:green;font-weight: 700;">The message has been sent again.</span>';
            } else {
                $send_statu = '<span style="color:#ffa22a;font-weight: 700;">Failed to send, try again in a moment or check your Elastic Email account configuration.';
            }
        }
        
        ?>
        <div class="ee_col-12">
            <?php
            if (isset($send_status)) {
                echo $send_status;
            }
            ?>
        </div>

    </div>
</div>
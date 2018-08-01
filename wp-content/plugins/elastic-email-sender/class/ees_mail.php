<?php

class eemail {

    static $options = array(),
            $conflict = false;

    public static function on_load($pluginpath) {
        self::$options = get_option('ee_options');

        if (function_exists('wp_mail')) {
            self::$conflict = true;
            add_action('admin_notices', array(__CLASS__, 'adminNotices'));
            return;
        }

        if (self::is_configured() === false) {
            return;
        }

        require_once($pluginpath . '/api/ElasticEmailClient.php');
        \ElasticEmailClient\ApiClient::SetApiKey(self::getOption('ee_apikey'));

        function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
            try {
                $rs = eemail::send($to, $subject, $message, $headers, $attachments);
                if ($rs !== true) {
                    return eemail::wp_mail_native($to, $subject, $message, $headers, $attachments, $rs);
                }
                return $rs;
            } catch (Exception $e) {
                return eemail::wp_mail_native($to, $subject, $message, $headers, $attachments, $e->getMessage());
            }
        }

    }

    static function send($to, $subject, $message, $headers, $attachments) {
        $atts = apply_filters('wp_mail', compact('to', 'subject', 'message', 'headers', 'attachments'));
        if (isset($atts['to'])) {
            $to = $atts['to'];
        }
        if (isset($atts['subject'])) {
            $subject = $atts['subject'];
        }
        if (isset($atts['message'])) {
            $message = $atts['message'];
        }
        if (isset($atts['headers'])) {
            $headers = $atts['headers'];
        }
        if (isset($atts['attachments'])) {
            $attachments = $atts['attachments'];
        }
        if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
        }
        $cc = $bcc = array();
        if (empty($headers)) {
            $headers = array();
        } else {
            if (!is_array($headers)) {
                $tempheaders = explode("\n", str_replace("\r\n", "\n", $headers));
            } else {
                $tempheaders = $headers;
            }
            $headers = array();
            $j = 1;
            if (!empty($tempheaders)) {
                foreach ((array) $tempheaders as $header) {
                    if (strpos($header, ':') === false) {
                        if (false !== stripos($header, 'boundary=')) {
                            $parts = preg_split('/boundary=/i', trim($header));
                            $boundary = trim(str_replace(array("'", '"'), '', $parts[1]));
                        }
                        continue;
                    }
                    list( $name, $content ) = explode(':', trim($header), 2);
                    $name = trim($name);
                    $content = trim($content);
                    switch (strtolower($name)) {
                        case 'from':
                            list($from_email, $from_name) = self::getEmailAndName($content);
                            break;
                        case 'content-type':
                            if (strpos($content, ';') !== false) {
                                list( $type, $charset_content ) = explode(';', $content);
                                $content_type = trim($type);
                                if (false !== stripos($charset_content, 'charset=')) {
                                    $charset = trim(str_replace(array('charset=', '"'), '', $charset_content));
                                } elseif (false !== stripos($charset_content, 'boundary=')) {
                                    $boundary = trim(str_replace(array('BOUNDARY=', 'boundary=', '"'), '', $charset_content));
                                    $charset = '';
                                }
                            } elseif ('' !== trim($content)) {
                                $content_type = trim($content);
                            }
                            break;
                        case 'cc':
                            $cc = array_merge((array) $cc, explode(',', $content));
                            break;
                        case 'bcc':
                            $bcc = array_merge((array) $bcc, explode(',', $content));
                            break;
                        case 'reply-to':
                            list($reply_to, $reply_to_name) = self::getEmailAndName($content);
                            break;
                        default:
                            //custom headers
                            $headers[('header' . $j++)] = sprintf('%1$s: %2$s', trim($name), trim($content));
                            break;
                    }
                }
            }
        }
        if (!isset($from_name)) {
            $from_name = 'Wordpress';
        }
        if (!isset($from_email)) {
            $from_email = 'wordpress@' . self::getDefaultDomain();
        }
        if (!isset($content_type)) {
            $content_type = 'text/plain';
        }
        if (!isset($charset)) {
            $charset = get_bloginfo('charset');
        }
        if (!is_array($to)) {
            $to = array_merge(explode(',', $to), $bcc);
        }
        if ($content_type === 'text/plain') {
            $message_bodyHTML = null;
            $message_bodyText = $message;
        } else {
            $message_bodyHTML = $message;
            $message_bodyText = $message;
        }

        $from_email = apply_filters('wp_mail_from', $from_email);
        $from_name = apply_filters('wp_mail_from_name', $from_name);
        $content_type = apply_filters('wp_mail_content_type', $content_type);
        $charset = apply_filters('wp_mail_charset', $charset);


        $Email = new \ElasticEmailClient\Email();

        if (!isset($reply_to)) {
            $reply_to = '';
        }
        if (!isset($reply_to_name)) {
            $reply_to_name = '';
        }

        $emailsend = $Email->Send(
                /* 1 subject */ $subject,
                /* 2 from */ $from_email,
                /* 3 fromName */ $from_name,
                /* 4 sender */ null,
                /* 5 senderName */ null,
                /* 6 msgFrom */ null,
                /* 7 msgFromName */ null,
                /* 8 replyTo */ $reply_to,
                /* 9 replyToName */ $reply_to_name,
                /* 10 to */ $to,
                /* 11 msgTo */ array(),
                /* 12 msgCC */ $cc,
                /* 13 msgBcc */ $bcc,
                /* 14 lists */ array(),
                /* 15 segments */ array(),
                /* 16 mergeSourceFilename */ null,
                /* 17 channel */ null,
                /* 18 bodyHtml */ $message_bodyHTML,
                /* 19 bodyText */ $message_bodyText,
                /* 20 charset */ $charset,
                /* 21 charsetBodyHtml */ null,
                /* 22 charsetBodyText */ null,
                /* 23 encodingType */ ApiTypes\EncodingType::None,
                /* 24 template */ null,
                /* 25 attachmentFiles */ $attachments,
                /* 26 headers */ $headers,
                /* 27 postBack */ null,
                /* 28 merge */ array(),
                /* 29 timeOffSetMinutes */ null,
                /* 30 poolName */ null,
                /* 31 isTransactional */ false
        );

        if (!$emailsend['success'] === true) {
            $queue = json_encode(array($subject, $from_email, $from_name, null, null, null, null, $reply_to, $reply_to_name, $to, array(), $cc, $bcc, array(), array(), null, null, $message_bodyHTML, $message_bodyText, $charset, null, null, ApiTypes\EncodingType::None, null, $attachments, $headers, null, array(), null, null, false));
            global $wpdb;
            $table_name = $wpdb->prefix . 'senderqueue';
            $wpdb->insert($table_name, array('msg_to' => $reply_to, 'msg_subject' => $subject, 'msg_date' => date("Y-m-d H:i:s"), 'msg_body' => $queue));
        }

        if (isset($emailsend)) {
            if ($emailsend == TRUE) {
                return true;
            } else {
                return false;
            }
        }
    }

    static function wp_mail_native($to, $subject, $message, $headers, $attachments, $error) {
        error_log("eemail::wp_mail_native: $to ($subject) Error: $error");
        require_once plugin_dir_path(__DIR__) . 'defaults/function.wp_mail.php';
    }

//Helpers method
    static function adminNotices() {
        if (self::$conflict) {
            echo '<div class="error"><p>wp_mail has been declared by another process or plugin, so you won\'t be able to use ElasticEmailSender until the problem is solved.</p></div>';
        }
    }

    static function is_configured() {
        return (self::getOption('ee_enable') === 'yes' && self::getOption('ee_apikey'));
    }

    static function getOption($name, $default = false) {
        if (isset(self::$options[$name])) {
            return self::$options[$name];
        }
        return $default;
    }

    static function getEmailAndName($content) {
        $address = array('', '');
        $bracket_pos = strpos($content, '<');
        if ($bracket_pos !== false) {
            // Text before the bracketed email is the "From" name.
            if ($bracket_pos > 0) {
                $address[1] = substr($content, 0, $bracket_pos - 1);
                $address[1] = str_replace('"', '', $address[1]);
                $address[1] = trim($address[1]);
            }

            $address[0] = substr($content, $bracket_pos + 1);
            $address[0] = str_replace('>', '', $address[0]);
            $address[0] = trim($address[0]);
            // Avoid setting an empty $email.
        } elseif ('' !== trim($content)) {
            $address[0] = trim($content);
        }
        return $address;
    }

    /* If we don't have an email from the input headers default to wordpress@$sitename
     * Some hosts will block outgoing mail from this address if it doesn't exist but
     * there's no easy alternative. Defaulting to admin_email might appear to be another
     * option but some hosts may refuse to relay mail from an unknown domain. See
     * https://core.trac.wordpress.org/ticket/5007.
     */

    static function getDefaultDomain() {
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        return $sitename;
    }

}

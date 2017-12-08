<?php namespace App;

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    static function send($to, $subject, $body, $path = null, $stringAttachments = null, $cc = false, $cc_to = null)
    {
        global $cfg;

        //SMTP needs accurate times, and the PHP time zone MUST be set
        //This should be done in your php.ini, but this is how to do it if you don't have access to that
        date_default_timezone_set('Etc/UTC');

        //Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        //Tell PHPMailer to use SMTP

        if (SMTP_USE_SENDMAIL) {
            $mail->isSendmail();
        } else {
            $mail->isSMTP();
        }

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->isHTML(true);

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = SMTP_DEBUG;

        //Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';

        //Set the hostname of the mail server
        $mail->Host = SMTP_HOST;

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->CharSet = 'UTF-8';
        $mail->Port = SMTP_PORT;

        if (SMTP_AUTH) {
            $mail->SMTPSecure = SMTP_ENCRYPTION;

            //Whether to use SMTP authentication

            $mail->SMTPAuth = SMTP_AUTH;

            //Username to use for SMTP authentication - use full email address for gmail
            $mail->Username = SMTP_AUTH_USERNAME;


            //Password to use for SMTP authentication
            $mail->Password = SMTP_AUTH_PASSWORD;
        }

        //Set who the message is to be sent from
        try {

            $mail->setFrom(SMTP_FROM);


            //Set who the message is to be sent to
            $mail->addAddress($to);

            //Set the subject line
            $mail->Subject = $subject;

            //Set message body
            $mail->Body = "<html><body>$body</body></html>";
            $mail->AltBody = strip_tags($body);

            // DKIM
            if (!empty(SMTP_DKIM_ENABLED)) {
                $mail->DKIM_domain = SMTP_DKIM_DOMAIN;
                $mail->DKIM_private = SMTP_DKIM_PRIVATE_KEY;
                $mail->DKIM_selector = SMTP_DKIM_SELECTOR;
                $mail->DKIM_passphrase = SMTP_DKIM_PASSPHRASE;
                $mail->DKIM_identity = SMTP_DKIM_IDENTITY;
            }


            //Set Carbon Copy recipient
            if ($cc) {
                $mail->AddCC($cc_to);
            }

            //Attach attachment, if given
            if (!empty($path)) {

                $mail->addAttachment($path);
            }
            if (!empty($stringAttachments)) {
                foreach ($stringAttachments as $key => $val) {
                    $mail->addStringAttachment($val, $key);
                }
            }

            //send the message
            $mail->send();

        } catch (\Exception $e) {

            // Check ajaxness
            $is_ajax_request = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if($is_ajax_request){

                stop(500, $e->getMessage());

            }else{

                error_out($e->getMessage());
            }

        }

    }

    static function hasValidDomain($email)
    {
        $regex = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";

        if (preg_match("/$regex/", $email, $email_parts) == 1) {

            $domain = $email_parts[2];

            if (checkdnsrr($domain, "MX")) {
                return true;
            }

        }

        return false;
    }



}
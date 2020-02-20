<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail
 *
 * PHP version 7.0
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $cc = "smb_srs.pet@ph.yazaki.com", $subject, $message, $attachment = "")
    {

        $mail = new PHPMailer(true);

        //$mail->SMTPDebug = 1;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = "exchange.jp.yazaki.com";                       // Specify main and backup SMTP servers
        //$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        //$mail->Host       = "10.194.23.193";  
        $mail->SMTPAuth   = false;
        $mail->Username   = Config::MAILER_USERNAME;                // SMTP username
        $mail->Password   = Config::MAILER_PASSWORD;                // SMTP password
        //$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 25;                                    // TCP port to connect to

        /*
        $mail->From = Config::MAILER_USERNAME;
        $mail->FromName = "[SRS Mailer]";
        */

        $mail->setFrom(Config::MAILER_USERNAME, "[SRS Mailer]");

        if (!empty($to)) {
            $mail->addAddress($to);
        } else {
            $mail->addAddress('smb_srs.pet@ph.yazaki.com');
        }

        $mail->AddReplyTo(Config::MAILER_USERNAME, "[SRS Mailer]");
        $mail->addCC('smb_srs.pet@ph.yazaki.com');                                    // Name is optional

        if (!empty($cc)) {

            if (is_array($cc)) {

                foreach ($cc as $email) {
                    # code...

                    $mail->addCC($email);
                }
            } else {
                # code...
                $mail->addCC($cc);
            }
        } else {
            $mail->addCC('smb_srs.pet@ph.yazaki.com');
        }


        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if (!empty($attachment)) {
            
            $mail->AddAttachment($attachment); 
        }

        if (!$mail->Send()) {
            return "Message could not be sent.";
            return "Mailer Error: " . $mail->ErrorInfo;
            exit;
        } else {
            return 'Message has been sent';
        }
    }
}

<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Send email with attachment
     * 
     * @param string $file path of the file to send 
     * @param string $from sender address
     * @param string $to receiver address
     */
    public function send($file, $from, $to) 
    {
        try {
            $mail = new PHPMailer();
            $mail->IsSendmail(); 
            $mail->SetFrom($from, 'Kindled');
            $mail->addReplyTo($from, 'Kindled');
            $mail->AddAddress($to, 'Kindled');
            $mail->Subject = 'Kindled';
            $mail->MsgHTML('Your .mobi file');
            $mail->AddAttachment($file); 

            if(!$mail->Send()) {
                throw new \Exception('Message could not be sent. Mailer Error: '. $mail->ErrorInfo);
            }            
        } catch (Exception $e) {
            throw new \Exception('Message could not be sent. Mailer Error: '. $mail->ErrorInfo);
        }
    }
}
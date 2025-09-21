<?php
namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'gymly2b@gmail.com';
        $this->mailer->Password = 'xymx fdws azbh aoxd';
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = 587;
        $this->mailer->setFrom('your_email@gmail.com', 'Gymly');
    }

    public function sendVerification($to, $code) {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($to);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Your Gymly Verification Code';
        $this->mailer->Body = "Your verification code is: <b>$code</b>";
        return $this->mailer->send();
    }
}

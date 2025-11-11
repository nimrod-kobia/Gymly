<?php
namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private PHPMailer $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'gymly2b@gmail.com';
        $this->mailer->Password = 'xymx fdws azbh aoxd';
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = 587;
        $this->mailer->setFrom('gymly2b@gmail.com', 'Gymly');
    }

    public function sendVerification(string $to, string $username, string $code) {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($to);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Gymly Account Verification';
        $this->mailer->Body = "
            Hi <b>$username</b>,<br><br>
            Your verification code is: <b>$code</b><br>
            This code will expire in 15 minutes.<br><br>
            Thanks,<br>Gymly Team
        ";
        return $this->mailer->send();
    }
    
    public function sendMail(string $to, string $subject, string $htmlBody) {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($to);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $htmlBody;
        return $this->mailer->send();
    }
}
?>

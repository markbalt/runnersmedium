<?php

class MailComponent extends Component
{
    // send message using sendmail
    public function send($from = null, $fromName = null, $to = null, $subject = null, $body = null)
    {
        if ($from && $to && $subject && $body) {
            $headers = ($fromName) ? "From: ". $fromName . " <" . $from . ">\r\n" : "From: $from\r\n" . "X-Mailer: php";
            
            // use sendmail
            if (mail($to, $subject, $body, $headers)) {
                return true;
            } else {
            	$this->error = 'unable to send mail';
            	return false;
            }
        }
        return false;
    }
}

?>
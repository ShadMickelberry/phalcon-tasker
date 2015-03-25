<?php
/**
 * Created by PhpStorm.
 * User: shad
 * Date: 2/22/15
 * Time: 4:27 PM
 */

namespace App\Library;

use Phalcon\Mvc\User\Component;
use Swift_SmtpTransport;
use Swift_Mailer;

class Mail extends Component {

    /**
     * SMTP URL
     * @var string
     */
    protected $url;

    /**
     * SMTP user
     * @var string
     */
    protected $login;

    /**
     * SMTP password
     * @var string
     */
    protected $password;

    /**
     * SMTP PORT
     * @var string
     */
    protected $port;

    /**
     * SMTP from name
     * @var string
     */
    protected $from_name;

    /**
     * SMTP from email
     * @var string
     */
    protected  $from_email;

    protected $transport;

    public function __construct()
    {
        $this->setConfig();
        $this->setTransport();
    }

    /**
     * SETS Config settings from /app/config/config.php settings
     */
    protected function setConfig()
    {
        $this->url = $this->config->mail->smtp->url;
        $this->login = $this->config->mail->smtp->login;
        $this->password = $this->config->mail->smtp->password;
        $this->port = $this->config->mail->smtp->port;

        $this->from_email = $this->config->mail->fromAddress;
        $this->from_email = $this->config->mail->fromEmail;

    }

    protected function setTransport()
    {
        // Create the Transport
        $this->transport = Swift_SmtpTransport::newInstance($this->url, $this->port)
            ->setUsername($this->login)
            ->setPassword($this->password)
        ;
    }

    public function sendMail($to_email, $to_name, $subject, $html, $text)
    {
        try {
            // Create the Mailer using your created Transport
            $mailer = Swift_Mailer::newInstance($this->transport);

            // Create a message
            $message = new \Swift_Message();
            $message->setSubject($subject);
            $message->setFrom(array($this->from_email => $this->from_name));
            $message->setTo(array($to_email => $to_name));
            $message->setBody($html, 'text/html');
            $message->addPart($text, 'text/plain');

            // Send the message
            $result = $mailer->send($message);
            return $result;
        } catch (\Swift_SwiftException $e) {
            $response = $e->getMessage();
            $response .= $this->config->mail->fromEmail;
           return $response;
        }

    }

} 
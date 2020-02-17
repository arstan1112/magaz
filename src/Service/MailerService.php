<?php


namespace App\Service;


class MailerService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * MailerService constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $messageBody
     * @param $email
     */
    public function send($messageBody, $email)
    {
        $name = 'Kyle';
        $message = (new \Swift_Message('Hi email'))
            ->setFrom('slee76058@gmail.com')
            ->setTo($email)
            ->setBody(
                $messageBody
//                $this->renderView(
//                    'emails/registration.html.twig',
//                    ['name' => $name]
//                )
            )
        ;
        $this->mailer->send($message);
    }

}
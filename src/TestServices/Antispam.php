<?php


namespace App\TestServices;
use Swift_Message;


class Antispam
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function send(Swift_Message $message)
    {
        $this->logger->info('Envoi du mail : ' . $message);
    }
}
<?php


namespace App\TestServices;


class Spam
{
    private $chaines_interdites;
    private $logger;
    public function __construct($chaines_interdites)
    {
        $this->chaines_interdites = $chaines_interdites;
        $this->logger = new Logger(new JsonFormatter());
    }
    public function isSpam($message, $request)
    {
        if( $message == $this->chaines_interdites[0] || $message == $this->chaines_interdites[1] )
        {
            $ip = $request->server->get('REMOTE_ADDR');
            $this->logger->info("Vous venez d'écrire une chaîne interdite ".$ip);
            return true;
        }else{
            return false;
        }
    }
}
<?php


namespace App\TestServices;

use App\TestServices\JsonFormatter;

class Logger extends JsonFormatter
{
    private $formatter;

    public function __construct(JsonFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function info($message, array $context = array())
    {
        $log = $this->formatter->format(array($message));

        dump($log);
    }

    public function error($message, array $context = array())
    {
        dump($message);
    }
}
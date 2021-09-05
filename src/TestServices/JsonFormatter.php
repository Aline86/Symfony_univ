<?php
namespace App\TestServices;
use Monolog\Formatter\FormatterInterface;

class JsonFormatter implements FormatterInterface
{
    public function format(array $record)
    {
        return json_encode($record);
    }

    public function formatBatch(array $records)
    {
        return json_encode($records);
    }
}
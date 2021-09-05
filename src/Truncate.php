<?php

namespace App;
use Twig\Extension\AbstractExtension;
use Twig\FileExtensionEscapingStrategy;
use Twig\TwigFilter;

class Truncate extends AbstractExtension
{
    public function truncate($text, $max = 15)
    {
        if (mb_strlen($text) > $max) {
            $text = mb_substr($text, 0, $max);
            $text = mb_substr($text, 0, mb_strrpos($text,' '));
            $text.= '...';
        }
        return $text;
    }
    public function getFilters()
    {
        return [
            new TwigFilter('truncate', [$this, 'truncate']),
        ];
    }
}
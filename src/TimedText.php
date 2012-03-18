<?php
require_once 'TimedText/Lexar.php';
require_once 'TimedText/Parser.php';

class TimedText
{
    public static function convert($text)
    {
        $lexar = new TimedText_Lexar;
        $parser = new TimedText_Parser;
        return $parser->parse($lexar->tokenize($text));
    }
}

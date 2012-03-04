<?php
require_once 'TimedText/Parser.php';
require_once 'TimedText/Lexar.php';
require_once 'TimedText/Text.php';
require_once 'TimedText/Section.php';

class TimedText_ParserTest extends PHPUnit_Framework_TestCase
{
    public $parser;

    public function setUp()
    {
        $this->parser = new TimedText_Parser;
    }

    /**
     * @test
     */
    public function parse_should_return_Text_object()
    {
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo'));
        $tokens = array(new TimedText_Token_String('foo'));
        $this->assertEquals($expected, $this->parser->parse($tokens));
    }

    public function assertEqualsAsTimedText($expected, TimedText_Text $actual, $message = NULL)
    {
        $this->assertEquals($expected, (string)$actual, $message);
    }
}

<?php
require_once 'TimedText/Parser.php';
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
        $this->assertEqualsAsTimedText('foo', $this->parser->parse('foo'));
    }

    public function assertEqualsAsTimedText($expected, TimedText_Text $actual, $message = NULL)
    {
        $this->assertEquals($expected, (string)$actual, $message);
    }
}

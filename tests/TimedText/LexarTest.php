<?php
require_once 'TimedText/Lexar.php';

class TimedText_LexarTest extends PHPUnit_Framework_TestCase
{
    public $lexar;

    public function setUp()
    {
        $this->lexar = new TimedText_Lexar;
    }

    public function tokenize_should_create_empty_array_from_empty_string()
    {
        $this->assertEquals(array(), $this->lexar->tokenize(''));
    }

    /**
     * @test
     */
    public function tokenize_can_create_string_token()
    {
        $expected = array(new TimedText_Token_String('foo'));
        $this->assertEquals($expected, $this->lexar->tokenize('foo'));
    }

    /**
     * @test
     */
    public function tokenize_can_create_begin_before_token()
    {
        $date = '2000/01/01 00:00';
        $expected = array(new TimedText_Token_BeginBefore($date));
        $this->assertEquals($expected, $this->lexar->tokenize("{before {$date}}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_end_before_token()
    {
        $expected = array(new TimedText_Token_EndBefore);
        $this->assertEquals($expected, $this->lexar->tokenize('{/before}'));
    }

    /**
     * @test
     */
    public function tokenize_can_create_begin_after_token()
    {
        $date = '2000/01/01 00:00';
        $expected = array(new TimedText_Token_BeginAfter($date));
        $this->assertEquals($expected, $this->lexar->tokenize("{after {$date}}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_end_after_token()
    {
        $expected = array(new TimedText_Token_EndAfter);
        $this->assertEquals($expected, $this->lexar->tokenize('{/after}'));
    }
}

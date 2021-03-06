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
    public function tokenize_can_create_String_token()
    {
        $expected = array(new TimedText_Token_String('foo'));
        $this->assertEquals($expected, $this->lexar->tokenize('foo'));
    }

    /**
     * @test
     */
    public function tokenize_can_create_BeginBefore_token()
    {
        $date = '2000/01/01 00:00';
        $expected = array(new TimedText_Token_BeginBefore($date));
        $this->assertEquals($expected, $this->lexar->tokenize("{before {$date}}"));
    }

    /**
     * @test
     * @dataProvider lineBreakProvider
     */
    public function a_linebreak_after_BeginBefore_token_should_be_omitted($lineBreak)
    {
        $date = '2000/01/01 00:00';
        $expected = array(new TimedText_Token_BeginBefore($date));
        $this->assertEquals($expected, $this->lexar->tokenize("{before {$date}}{$lineBreak}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_EndBefore_token()
    {
        $expected = array(new TimedText_Token_EndBefore);
        $this->assertEquals($expected, $this->lexar->tokenize('{/before}'));
    }

    /**
     * @test
     * @dataProvider lineBreakProvider
     */
    public function a_linebreak_after_EndBefore_token_should_be_omitted($lineBreak)
    {
        $expected = array(new TimedText_Token_EndBefore);
        $this->assertEquals($expected, $this->lexar->tokenize("{/before}{$lineBreak}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_BeginAfter_token()
    {
        $date = '2000/01/01 00:00';
        $expected = array(new TimedText_Token_BeginAfter($date));
        $this->assertEquals($expected, $this->lexar->tokenize("{after {$date}}"));
    }

    /**
     * @test
     * @dataProvider lineBreakProvider
     */
    public function a_linebreak_after_BeginAfter_token_should_be_omitted($lineBreak)
    {
        $date = '2000/01/01 00:00';
        $expected = array(new TimedText_Token_BeginAfter($date));
        $this->assertEquals($expected, $this->lexar->tokenize("{after {$date}}{$lineBreak}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_EndAfter_token()
    {
        $expected = array(new TimedText_Token_EndAfter);
        $this->assertEquals($expected, $this->lexar->tokenize('{/after}'));
    }

    /**
     * @test
     * @dataProvider lineBreakProvider
     */
    public function a_linebreak_after_EndAfter_token_should_be_omitted($lineBreak)
    {
        $expected = array(new TimedText_Token_EndAfter);
        $this->assertEquals($expected, $this->lexar->tokenize("{/after}{$lineBreak}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_BeginBetween_token()
    {
        $after = '2000/01/01 00:00';
        $before = '2001/01/01 00:00';
        $expected = array(new TimedText_Token_BeginBetween($after, $before));
        $this->assertEquals($expected, $this->lexar->tokenize("{between {$after} - {$before}}"));
    }

    /**
     * @test
     */
    public function tokenize_can_create_EndBetween_token()
    {
        $expected = array(new TimedText_Token_EndBetween);
        $this->assertEquals($expected, $this->lexar->tokenize('{/between}'));
    }

    /**
     * @test
     * @dataProvider lineBreakProvider
     */
    public function a_linebreak_after_EndBetween_token_should_be_omitted($lineBreak)
    {
        $expected = array(new TimedText_Token_EndBetween);
        $this->assertEquals($expected, $this->lexar->tokenize("{/between}{$lineBreak}"));
    }

    /**
     * @test
     */
    public function tokenize_should_create_String_token_from_no_token_brace()
    {
        $expected = array(
            new TimedText_Token_String('{'),
            new TimedText_Token_String('notoken}'),
        );
        $this->assertEquals($expected, $this->lexar->tokenize('{notoken}'));
    }

    /**
     * @test
     */
    public function BeginBetween_token_should_have_before_and_after_property_correctly()
    {
        $afterDatetime   = '2000-01-01 00:00';
        $beforeDatetime  = '2001-01-01 00:00';
        $afterTimestamp  = strtotime($afterDatetime);
        $beforeTimestamp = strtotime($beforeDatetime);
        $token = new TimedText_Token_BeginBetween($afterDatetime, $beforeDatetime);
        $this->assertEquals(
            array(
                'after'  => $afterTimestamp,
                'before' => $beforeTimestamp,
            ),
            $token->getOptions()
        );
    }

    public function lineBreakProvider()
    {
        return array(
            array("\r\n"),
            array("\r"),
            array("\n"),
        );
    }
}

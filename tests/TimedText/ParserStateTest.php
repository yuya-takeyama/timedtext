<?php
require_once 'TimedText/ParserState.php';
require_once 'TimedText/Lexar.php';

class TimedText_ParserStateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getState_should_be_OUT_BLOCK_initially()
    {
        $state = new TimedText_ParserState;
        $this->assertEquals(TimedText_ParserState::OUT_BLOCK, $state->getState());
    }

    /**
     * @test
     */
    public function getState_should_be_IN_BEFORE_if_BeginBefore_Token_is_given()
    {
        $state = new TimedText_ParserState;
        $state->pushToken(new TimedText_Token_BeginBefore('2000-01-01 00:00'));
        $this->assertEquals(TimedText_ParserState::IN_BEFORE, $state->getState());
    }

    /**
     * @test
     */
    public function getState_should_be_IN_AFTER_if_BeginAfter_Token_is_given()
    {
        $state = new TimedText_ParserState;
        $state->pushToken(new TimedText_Token_BeginAfter('2000-01-01 00:00'));
        $this->assertEquals(TimedText_ParserState::IN_AFTER, $state->getState());
    }
}

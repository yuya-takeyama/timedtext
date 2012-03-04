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

    /**
     * @test
     * @dataProvider provideInvalidStateAndToken
     * @expectedException TimedText_Parser_Invalid_TokenException
     */
    public function pushToken_should_throw_InvalidTokenException_if_invalid_token_is_given($stateCode, $token)
    {
        $state = new TimedText_ParserState;
        $state->setState($stateCode);
        $state->pushToken($token);
    }

    public function provideInvalidStateAndToken()
    {
        return array(
            array(TimedText_ParserState::OUT_BLOCK, new TimedText_Token_EndBefore),
            array(TimedText_ParserState::OUT_BLOCK, new TimedText_Token_EndAfter),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_BeginBefore('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_BeginAfter('2000-01-01 00:00')),
        );
    }
}

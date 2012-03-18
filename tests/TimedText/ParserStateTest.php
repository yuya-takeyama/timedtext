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
     */
    public function getState_should_be_IN_BETWEEN_if_BeginBetween_Token_is_given()
    {
        $state = new TimedText_ParserState;
        $state->pushToken(new TimedText_Token_BeginBetween('2000-01-01 00:00', '2001-01-01 00:00'));
        $this->assertEquals(TimedText_ParserState::IN_BETWEEN, $state->getState());
    }

    /**
     * @test
     * @dataProvider provideInvalidStateAndToken
     * @expectedException TimedText_Parser_InvalidTokenException
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
            array(TimedText_ParserState::OUT_BLOCK, new TimedText_Token_EndBetween),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_BeginBefore('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_BeginAfter('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_EndAfter),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_BeginBetween('2000-01-01 00:00', '2001-01-01 00:00')),
            array(TimedText_ParserState::IN_BEFORE, new TimedText_Token_EndBetween),
            array(TimedText_ParserState::IN_AFTER, new TimedText_Token_BeginBefore('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_AFTER, new TimedText_Token_BeginAfter('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_AFTER, new TimedText_Token_EndBefore),
            array(TimedText_ParserState::IN_AFTER, new TimedText_Token_BeginBetween('2000-01-01 00:00', '2001-01-01 00:00')),
            array(TimedText_ParserState::IN_AFTER, new TimedText_Token_EndBetween),
            array(TimedText_ParserState::IN_BETWEEN, new TimedText_Token_BeginBefore('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_BETWEEN, new TimedText_Token_EndBefore),
            array(TimedText_ParserState::IN_BETWEEN, new TimedText_Token_BeginAfter('2000-01-01 00:00')),
            array(TimedText_ParserState::IN_BETWEEN, new TimedText_Token_EndAfter),
            array(TimedText_ParserState::IN_BETWEEN, new TimedText_Token_BeginBetween('2000-01-01 00:00', '2001-01-01 00:00')),
        );
    }

    /**
     * @test
     */
    public function getTextStack_should_be_pushed_string_as_token()
    {
        $state = new TimedText_ParserState;
        $state->pushToken(new TimedText_Token_String('foo'));
        $this->assertEquals(array('foo'), $state->getTextStack());
    }

    /**
     * @test
     */
    public function getTextStack_should_be_empty_array_if_flushed()
    {
        $state = new TimedText_ParserState;
        $state->pushTextStack('foo');
        $state->flushTextStack();
        $this->assertEquals(array(), $state->getTextStack());
    }

    /**
     * @test
     * @dataProvider provideInputTokensAndExpectedText
     */
    public function getText_should_be_Text_object_has_pushed_text($tokens, $expected)
    {
        $state = new TimedText_ParserState;
        foreach ($tokens as $token) {
            $state->pushToken($token);
        }
        $state->flushTextStack();
        $this->assertEquals($expected, $state->getText());
    }

    public function provideInputTokensAndExpectedText()
    {
        $data = array();
        $time = strtotime(date('Y-m-d H:i', time()));
        $datetime = date('Y-m-d H:i', $time);

        $tokens = array(new TimedText_Token_String('foo'));
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo'));
        $data[] = array($tokens, $expected);

        $tokens = array(
            new TimedText_Token_BeginBefore($datetime),
            new TimedText_Token_String('foo'),
            new TimedText_Token_EndBefore,
        );
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo', array('before' => $time)));
        $data[] = array($tokens, $expected);

        $tokens = array(
            new TimedText_Token_BeginAfter($datetime),
            new TimedText_Token_String('foo'),
            new TimedText_Token_EndAfter,
        );
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo', array('after' => $time)));
        $data[] = array($tokens, $expected);

        $tokens = array(
            new TimedText_Token_BeginBefore($datetime),
            new TimedText_Token_String('foo'),
            new TimedText_Token_EndBefore,
            new TimedText_Token_String('bar'),
        );
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo', array('before' => $time)));
        $expected->push(new TimedText_Section('bar'));
        $data[] = array($tokens, $expected);

        $tokens = array(
            new TimedText_Token_BeginAfter($datetime),
            new TimedText_Token_String('foo'),
            new TimedText_Token_EndAfter,
            new TimedText_Token_String('bar'),
        );
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo', array('after' => $time)));
        $expected->push(new TimedText_Section('bar'));
        $data[] = array($tokens, $expected);

        $tokens = array(
            new TimedText_Token_String('foo'),
            new TimedText_Token_BeginBefore($datetime),
            new TimedText_Token_String('bar'),
            new TimedText_Token_EndBefore,
        );
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo'));
        $expected->push(new TimedText_Section('bar', array('before' => $time)));
        $data[] = array($tokens, $expected);

        $tokens = array(
            new TimedText_Token_String('foo'),
            new TimedText_Token_BeginAfter($datetime),
            new TimedText_Token_String('bar'),
            new TimedText_Token_EndAfter,
        );
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section('foo'));
        $expected->push(new TimedText_Section('bar', array('after' => $time)));
        $data[] = array($tokens, $expected);

        return $data;
    }

    /**
     * @test
     * @dataProvider provideUnclosedTokens
     * @expectedException TimedText_Parser_UnclosedBlockException
     */
    public function finish_should_throw_UnclosedBlockException_if_unclosed_block_exists($tokens)
    {
        $state = new TimedText_ParserState;
        foreach ($tokens as $token) {
            $state->pushToken($token);
        }
        $state->finish();
    }

    public function provideUnclosedTokens()
    {
        return array(
            array(
                array(
                    new TimedText_Token_BeginBefore('2000-01-01 00:00'),
                    new TimedText_Token_String('foo'),
                ),
            ),
            array(
                array(
                    new TimedText_Token_BeginAfter('2000-01-01 00:00'),
                    new TimedText_Token_String('foo'),
                ),
            ),
        );
    }
}

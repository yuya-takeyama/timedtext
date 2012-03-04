<?php
require_once 'TimedText/ParserState.php';

/**
 * TimedText_Parser
 *
 * @author Yuya Takeyama
 */
class TimedText_Parser
{
    /**
     * @param  array<TimedText_Token> $tokens
     * @return TimedText_Text
     */
    public function parse($tokens)
    {
        $state = new TimedText_ParserState;
        foreach ($tokens as $token) {
            $state->pushToken($token);
        }
        $state->finish();
        return $state->getText();
    }
}

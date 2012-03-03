<?php
/**
 * TimedText_Parser
 *
 * @author Yuya Takeyama
 */
class TimedText_Parser
{
    /**
     * @param  string $text
     * @return TimedText_Text
     */
    public function parse($input)
    {
        $text = new TimedText_Text;
        $text->push(new TimedText_Section($input));
        return $text;
    }
}

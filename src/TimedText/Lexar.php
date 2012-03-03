<?php
/**
 * TimedText_Lexar
 *
 * @author Yuya Takeyama
 */
require_once 'TimedText/Text.php';

class TimedText_Token_String
{
    protected $_str;

    public function __construct($str)
    {
        $this->_str = $str;
    }
}

class TimedText_Token_BeginBefore
{
    protected $_time;

    public function __construct($datetime)
    {
        $this->_time = ($datetime);
    }
}

class TimedText_Token_EndBefore {}

class TimedText_Token_BeginAfter
{
    protected $_time;

    public function __construct($datetime)
    {
        $this->_time = strtotime($datetime);
    }
}

class TimedText_Token_EndAfter {}

class TimedText_TokenFactory
{
    const TYPE_STRING       = 0;
    const TYPE_BEGIN_BEFORE = 1;
    const TYPE_END_BEFORE   = 2;
    const TYPE_BEGIN_AFTER  = 3;
    const TYPE_END_AFTER    = 4;

    public function create($type, $value = NULL)
    {
        switch ($type)
        {
        case self::TYPE_STRING:
            return new TimedText_Token_String($value);
        case self::TYPE_BEGIN_BEFORE:
            return new TimedText_Token_BeginBefore($value);
        case self::TYPE_END_BEFORE:
            return new TimedText_Token_EndBefore;
        case self::TYPE_BEGIN_AFTER:
            return new TimedText_Token_BeginAfter($value);
        case self::TYPE_END_AFTER:
            return new TimedText_Token_EndAfter;
        default:
            throw new InvalidArgumentException("Undefined token type '{$type}' is specified.");
        }
    }
}

class TimedText_Lexar
{
    const EXPR_BEGIN_BEFORE = '/^\{before (\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2} \d{1,2}:\d{1,2})\}/';
    const EXPR_END_BEFORE   = '#^\{/before\}#';
    const EXPR_BEGIN_AFTER  = '/^\{after (\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2} \d{1,2}:\d{1,2})\}/';
    const EXPR_END_AFTER    = '#^\{/after\}#';
    const EXPR_STRING       = '/^([^{]+)/';
    const EXPR_BRACE        = '/^\{/';

    /**
     * @param  string $text
     * @return array<TimedText_TokenAbstract>
     */
    public function tokenize($input)
    {
        $factory = new TimedText_TokenFactory;
        $tokens = array();
        $pos = 0;
        $len = mb_strlen($input);
        while ($pos < $len) {
            if (preg_match(self::EXPR_BEGIN_BEFORE, $input, $matches)) {
                $tokens[] = $factory->create(
                    TimedText_TokenFactory::TYPE_BEGIN_BEFORE,
                    $matches[1]
                );
                $eatLen = mb_strlen($matches[0]);
            } else if (preg_match(self::EXPR_END_BEFORE, $input, $matches)) {
                $tokens[] = $factory->create(TimedText_TokenFactory::TYPE_END_BEFORE);
                $eatLen = mb_strlen($matches[0]);
            } else if (preg_match(self::EXPR_BEGIN_AFTER, $input, $matches)) {
                $tokens[] = $factory->create(
                    TimedText_TokenFactory::TYPE_BEGIN_AFTER,
                    $matches[1]
                );
                $eatLen = mb_strlen($matches[0]);
            } else if (preg_match(self::EXPR_END_AFTER, $input, $matches)) {
                $tokens[] = $factory->create(TimedText_TokenFactory::TYPE_END_AFTER);
                $eatLen = mb_strlen($matches[0]);
            } else if (preg_match(self::EXPR_STRING, $input, $matches)) {
                $tokens[] = $factory->create(TimedText_TokenFactory::TYPE_STRING, $matches[1]);
                $eatLen = mb_strlen($matches[0]);
            } else if (preg_match(self::EXPR_BRACE, $input, $matches)) {
                $tokens[] = $factory->create(TimedText_TokenFactory::TYPE_STRING, '{');
                $eatLen = 1;
            }
            $pos += $eatLen;
            $input = mb_substr($input, $eatLen);
        }
        return $tokens;
    }
}

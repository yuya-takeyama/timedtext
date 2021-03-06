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

    public function getString()
    {
        return $this->_str;
    }
}

class TimedText_Token_BeginBefore
{
    protected $_timestamp;

    public function __construct($datetime)
    {
        $this->_timestamp = strtotime($datetime);
    }

    public function getOptions()
    {
        return array(
            'before' => $this->_timestamp,
        );
    }
}

class TimedText_Token_EndBefore {}

class TimedText_Token_BeginAfter
{
    protected $_timestamp;

    public function __construct($datetime)
    {
        $this->_timestamp = strtotime($datetime);
    }

    public function getOptions()
    {
        return array(
            'after' => $this->_timestamp,
        );
    }
}

class TimedText_Token_EndAfter {}

class TimedText_Token_BeginBetween
{
    protected $_after;

    protected $_before;

    public function __construct($afterDatetime, $beforeDatetime)
    {
        $this->_after  = strtotime($afterDatetime);
        $this->_before = strtotime($beforeDatetime);
    }

    public function getOptions()
    {
        return array(
            'after'  => $this->_after,
            'before' => $this->_before,
        );
    }
}

class TimedText_Token_EndBetween {}

class TimedText_TokenFactory
{
    const TYPE_STRING       = 0;
    const TYPE_BEGIN_BEFORE = 1;
    const TYPE_END_BEFORE   = 2;
    const TYPE_BEGIN_AFTER  = 3;
    const TYPE_END_AFTER    = 4;
    const TYPE_BEGIN_BETWEEN = 5;
    const TYPE_END_BETWEEN  = 6;

    public function create($type, $value = NULL, $value2 = NULL)
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
        case self::TYPE_BEGIN_BETWEEN:
            return new TimedText_Token_BeginBetween($value, $value2);
        case self::TYPE_END_BETWEEN:
            return new TimedText_Token_EndBetween;
        default:
            throw new InvalidArgumentException("Undefined token type '{$type}' is specified.");
        }
    }
}

class TimedText_Lexar
{
    const EXPR_BEGIN_BEFORE = '/^\{before (\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2} \d{1,2}:\d{1,2})\}(?:\r\n|\r|\n)?/u';
    const EXPR_END_BEFORE   = '#^\{/before\}(?:\r\n|\r|\n)?#u';
    const EXPR_BEGIN_AFTER  = '/^\{after (\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2} \d{1,2}:\d{1,2})\}(?:\r\n|\r|\n)?/u';
    const EXPR_END_AFTER    = '#^\{/after\}(?:\r\n|\r|\n)?#u';
    const EXPR_BEGIN_BETWEEN  = '/^\{between (\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2} \d{1,2}:\d{1,2}) \- (\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2} \d{1,2}:\d{1,2})\}(?:\r\n|\r|\n)?/u';
    const EXPR_END_BETWEEN  = '#^\{/between\}(?:\r\n|\r|\n)?#u';
    const EXPR_STRING       = '/^([^{]+)/u';
    const EXPR_BRACE        = '/^\{/u';

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
            } else if (preg_match(self::EXPR_BEGIN_BETWEEN, $input, $matches)) {
                $tokens[] = $factory->create(
                    TimedText_TokenFactory::TYPE_BEGIN_BETWEEN,
                    $matches[1],
                    $matches[2]
                );
                $eatLen = mb_strlen($matches[0]);
            } else if (preg_match(self::EXPR_END_BETWEEN, $input, $matches)) {
                $tokens[] = $factory->create(TimedText_TokenFactory::TYPE_END_BETWEEN);
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

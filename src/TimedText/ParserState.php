<?php
require_once 'TimedText/Text.php';

class TimedText_Parser_Invalid_TokenException extends RuntimeException {}

class TimedText_ParserState
{
    const OUT_BLOCK = 0;
    const IN_BEFORE = 1;
    const IN_AFTER  = 2;

    protected $_textStack;

    protected $_text;

    protected $_currentBlockOptions;

    public function __construct()
    {
        $this->_state = self::OUT_BLOCK;
        $this->_text  = new TimedText_Text;
        $this->clearTextStack();
    }

    public function pushToken($token)
    {
        switch ($this->_state) {
        case self::OUT_BLOCK:
            $this->handleAsOutBlock($token);
            $this->setCurrentBlockOptions(array());
            break;
        case self::IN_BEFORE:
            $this->handleAsInBefore($token);
            break;
        case self::IN_AFTER:
            $this->handleAsInAfter($token);
            break;
        }
    }

    public function setState($state)
    {
        $this->_state = $state;
    }

    public function getState()
    {
        return $this->_state;
    }

    public function handleAsOutBlock($token)
    {
        if ($token instanceof TimedText_Token_String) {
            $this->pushTextStack($token->getString());
        } else if ($token instanceof TimedText_Token_BeginBefore) {
            $this->setState(self::IN_BEFORE);
            $this->setCurrentBlockOptions($token->getOptions());
        } else if ($token instanceof TimedText_Token_BeginAfter) {
            $this->setState(self::IN_AFTER);
            $this->setCurrentBlockOptions($token->getOptions());
        } else {
            throw new TimedText_Parser_Invalid_TokenException;
        }
    }

    public function handleAsInBefore($token)
    {
        if ($token instanceof TimedText_String) {
            $this->pushTextStack($token->getString());
        } else if ($token instanceof TimedText_Token_EndBefore) {
            $this->setState(self::OUT_BLOCK);
        } else {
            throw new TimedText_Parser_Invalid_TokenException;
        }
    }

    public function handleAsInAfter($token)
    {
        if ($token instanceof TimedText_String) {
            $this->pushTextStack($token->getString());
        } else if ($token instanceof TimedText_Token_EndAfter) {
            $this->setState(self::OUT_BLOCK);
        } else {
            throw new TimedText_Parser_Invalid_TokenException;
        }
    }

    public function pushTextStack($text)
    {
        $this->_textStack[] = $text;
    }

    public function hasTextStack()
    {
        return count($this->_textStack) > 0;
    }

    public function flushTextStack()
    {
        if ($this->hasTextStack()) {
            $this->_text->push(
                new TimedText_Section(
                    join('', $this->_textStack),
                    $this->getCurrentBlockOptions()
                )
            );
        }
    }

    public function finish()
    {
        $this->flushTextStack();
    }

    public function clearTextStack()
    {
        $this->_textStack = array();
    }

    public function setCurrentBlockOptions($options)
    {
        $this->_currentBlockOptions = $options;
    }

    public function getCurrentBlockOptions()
    {
        return $this->_currentBlockOptions;
    }

    public function getText()
    {
        return $this->_text;
    }
}

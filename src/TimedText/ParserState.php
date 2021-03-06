<?php
require_once 'TimedText/Text.php';
require_once 'TimedText/Section.php';

class TimedText_Parser_InvalidTokenException extends RuntimeException {}

class TimedText_Parser_UnclosedBlockException extends RuntimeException {}

class TimedText_ParserState
{
    const OUT_BLOCK = 0;
    const IN_BEFORE = 1;
    const IN_AFTER  = 2;
    const IN_BETWEEN = 3;

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
            break;
        case self::IN_BEFORE:
            $this->handleAsInBefore($token);
            break;
        case self::IN_AFTER:
            $this->handleAsInAfter($token);
            break;
        case self::IN_BETWEEN;
            $this->handleAsInBetween($token);
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
            $this->flushTextStack();
            $this->setState(self::IN_BEFORE);
            $this->setCurrentBlockOptions($token->getOptions());
        } else if ($token instanceof TimedText_Token_BeginAfter) {
            $this->flushTextStack();
            $this->setState(self::IN_AFTER);
            $this->setCurrentBlockOptions($token->getOptions());
        } else if ($token instanceof TimedText_Token_BeginBetween) {
            $this->flushTextStack();
            $this->setState(self::IN_BETWEEN);
            $this->setCurrentBlockOptions($token->getOptions());
        } else {
            $this->throwInvalidTokenException($token);
        }
    }

    public function handleAsInBefore($token)
    {
        if ($token instanceof TimedText_Token_String) {
            $this->pushTextStack($token->getString());
        } else if ($token instanceof TimedText_Token_EndBefore) {
            $this->flushTextStack();
            $this->setCurrentBlockOptions(array());
            $this->setState(self::OUT_BLOCK);
        } else {
            $this->throwInvalidTokenException($token);
        }
    }

    public function handleAsInAfter($token)
    {
        if ($token instanceof TimedText_Token_String) {
            $this->pushTextStack($token->getString());
        } else if ($token instanceof TimedText_Token_EndAfter) {
            $this->flushTextStack();
            $this->setCurrentBlockOptions(array());
            $this->setState(self::OUT_BLOCK);
        } else {
            $this->throwInvalidTokenException($token);
        }
    }

    public function handleAsInBetween($token)
    {
        if ($token instanceof TimedText_Token_String) {
            $this->pushTextStack($token->getString());
        } else if ($token instanceof TimedText_Token_EndBetween) {
            $this->flushTextStack();
            $this->setCurrentBlockOptions(array());
            $this->setState(self::OUT_BLOCK);
        } else {
            $this->throwInvalidTokenException($token);
        }
    }

    public function throwInvalidTokenException($token)
    {
        throw new TimedText_Parser_InvalidTokenException(
            'Invalid token ' . get_class($token) . ' detected in ' . $this->getStateAsString()
        );
    }

    public function pushTextStack($text)
    {
        $this->_textStack[] = $text;
    }

    public function getTextStack()
    {
        return $this->_textStack;
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
            $this->clearTextStack();
        }
    }

    public function finish()
    {
        if ($this->getState() !== self::OUT_BLOCK) {
            throw new TimedText_Parser_UnclosedBlockException(
                'Unclosed block ' . $this->getStateAsString() . ' is detected.'
            );
        }
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

    public function getStateAsString()
    {
        $map = array(
            self::OUT_BLOCK => 'OUT_BLOCK',
            self::IN_BEFORE => 'IN_BEFORE',
            self::IN_AFTER  => 'IN_AFTER',
            self::IN_BETWEEN => 'IN_BETWEEN',
        );
        return $map[$this->_state];
    }
}

<?php
/**
 * TimedText_Section
 *
 * Section has before/after timestamps,
 * and knows whether it's visible or not.
 *
 * @author Yuya Takeyama
 */
class TimedText_Section
{
    /**
     * Text the section has.
     *
     * @var string
     */
    protected $_text;

    /**
     * Timestamp.
     *
     * @var int
     */
    protected $_before;

    /**
     * Timestamp.
     *
     * @var int
     */
    protected $_after;

    /**
     * Constructor.
     *
     * @param string $text
     * @param array  $options
     *               int before timestamp
     *               int after  timestamp
     */
    public function __construct($text, $options = array())
    {
        $this->_text = $text;
        if (isset($options['before'])) {
            $this->_before = $options['before'];
        }
        if (isset($options['after'])) {
            $this->_after = $options['after'];
        }
    }

    /**
     * Whether the section has before property.
     *
     * @return bool
     */
    public function hasBefore()
    {
        return isset($this->_before);
    }

    /**
     * Whether the section has after property.
     *
     * @return bool
     */
    public function hasAfter()
    {
        return isset($this->_after);
    }

    /**
     * Whether the section is visible.
     *
     * @param  int $current Current timestamp.
     * @return bool
     */
    public function isVisible($current = NULL)
    {
        if (is_null($current)) {
            $current = time();
        }
        if ($this->hasBefore() && $current >= $this->_before) {
            return false;
        }
        if ($this->hasAfter() && $current < $this->_after) {
            return false;
        }
        return true;
    }
}

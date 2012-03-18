<?php
/**
 * TimedText_Text
 *
 * Set of TimedText_Section
 *
 * @author Yuya Takeyama
 */
class TimedText_Text implements IteratorAggregate
{
    /**
     * Sections.
     *
     * @var array<TimedText_Section>
     */
    protected $_sections = array();

    /**
     * String cast hook.
     *
     * @return string
     */
    public function __toString()
    {
        $result = '';
        foreach ($this->_sections as $section) {
            if ($section->isVisible()) {
                $result .= (string)$section;
            }
        }
        return $result;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_sections);
    }

    /**
     * Pushes a section.
     *
     * @param  TimedText_Section $section
     * @return void
     */
    public function push(TimedText_Section $section)
    {
        $this->_sections[] = $section;
    }
}

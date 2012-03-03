<?php
require_once 'TimedText/Text.php';
require_once 'TimedText/Section.php';

class TimedText_TextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_be_concatnated_section_if_it_is_casted()
    {
        $text = new TimedText_Text;
        $text->push($this->createVisibleSection('foo'));
        $text->push($this->createVisibleSection('bar'));
        $this->assertEquals('foobar', (string)$text);
    }

    /**
     * @test
     */
    public function only_visible_section_should_be_returned()
    {
        $text = new TimedText_Text;
        $text->push($this->createVisibleSection('foo'));
        $text->push($this->createInvisibleSection('bar'));
        $this->assertEquals('foo', (string)$text);
    }

    /**
     * Creates visible section.
     *
     * @param  string $text
     * @return TimedText_Section
     */
    protected function createVisibleSection($text)
    {
        return $this->createSectionByTextAndVisiblity($text, true);
    }

    /**
     * Creates invisible section.
     *
     * @param  string $text
     * @return TimedText_Section
     */
    protected function createInvisibleSection($text)
    {
        return $this->createSectionByTextAndVisiblity($text, false);
    }

    protected function createSectionByTextAndVisiblity($text, $visiblity)
    {
        $section = $this->getMock(
            'TimedText_Section',
            array('isVisible'),
            array($text)
        );
        $section->expects($this->any())
            ->method('isVisible')
            ->will($this->returnValue($visiblity));
        return $section;
    }
}

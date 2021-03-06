<?php
require_once 'TimedText/Section.php';

class TimedText_SectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function hasBefore_should_be_true_if_before_is_set()
    {
        $section = new TimedText_Section('foo', array('before' => time()));
        $this->assertTrue($section->hasBefore());
    }

    /**
     * @test
     */
    public function hasBefore_should_be_false_if_before_is_not_set()
    {
        $section = new TimedText_Section('foo');
        $this->assertFalse($section->hasBefore());
    }

    /**
     * @test
     */
    public function getBefore_should_be_timestamp_it_is_set_on_constructor()
    {
        $time = time();
        $section = new TimedText_Section('foo', array('before' => $time));
        $this->assertEquals($time, $section->getBefore());
    }

    /**
     * @test
     */
    public function hasAfter_should_be_true_if_after_is_set()
    {
        $section = new TimedText_Section('foo', array('after' => time()));
        $this->assertTrue($section->hasAfter());
    }

    /**
     * @test
     */
    public function hasAfter_should_be_false_if_after_is_not_set()
    {
        $section = new TimedText_Section('foo');
        $this->assertFalse($section->hasAfter());
    }

    /**
     * @test
     */
    public function getAfter_should_be_timestamp_it_is_set_on_constructor()
    {
        $time = time();
        $section = new TimedText_Section('foo', array('after' => $time));
        $this->assertEquals($time, $section->getAfter());
    }

    /**
     * @test
     */
    public function isVisible_should_be_true_if_both_befor_and_after_is_not_set()
    {
        $section = new TimedText_Section('foo');
        $this->assertTrue($section->isVisible(time()));
    }

    /**
     * @test
     */
    public function isVisible_should_be_true_if_current_is_earlier_than_before_property()
    {
        $before  = time();
        $current = $before - 1;
        $section = new TimedText_Section('foo', array('before' => $before));
        $this->assertTrue($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_false_if_current_is_equal_to_before_property()
    {
        $current = $before = time();
        $section = new TimedText_Section('foo', array('before' => $before));
        $this->assertFalse($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_false_if_current_is_later_than_before_property()
    {
        $before  = time();
        $current = $before + 1;
        $section = new TimedText_Section('foo', array('before' => $before));
        $this->assertFalse($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_false_if_current_is_earlier_than_after_property()
    {
        $after   = time();
        $current = $after - 1;
        $section = new TimedText_Section('foo', array('after' => $after));
        $this->assertFalse($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_true_if_current_is_equal_to_after_property()
    {
        $after = $current = time();
        $section = new TimedText_Section('foo', array('after' => $after));
        $this->assertTrue($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_true_if_current_is_later_than_after_property()
    {
        $after   = time();
        $current = $after + 1;
        $section = new TimedText_Section('foo', array('after' => $after));
        $this->assertTrue($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_true_both_before_and_after_condition_is_met()
    {
        $current = $after = time();
        $before  = $current + 1;
        $section = new TimedText_Section('foo', array('before' => $before, 'after' => $after));
        $this->assertTrue($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_false_if_only_before_condition_is_not_met()
    {
        $current = $before = $after = time();
        $section = new TimedText_Section('foo', array('before' => $before, 'after' => $after));
        $this->assertFalse($section->isVisible($current));
    }

    /**
     * @test
     */
    public function isVisible_should_be_false_if_only_after_condition_is_not_met()
    {
        $current = time();
        $before  = $after = $current + 1;
        $section = new TimedText_Section('foo', array('before' => $before, 'after' => $after));
        $this->assertFalse($section->isVisible($current));
    }

    /**
     * @test
     */
    public function it_should_be_its_text_if_section_is_casted()
    {
        $section = new TimedText_Section('foo');
        $this->assertEquals('foo', (string)$section);
    }
}

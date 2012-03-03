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
}

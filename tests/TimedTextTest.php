<?php
require_once 'TimedText.php';

class TimedTextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function convert_should_work_correctly()
    {
        $before = strtotime("2000-01-01 00:00");
        $after  = strtotime("2001-01-01 00:00");
        $input  = "Beginning\n" .
                  "{before 2000-01-01 00:00}\n" .
                  "Before block\n" .
                  "{/before}\n" .
                  "{between 2000-01-01 00:00 - 2001-01-01 00:00}\n" .
                  "Between block\n" .
                  "{/between}" .
                  "{after 2001-01-01 00:00}\n" .
                  "After block\n" .
                  "{/after}\n" .
                  "Out of block";
        $expected = new TimedText_Text;
        $expected->push(new TimedText_Section("Beginning\n"));
        $expected->push(new TimedText_Section("Before block\n", array(
            'before' => $before,
        )));
        $expected->push(new TimedText_Section("Between block\n", array(
            'before' => $before,
            'after'  => $after,
        )));
        $expected->push(new TimedText_Section("After block\n", array(
            'after'  => $after,
        )));
        $expected->push(new TimedText_Section('Out of block'));
        $this->assertEquals($expected, TimedText::convert($input));
    }
}

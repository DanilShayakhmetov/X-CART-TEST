<?php


namespace TijsVerkoyen\CssToInlineStyles\Tests\Css;

use TijsVerkoyen\CssToInlineStyles\Css\Processor;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Processor
     */
    protected $processor;

    public function setUp()
    {
        $this->processor = new Processor();
    }

    public function tearDown()
    {
        $this->processor = null;
    }

    public function testCssWithOneRule()
    {
        $css = <<<EOF
            a {
                padding: 5px;
                display: block;
            }
EOF;

        $rules = $this->processor->getRules($css);

        $this->assertCount(1, $rules);
        $this->assertInstanceOf('TijsVerkoyen\CssToInlineStyles\Css\Rule\Rule', $rules[0]);
        $this->assertEquals('a', $rules[0]->getSelector());
        $this->assertCount(2, $rules[0]->getProperties());
        $this->assertEquals('padding', $rules[0]->getProperties()[0]->getName());
        $this->assertEquals('5px', $rules[0]->getProperties()[0]->getValue());
        $this->assertEquals('display', $rules[0]->getProperties()[1]->getName());
        $this->assertEquals('block', $rules[0]->getProperties()[1]->getValue());
        $this->assertEquals(1, $rules[0]->getOrder());
    }

    public function testCssWithMediaQueries()
    {
        $css = <<<EOF
@media (max-width: 600px) {
    a {
        color: green;
    }
}

a {
  color: red;
}
EOF;

        $rules = $this->processor->getRules($css);

        $this->assertCount(1, $rules);
        $this->assertInstanceOf('TijsVerkoyen\CssToInlineStyles\Css\Rule\Rule', $rules[0]);
        $this->assertEquals('a', $rules[0]->getSelector());
        $this->assertCount(1, $rules[0]->getProperties());
        $this->assertEquals('color', $rules[0]->getProperties()[0]->getName());
        $this->assertEquals('red', $rules[0]->getProperties()[0]->getValue());
        $this->assertEquals(1, $rules[0]->getOrder());
    }

    public function testMakeSureMediaQueriesAreRemoved()
    {
        $css = '@media tv and (min-width: 700px) and (orientation: landscape) {.foo {display: none;}}';
        $this->assertEmpty($this->processor->getRules($css));

        $css = '@media (min-width: 700px), handheld and (orientation: landscape) {.foo {display: none;}}';
        $this->assertEmpty($this->processor->getRules($css));

        $css = '@media not screen and (color), print and (color)';
        $this->assertEmpty($this->processor->getRules($css));

        $css = '@media screen and (min-aspect-ratio: 1/1) {.foo {display: none;}}';
        $this->assertEmpty($this->processor->getRules($css));

        $css = '@media screen and (device-aspect-ratio: 16/9), screen and (device-aspect-ratio: 16/10) {.foo {display: none;}}';
        $this->assertEmpty($this->processor->getRules($css));
    }

    public function testSimpleStyleTagsInHtml()
    {
        $expected = 'p { color: #F00; }' . "\n";
        $this->assertEquals(
            $expected,
            $this->processor->getCssFromStyleTags(
                <<<EOF
                    <html>
    <head>
        <style>
            p { color: #F00; }
        </style>
    </head>
    <body>
        <p>foo</p>
    </body>
    </html>
EOF
            )
        );
    }

    public function testMultipleStyleTagsInHtml()
    {
        $expected = 'p { color: #F00; }' . "\n" . 'p { color: #0F0; }' . "\n";
        $this->assertEquals(
            $expected,
            $this->processor->getCssFromStyleTags(
                <<<EOF
                    <html>
    <head>
        <style>
            p { color: #F00; }
        </style>
    </head>
    <body>
        <style>
            p { color: #0F0; }
        </style>
        <p>foo</p>
    </body>
    </html>
EOF
            )
        );
    }
}

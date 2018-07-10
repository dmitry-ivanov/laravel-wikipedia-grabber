<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Formatter\BootstrapFormatter;
use Illuminated\Wikipedia\Grabber\Formatter\BulmaFormatter;
use Illuminated\Wikipedia\Grabber\Formatter\Formatter;
use Illuminated\Wikipedia\Grabber\Formatter\PlainFormatter;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class FormatterTest extends TestCase
{
    /** @test */
    public function it_has_static_factory_method_which_returns_plain_formatter_by_default()
    {
        $this->assertInstanceOf(PlainFormatter::class, Formatter::factory('foobar', collect()));
    }

    /** @test */
    public function and_it_returns_plain_formatter_if_asked()
    {
        $this->assertInstanceOf(PlainFormatter::class, Formatter::factory('plain', collect()));
    }

    /** @test */
    public function and_it_returns_bulma_formatter_if_asked()
    {
        $this->assertInstanceOf(BulmaFormatter::class, Formatter::factory('bulma', collect()));
    }

    /** @test */
    public function and_it_returns_bootstrap_formatter_if_asked()
    {
        $this->assertInstanceOf(BootstrapFormatter::class, Formatter::factory('bootstrap', collect()));
    }
}

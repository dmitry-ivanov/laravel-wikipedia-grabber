<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Formatter\BootstrapFormatter;
use Illuminated\Wikipedia\Grabber\Formatter\BulmaFormatter;
use Illuminated\Wikipedia\Grabber\Formatter\Formatter;
use Illuminated\Wikipedia\Grabber\Formatter\PlainFormatter;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FormatterTest extends TestCase
{
    #[Test]
    public function it_has_static_factory_method_which_returns_plain_formatter_by_default(): void
    {
        $this->assertInstanceOf(PlainFormatter::class, Formatter::factory('foobar', collect()));
    }

    #[Test]
    public function and_it_returns_plain_formatter_if_asked(): void
    {
        $this->assertInstanceOf(PlainFormatter::class, Formatter::factory('plain', collect()));
    }

    #[Test]
    public function and_it_returns_bulma_formatter_if_asked(): void
    {
        $this->assertInstanceOf(BulmaFormatter::class, Formatter::factory('bulma', collect()));
    }

    #[Test]
    public function and_it_returns_bootstrap_formatter_if_asked(): void
    {
        $this->assertInstanceOf(BootstrapFormatter::class, Formatter::factory('bootstrap', collect()));
    }
}

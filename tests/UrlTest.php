<?php

namespace Astrotomic\PhpWeservImages\Tests;

use Astrotomic\Weserv\Images\Enums\Fit;
use Astrotomic\Weserv\Images\Url;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    /** @test */
    public function it_can_build_without_options(): void
    {
        $url = new Url('https://example.com/image.jpg');

        static::assertInstanceOf(Url::class, $url);
        static::assertSame('https://images.weserv.nl?url=https%3A%2F%2Fexample.com%2Fimage.jpg', (string) $url);
    }

    /** @test */
    public function it_can_build_with_custom_base_url(): void
    {
        $url = new Url('https://example.com/image.jpg', 'https://images.astrotomic.info');

        static::assertInstanceOf(Url::class, $url);
        static::assertSame('https://images.astrotomic.info?url=https%3A%2F%2Fexample.com%2Fimage.jpg', (string) $url);
    }

    /** @test */
    public function it_can_build_with_custom_dimensions(): void
    {
        $url = new Url('https://example.com/image.jpg');
        $url->w(1920)->h(1080)->fit(Fit::CONTAIN)->we();

        static::assertInstanceOf(Url::class, $url);
        static::assertSame('https://images.weserv.nl?w=1920&h=1080&fit=contain&we=1&url=https%3A%2F%2Fexample.com%2Fimage.jpg', (string) $url);
    }

    /** @test */
    public function it_can_transform_to_img_tag(): void
    {
        $url = new Url('https://example.com/image.jpg');

        static::assertInstanceOf(Url::class, $url);
        static::assertSame('<img src="https://images.weserv.nl?url=https%3A%2F%2Fexample.com%2Fimage.jpg" />', $url->toImg());
    }

    /** @test */
    public function it_can_transform_to_picture_tag(): void
    {
        $url = new Url('https://example.com/image.jpg');

        static::assertInstanceOf(Url::class, $url);
        static::assertSame(
            <<<'HTML'
            <picture>
                <source type="image/webp" srcset="https://images.weserv.nl?output=webp&dpr=1&url=https%3A%2F%2Fexample.com%2Fimage.jpg 1x, https://images.weserv.nl?output=webp&dpr=2&url=https%3A%2F%2Fexample.com%2Fimage.jpg 2x" />
                <img alt="My cool avatar" class="avatar" src="https://images.weserv.nl?url=https%3A%2F%2Fexample.com%2Fimage.jpg" srcset="https://images.weserv.nl?dpr=1&url=https%3A%2F%2Fexample.com%2Fimage.jpg 1x, https://images.weserv.nl?dpr=2&url=https%3A%2F%2Fexample.com%2Fimage.jpg 2x" />
            </picture>
            HTML,
            $url->toPicture([
                'alt' => 'My cool avatar',
                'class' => 'avatar',
            ], [
                '1x' => fn (Url $url) => $url->dpr(1),
                '2x' => fn (Url $url) => $url->dpr(2),
            ])
        );
    }
}

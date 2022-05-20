<?php

namespace RichanFongdasen\Glide\Tests\Features;

use RichanFongdasen\Glide\Facade\Glide;
use RichanFongdasen\Glide\Tests\TestCase;

class GenerateUrlTest extends TestCase
{
    /** @test */
    public function it_can_generate_the_asset_url_correctly(): void
    {
        $expected = 'http://localhost/assets/dummy-directory/dummy-image.jpg?s=8d91851982cf9d34726f0f8b32ab0206&sharp=15&w=800';

        self::assertEquals($expected, Glide::url('dummy-directory/dummy-image.jpg', ['w' => 800, 'sharp' => 15]));
    }
}

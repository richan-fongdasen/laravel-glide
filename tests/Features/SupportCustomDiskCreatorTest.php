<?php

namespace RichanFongdasen\Glide\Tests\Features;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\Glide\Exceptions\FilesystemException;
use RichanFongdasen\Glide\Facade\Glide;
use RichanFongdasen\Glide\Tests\TestCase;

class SupportCustomDiskCreatorTest extends TestCase
{
    #[Test]
    public function it_supports_custom_disk_creator_and_throw_exception_when_the_created_disk_is_invalid(): void
    {
        $this->expectException(FilesystemException::class);

        Glide::setDisk('my-own-disk', function () {
            return Storage::disk('public')->getDriver();
        });

        Glide::getDisk('my-own-disk');
    }
}

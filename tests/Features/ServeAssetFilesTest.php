<?php

namespace RichanFongdasen\Glide\Tests\Features;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use RichanFongdasen\Glide\Tests\TestCase;

class ServeAssetFilesTest extends TestCase
{
    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
    }

    /** @test */
    public function it_will_produce_http_403_error_on_invalid_signature_token(): void
    {
        $this->get('/assets/not-found-image.jpg?s=33b636019c245a9930f957ef86c929de&w=300')
            ->assertForbidden();
    }

    /** @test */
    public function it_will_produce_http_404_error_on_invalid_asset_url(): void
    {
        $this->get('/assets/not-found-image.jpg?s=33b636019c245a9930f957ef86c927de&w=300')
            ->assertNotFound();
    }

    /** @test */
    public function it_will_produce_http_403_error_when_trying_to_manipulate_non_image_file(): void
    {
        $dummy = new File(dirname(__DIR__, 2).'/dummies/document.pdf');
        Storage::disk('public')->put('document/super-important.pdf', $dummy->getContent());

        $this->get('/assets/document/super-important.pdf?s=6de24c05ea83f03d0b71aedb9d0c266d&sharp=10&w=800')
            ->assertForbidden();
    }

    /** @test */
    public function it_will_stream_non_image_file_directly(): void
    {
        $dummy = new File(dirname(__DIR__, 2).'/dummies/document.pdf');
        Storage::disk('public')->put('document/super-important.pdf', $dummy->getContent());

        $this->get('/assets/document/super-important.pdf?s=8ff550c7dcb99792d3a56569c0c5b14d')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function it_can_process_glide_request_as_expected(): void
    {
        $dummy = new File(dirname(__DIR__, 2).'/dummies/image.jpg');
        Storage::disk('public')->put('images/hyuna.jpg', $dummy->getContent());

        $this->get('/assets/images/hyuna.jpg?s=693afe8ba38f60b6ca5e42e7cf5a5bb6&sharp=10&w=700')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }
}

<?php

namespace Tests\Unit\Services;

use App\Services\UrlShortenerService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UrlShortenerServiceTest extends TestCase
{
    protected $service;
    protected $testConfig;

    protected function setUp(): void
    {
        parent::setUp();

        // Prepare a default configuration
        $this->testConfig = [
            'short_url_base' => 'http://short.url/',
            'features' => [
                'url_encode_validation' => true,
                'url_decode_validation' => true,
                'supported_url_protocols' => ['http://', 'https://'],
                'is_url_reachable' => true,
                'url_max_length' => 2000,
                'duplicate_check_expiry_time_in_seconds' => 30,
                'logging' => true,
                'log_slow_process' => true,
                'slow_process_time_in_seconds' => 1
            ]
        ];

        // Create service with test configuration
        $this->service = new UrlShortenerService($this->testConfig);
    }
    
    #[Test]
    public function it_handles_urls_without_protocol()
    {
        $result = $this->service->validateUrl('example.com', 'encode');
        if( $this->testConfig['features']['url_encode_validation'] ){
            $this->assertFalse($result['status'], (isset($result['msg'])?$result['msg']:''));
        }else{
            $this->assertTrue($result['status'], (isset($result['msg'])?$result['msg']:''));
        }
    }

    #[Test]
    public function it_validates_url_with_valid_http_url()
    {
        $result = $this->service->validateUrl('http://example.com', 'encode');
        $this->assertTrue($result['status']);
    }

    #[Test]
    public function it_validates_url_with_https_url()
    {
        $result = $this->service->validateUrl('https://example.com');
        $this->assertTrue($result['status']);
    }

    #[Test]
    public function it_fails_url_validation_with_invalid_url_format()
    {
        $result = $this->service->validateUrl('not a valid url');
        $this->assertFalse($result['status']);
        $this->assertEquals('Invalid URL format', $result['msg']);
    }

    #[Test]
    public function it_fails_url_validation_with_unsupported_protocol()
    {
        $result = $this->service->validateUrl('ftp://example.com');
        $this->assertFalse($result['status']);
        $this->assertStringContainsString('Invalid URL protocol', $result['msg']);
    }

    #[Test]
    public function it_validates_url_reachability()
    {
        Http::fake([
            'example.com' => Http::response('OK', 200)
        ]);

        $result = $this->service->validateUrl('http://example.com');
        $this->assertTrue($result['status']);
    }

    #[Test]
    public function it_fails_url_validation_for_unreachable_url()
    {
        Http::fake([
            'example.com' => Http::response('Not Found', 404)
        ]);

        $result = $this->service->validateUrl('http://example.com');
        $this->assertFalse($result['status']);
        $this->assertEquals('URL is unreachable (01)', $result['msg']);
    }

    #[Test]
    public function it_validates_url_length()
    {
        $longUrl = 'http://example.com/?' . str_repeat('a', intval($this->testConfig['features']['url_max_length']) + 1 );
        $result = $this->service->validateUrl($longUrl);
        $this->assertFalse($result['status']);
        $this->assertStringContainsString('URL exceeds the maximum supported length', $result['msg']);
    }

    #[Test]
    public function it_encodes_url_and_generates_unique_short_code()
    {
        $originalUrl = 'http://example.com';
        $result = $this->service->encodeUrl($originalUrl);

        $this->assertArrayHasKey('original_url', $result);
        $this->assertArrayHasKey('short_url', $result);
        $this->assertEquals($originalUrl, $result['original_url']);
        $this->assertStringStartsWith('http://short.url/', $result['short_url']);
        $this->assertEquals(6, strlen(str_replace('http://short.url/', '', $result['short_url'])));
    }

    #[Test]
    public function it_returns_existing_short_code_for_duplicate_url()
    {
        $originalUrl = 'http://example.com';
        $firstResult = $this->service->encodeUrl($originalUrl);
        $secondResult = $this->service->encodeUrl($originalUrl);

        $this->assertEquals($firstResult['short_url'], $secondResult['short_url']);
    }

    #[Test]
    public function it_decodes_url_successfully()
    {
        $originalUrl = 'http://example.com';
        $encodedResult = $this->service->encodeUrl($originalUrl);
        $decodedResult = $this->service->decodeUrl($encodedResult['short_url']);

        $this->assertNotNull($decodedResult);
        $this->assertEquals($originalUrl, $decodedResult['original_url']);
        $this->assertEquals($encodedResult['short_url'], $decodedResult['short_url']);
    }

    #[Test]
    public function it_returns_null_for_non_existent_short_url()
    {
        $result = $this->service->decodeUrl('http://short.url/nonexistent');
        $this->assertNull($result['original_url']);
    }

    #[Test]
    public function it_base62_encodes_data_correctly()
    {
        $reflection = new \ReflectionClass(UrlShortenerService::class);
        $method = $reflection->getMethod('base62_encode');
        $method->setAccessible(true);

        $input = 'test';
        $encoded = $method->invokeArgs($this->service, [$input]);

        $this->assertIsString($encoded);
        $this->assertNotEmpty($encoded);
    }
}
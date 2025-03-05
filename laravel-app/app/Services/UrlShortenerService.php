<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;

class UrlShortenerService
{
    protected $storageFile;
    protected $config;
    protected $currentTime;

    public function __construct($config = null)
    {
        $this->config = $config ?? config('url_shortener');
        $this->storageFile = storage_path('app/url_mappings.json');
        $this->currentTime = Carbon::now()->format('U');
    }

    public function validateUrl($url, $action='encode')
    {
        $url = trim($url);

        // Test existence of base URL
        if( ! ( isset($this->config['short_url_base']) && $this->config['short_url_base'] ) ){
            return ['status' => false, 'msg' => 'Invalid Server Settings. Base URL is undefined'];
        }

        // URL format validation
        if ( ($this->config['features']['url_encode_validation'] && $action == 'encode' ) || 
            ( $this->config['features']['url_decode_validation'] && $action == 'decode' ) ) {
            
            // Basic URL validation
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return ['status' => false, 'msg' => 'Invalid URL format'];
            }

            if ( $action == 'encode' && $this->config['features']['supported_url_protocols'] ){
                $supportedProtocols = $this->config['features']['supported_url_protocols'];

                $urlProtocol = parse_url($url, PHP_URL_SCHEME);

                if (!$urlProtocol) {
                    // If the URL does not have a protocol, assume 'http://' by default
                    $url = 'http://' . $url;
                    $urlProtocol = 'http';
                }

                // Validate if the URL protocol is supported
                if ( ! in_array($urlProtocol . '://', $supportedProtocols) ) {
                    return ['status' => false, 'msg' => 'Invalid URL protocol. Supported protocols are: ' . implode(', ', $supportedProtocols)];
                }
            }

            // Optional URL reachability check before encoding
            if ( $action == 'encode' && $this->config['features']['url_encode_validation'] && 
            $this->config['features']['is_url_reachable']) {
                try {
                    $response = Http::timeout(5)->get($url);
                    if( ! $response->successful() ){
                        return ['status' => false, 'msg' => 'URL is unreachable (01)'];
                    }
                } catch (Exception $e) {
                    return ['status' => false, 'msg' => 'URL is unreachable (02)'];
                }
            }

            // Ensure Shortened URL matches the supported format before decoding
            if ( $action == 'decode' && $this->config['features']['url_decode_validation'] ) {
                
                if ( strpos(strtolower($url), strtolower($this->config['short_url_base']) ) !== 0) {
                    return ['status' => false, 'msg' => 'Invalid shortened URL. URL does not match the supported base format.'];
                }
            }

            // Optional URL length validation
            if ($this->config['features']['url_max_length'] && intval($this->config['features']['url_max_length']) > 0 && strlen($url) > intval($this->config['features']['url_max_length'])) {
                return ['status' => false, 'msg' => 'URL exceeds the maximum supported length of ' . number_format( intval($this->config['features']['url_max_length']), 0 ) . ' characters'];
            }
        }

        return ['status' => true];
    }

    public function encodeUrl($originalUrl)
    {
        // Check for existing mapping
        $existingMapping = $this->findExistingMapping($originalUrl);
        if ($existingMapping) {
            return [
                'original_url' => $originalUrl,
                'short_url' => $this->config['short_url_base'] . $existingMapping
            ];
        }

        // Generate unique short code
        $shortCode = $this->generateUniqueShortCode();

        // Store mapping
        $this->storeShortCode($originalUrl, $shortCode);

        // Log encoding
        $this->logEncoding($originalUrl, $shortCode);

        return [
            'original_url' => $originalUrl,
            'short_url' => $this->config['short_url_base'] . $shortCode
        ];
    }

    public function decodeUrl($shortUrl)
    {
        // Validate decoded URL if enabled
        if ($this->config['features']['url_decode_validation']) {
            $isValidURL = $this->validateUrl($shortUrl, 'decode');
        }else{
            $isValidURL = ['status' => True];
        }

        $originalUrl = null;

        if ( isset($isValidURL['status']) && $isValidURL['status'] ){
            $shortCode = str_replace($this->config['short_url_base'], '', $shortUrl);
            $shortCodeData = $this->getURLFromShortCode( str_replace('/', '', $shortCode) );
            if( isset($shortCodeData['url']) ){
                $originalUrl = $shortCodeData['url'];
            }
        }

        // Log decoding
        //$this->logDecoding($shortUrl, $originalUrl);

        return [
            'short_url' => $shortUrl,
            'original_url' => $originalUrl
        ];
    }

    protected function generateUniqueShortCode()
    {
        $file = null;
        $shortCode = 0;

        // Ensure unique and 6 characters long
        while ( $file == null || ( $file && file_exists($file) ) || strlen($shortCode) !== 6) {
            // Generate a random binary string of the desired length
            $randomBytes = random_bytes(4);
            // Convert serial number to base64 encoding
            $shortCode = $this->base62_encode($randomBytes);
            $file = storage_path('app/'.md5( $shortCode ).'.json');
        }

        return $shortCode;
    }

    protected function base62_encode($data) {
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $base = strlen($alphabet);
        $encoded = '';
    
        // Convert string to a number using its ASCII values
        $data = unpack('C*', $data); // Convert string to array of bytes (ASCII)
        $num = 0;
        foreach ($data as $byte) {
            $num = $num * 256 + $byte;  // Accumulate the value to a big number
        }
    
        // Base62 encoding
        while ($num > 0) {
            $encoded = $alphabet[$num % $base] . $encoded;
            $num = floor($num / $base);
        }
    
        return $encoded ?: '0';
    }

    protected function storeShortCode($originalUrl, $shortCode)
    {
        $file = storage_path('app/'.md5( $shortCode ).'.json');

        $mappings = [];
        $mappings['short_code'] = $shortCode;
        $mappings['url'] = $originalUrl;
        $mappings['date'] = $this->currentTime;
        file_put_contents($file, json_encode($mappings));

        unset($mappings['url']);

        $this->storeMapping([ $this->getMappingURL($originalUrl) => $mappings ], 0);
    }

    protected function getURLFromShortCode($shortCode)
    {
        $mappings = [];

        $file = storage_path('app/'.md5( $shortCode ).'.json');
        if ( file_exists($file)) {
            $mappings = json_decode( file_get_contents($file), 1 );
        }
        return $mappings;
    }

    protected function storeMapping($mappings = [], $overwrite = 1)
    {
        if( ! $overwrite ){
            if( $mappings ){
                $prevMappings = $this->getMappings();
                $mappings = array_merge($prevMappings, $mappings);
            }else{
                return;
            }
        }
        file_put_contents($this->storageFile, json_encode($mappings));
    }

    protected function getMappings()
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        return json_decode(file_get_contents($this->storageFile), true) ?? [];
    }

    protected function getMappingURL($originalUrl){
        return md5( strtolower( trim($originalUrl) ) );
    }

    protected function findExistingMapping($originalUrl)
    {
        if ( intval($this->config['features']['duplicate_check_expiry_time_in_seconds']) > 0 ) {
            $expiryTime = intval($this->config['features']['duplicate_check_expiry_time_in_seconds']);
            $mappings = $this->getMappings();
            $hashedURL = $this->getMappingURL($originalUrl);
            $shortCode = null;
            if( isset( $mappings[$hashedURL]['date'] ) && $mappings[$hashedURL]['date'] + $expiryTime >= $this->currentTime ){
                $shortCode = $mappings[$hashedURL]['short_code'];
                $mappings[$hashedURL]['date'] = $this->currentTime;
            }

            //House keeping: clear expired mappings
            if($mappings){
                foreach( $mappings as $k => $v ){
                    if( $v["date"] + $expiryTime < $this->currentTime ){
                        unset( $mappings[$k] );
                    }
                }
                $this->storeMapping($mappings, 1);
            }

            return $shortCode;
        }
    }

    protected function logEncoding($originalUrl, $shortCode)
    {
        if ($this->config['features']['logging']) {
            $startTime = microtime(true);

            Log::info('URL Encoded', [
                'original_url' => $originalUrl,
                'short_code' => $shortCode,
                'timestamp' => Carbon::now()
            ]);

            // Log slow process if enabled
            if ($this->config['features']['log_slow_process']) {
                $processingTime = microtime(true) - $startTime;
                if ($processingTime > $this->config['features']['slow_process_time_in_seconds']) {
                    Log::warning('Slow URL Encoding Process', [
                        'processing_time' => $processingTime,
                        'original_url' => $originalUrl
                    ]);
                }
            }
        }
    }

    protected function logDecoding($shortUrl, $originalUrl)
    {
        if ($this->config['features']['logging']) {
            $startTime = microtime(true);

            Log::info('URL Decoded', [
                'short_url' => $shortUrl,
                'original_url' => $originalUrl,
                'timestamp' => Carbon::now()
            ]);

            // Log slow process if enabled
            if ($this->config['features']['log_slow_process']) {
                $processingTime = microtime(true) - $startTime;
                if ($processingTime > $this->config['slow_process_time_in_seconds']) {
                    Log::warning('Slow URL Decoding Process', [
                        'processing_time' => $processingTime,
                        'short_url' => $shortUrl
                    ]);
                }
            }
        }
    }
}
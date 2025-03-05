<?php

namespace App\Http\Controllers;

use App\Services\UrlShortenerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UrlShortenerController extends Controller
{
    protected $urlShortenerService;

    public function __construct(UrlShortenerService $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }

    public function encode(Request $request): JsonResponse
    {
        $url = trim($request->input('url'));

        // Validate URL
        $isValidURL = $this->urlShortenerService->validateUrl($url, 'encode');
        if ( ! ( isset($isValidURL['status']) && $isValidURL['status'] ) ) {
            return response()->json([
                'error' => ( isset($isValidURL['msg']) && $isValidURL['msg'] )?$isValidURL['msg']:'Invalid URL format'
            ], 400);
        }

        $result = $this->urlShortenerService->encodeUrl($url);

        return response()->json($result);
    }

    public function decode(Request $request): JsonResponse
    {
        $shortUrl = trim($request->input('url'));

        // Validate URL
        $isValidURL = $this->urlShortenerService->validateUrl($shortUrl, 'decode');
        if ( ! ( isset($isValidURL['status']) && $isValidURL['status'] ) ) {
            return response()->json([
                'error' => ( isset($isValidURL['msg']) && $isValidURL['msg'] )?$isValidURL['msg']:'Invalid Short URL format'
            ], 400);
        }

        $result = $this->urlShortenerService->decodeUrl($shortUrl);

        if (!$result) {
            return response()->json([
                'error' => 'Short URL not found'
            ], 404);
        }

        return response()->json($result);
    }
}
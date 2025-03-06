<?php
return [
    'features' => [
        'logging' => true,
        //'log_daily_usage' => true, //for future impl
        //'log_duplicate_url' => true, //for future impl
        'log_slow_process' => true,
        'slow_process_time_in_seconds' => 1,
        'duplicate_check_expiry_time_in_seconds' => 60, //Duplicate request with same original URLs within the specified time will return the same short URL
        'url_encode_validation' => true, //Validate URL format/pattern during encoding
        'supported_url_protocols' => ['http://', 'https://'], //Supported URL protocol for encoding
        'url_decode_validation' => true, //Validate URL format/pattern during decoding, ensure it matches the value set in short_url_base
        'is_url_reachable' => false, //Validate existence of URL (ping url to ensure its reachable before encoding)
        'url_max_length' => 2048, //Limit length of URL (No of Characters)
    ],
    'short_url_base' => 'http://short.est/',
    //'code_length' => 6, //for future impl
];
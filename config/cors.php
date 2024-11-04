<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*','Admin/*','buffer/*','sanctum/csrf-cookie' ,'transaction/*'  ,'admin/*' ,'binance/*','bot/*' , 'channels/*' ,'console/*' ,'console/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];

// return [
//     'paths' => ['api/*'], // Specify the paths you want to enable CORS for

//     // Define the allowed HTTP methods (e.g., GET, POST, PUT, DELETE, OPTIONS)
//     'allowed_methods' => ['*'], // You can restrict methods if needed

//     // Set allowed origins, '*' allows all or specify specific origins
//     'allowed_origins' => ['*'],

//     // You can specify allowed request headers
//     'allowed_headers' => ['*'],

//     // Expose headers that are accessible to the browser
//     'exposed_headers' => [], // Add the headers you want to expose to the frontend

//     // Define whether credentials such as cookies or authorization headers are allowed
//     'allow_credentials' => false,

//     // Set the max age of the CORS options request in seconds (0 to disable)
//     'max_age' => 0,

//     // Set whether the response can be exposed when credentials are present
//     'supports_credentials' => false,

//     // Array of regex patterns to match allowed origins
//     'allowed_origins_patterns' => [],

//     // Add additional headers to the CORS response
//     'additional_headers' => [],
// ];

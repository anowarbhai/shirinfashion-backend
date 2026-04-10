<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    // Facebook Pixel & Conversion API
    'facebook_pixel_enabled' => env('FACEBOOK_PIXEL_ENABLED', false),
    'facebook_pixel_id' => env('FACEBOOK_PIXEL_ID', ''),
    'facebook_conversion_api_enabled' => env('FACEBOOK_CONVERSION_API_ENABLED', false),
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN', ''),
    'facebook_test_event_code' => env('FACEBOOK_TEST_EVENT_CODE', ''),

    // Google Tag Manager
    'google_tag_manager_enabled' => env('GOOGLE_TAG_MANAGER_ENABLED', false),
    'google_tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID', ''),

    // Google Analytics 4
    'google_analytics_enabled' => env('GOOGLE_ANALYTICS_ENABLED', false),
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', ''),

    // Google Ads
    'google_ads_enabled' => env('GOOGLE_ADS_ENABLED', false),
    'google_ads_id' => env('GOOGLE_ADS_ID', ''),

    // Fraud Checker
    'fraud_checker_enabled' => env('FRAUD_CHECKER_ENABLED', false),
    'fraud_checker_api_url' => env('FRAUD_CHECKER_API_URL', ''),
    'fraud_checker_api_key' => env('FRAUD_CHECKER_API_KEY', ''),
    'fraud_checker_pathao' => env('FRAUD_CHECKER_PATHAO', true),
    'fraud_checker_redx' => env('FRAUD_CHECKER_REDX', true),
    'fraud_checker_carrybee' => env('FRAUD_CHECKER_CARRYBEE', true),
    'fraud_checker_steadfast' => env('FRAUD_CHECKER_STEADFAST', true),

    // Review Settings
    'global_reviews_enabled' => env('GLOBAL_REVIEWS_ENABLED', true),
    'global_avg_rating_enabled' => env('GLOBAL_AVG_RATING_ENABLED', true),
    'guest_reviews_enabled' => env('GUEST_REVIEWS_ENABLED', true),

    // Contact Settings
    'contact_buttons_enabled' => env('CONTACT_BUTTONS_ENABLED', false),
    'whatsapp_number' => env('WHATSAPP_NUMBER', ''),
    'call_number' => env('CALL_NUMBER', ''),
    'whatsapp_message' => env('WHATSAPP_MESSAGE', "Hi, I'm interested in this product: {product_name}. Please provide more details."),

];

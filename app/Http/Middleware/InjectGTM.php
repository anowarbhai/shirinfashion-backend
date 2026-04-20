<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectGTM
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only inject on HTML responses
        if (!$response->headers->has('Content-Type') || 
            !str_contains($response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

$content = $response->getContent();

        // Read directly from .env to avoid caching issues
        $gtmEnabled = filter_var(env('GOOGLE_TAG_MANAGER_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        $gtmId = env('GOOGLE_TAG_MANAGER_ID', '');

        $pixelEnabled = filter_var(env('FACEBOOK_PIXEL_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        $pixelId = env('FACEBOOK_PIXEL_ID', '');

        // Google Tag Manager - noscript (must be right after body tag)
        if ($gtmEnabled && $gtmId) {
            $gtmNoscript = <<<HTML
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$gtmId}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
HTML;
            // Insert right after <body> tag
            $content = preg_replace(
                '/<body[^>]*>/i',
                '$0' . "\n" . $gtmNoscript . "\n",
                $content,
                1
            );
        }

        // Google Tag Manager - script (in head)
        if ($gtmEnabled && $gtmId) {
            $gtmScript = <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$gtmId}');</script>
<!-- End Google Tag Manager -->
HTML;
            // Insert at the beginning of head or after <head>
            if (preg_match('/<head[^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $headPos = $matches[0][1] + strlen($matches[0][0]);
                $content = substr($content, 0, $headPos) . $gtmScript . "\n" . substr($content, $headPos);
            }
        }

        // Facebook Pixel
        if ($pixelEnabled && $pixelId) {
            $fbScript = <<<HTML
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{f=f||{};f._qs=+new Date;
t=b.createElement(e);t.async=!0;
t.src=v;e=b.getElementsByTagName(e)[0];
e.parentNode.insertBefore(t,e)}
(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixelId}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->
HTML;
            // Insert at the beginning of head or after <head>
            if (preg_match('/<head[^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $headPos = $matches[0][1] + strlen($matches[0][0]);
                $content = substr($content, 0, $headPos) . $fbScript . "\n" . substr($content, $headPos);
            } else {
                $content = $fbScript . "\n" . $content;
            }
        }

        $response->setContent($content);

        return $response;
    }
}
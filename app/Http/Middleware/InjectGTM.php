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

        // Get settings directly without caching
        $gtmEnabled = filter_var(config('app.google_tag_manager_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $gtmId = config('app.google_tag_manager_id', '');

        $pixelEnabled = filter_var(config('app.facebook_pixel_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $pixelId = config('app.facebook_pixel_id', '');

        $scripts = '';

        // Google Tag Manager
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
            $scripts .= $gtmScript;
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
            $scripts .= $fbScript;
        }

        if ($scripts) {
            // Insert after <head> or at beginning of body
            if (str_contains($content, '<head>')) {
                $content = str_replace('<head>', '<head>' . $scripts, $content);
            } elseif (str_contains($content, '<body>')) {
                $content = str_replace('<body>', '<body>' . $scripts, $content);
            } else {
                $content = $scripts . $content;
            }
        }

        $response->setContent($content);

        return $response;
    }
}
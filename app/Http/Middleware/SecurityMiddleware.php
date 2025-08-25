<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // إزالة رؤوس قد تكشف معلومات الخادم
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->remove('x-turbo-charged-by');

        // حماية ضد clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // منع sniffing للـ MIME types
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // سياسة cross-domain
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // سياسة Referrer محسنة
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Cross-Origin-Embedder-Policy
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Content Security Policy صارمة
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "style-src 'self'; " .
            "script-src 'self'; " .
            "img-src 'self' data:; " .
            "connect-src 'self'; " .
            "font-src 'self'; " .
            "frame-ancestors 'none'; " .
            "form-action 'self'; " .
            "base-uri 'self'; " .
            "object-src 'none'; " .
            "upgrade-insecure-requests"
        );

        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Certificate Transparency
        $response->headers->set('Expect-CT', 'max-age=86400, enforce');

        // Permissions Policy صارمة
        $response->headers->set(
            'Permissions-Policy',
            "geolocation=(), microphone=(), camera=(), fullscreen=(), payment=(), autoplay=()"
        );

        // Clear-Site-Data للحد من تسرب البيانات
        $response->headers->set('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"');

        // Headers لإدارة الكاش
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');

        // Rate Limit Headers (يمكن تعديل القيم حسب سياساتك)
        $response->headers->set('X-RateLimit-Limit', '1000');
        $response->headers->set('X-RateLimit-Remaining', '950');
        $response->headers->set('X-RateLimit-Reset', '3600');

        // منع فتح الملفات في IE تلقائيًا
        $response->headers->set('X-Download-Options', 'noopen');

        // HSTS + إعادة التوجيه للـ HTTPS في الإنتاج
        if (config('app.env') === 'production') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=63072000; includeSubDomains; preload'
            );

            if (!$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }
        }

        // Headers إضافية
        $response->headers->set('X-Content-Security-Policy', "default-src 'self'");
        $response->headers->set('X-WebKit-CSP', "default-src 'self'");
        $response->headers->set('X-DNS-Prefetch-Control', 'off');

        return $response;
    }
}

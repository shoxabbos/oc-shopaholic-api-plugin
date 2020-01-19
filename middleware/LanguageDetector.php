<?php namespace Shohabbos\Shopaholicapi\Middleware;

use RainLab\Translate\Classes\Translator;
use Closure;
use Config;

class LanguageDetector
{
    public function handle($request, \Closure $next)
    {
    	$translator = Translator::instance();
        $translator->isConfigured();

        if (!$translator->loadLocaleFromRequest()) {
            if (Config::get('rainlab.translate::prefixDefaultLocale') && $request->header('Accept-Language')) {
                $translator->setLocale($request->header('Accept-Language'), false);
            } else {
                $translator->setLocale($translator->getDefaultLocale(), false);
            }
        }

        return $next($request);
    }
}
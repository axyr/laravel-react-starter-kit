<?php

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

function frontendUrl($path = null, $parameters = []): UrlGenerator|string
{
    return with(clone url(), static function (UrlGenerator $urlGenerator) use ($path, $parameters) {
        $root = config('app.frontend_url');

        $urlGenerator->useOrigin($root);

        if (is_null($path)) {
            return $urlGenerator;
        }

        $secure = Str::startsWith($root, 'https://');

        return $urlGenerator->to($path, $parameters, $secure);
    });
}

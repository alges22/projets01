<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

if (!function_exists('workflowName')) {

    /**
     * @param string $channel
     * @return string
     */
    function workflowName(string $channel): string
    {
        return implode(' ', array_map(function ($name) {
            return ucfirst($name);
        }, explode('_', $channel)));
    }
}

if (!function_exists("notifier")) {

    /**
     * @return Application|mixed
     */
    function notifier()
    {
        return app()->make('notifier');
    }
}

if (!function_exists('completeInterpolateText')) {

    /**
     * @param string $text
     * @param array $variables
     * @return string
     */
    function completeInterpolateText(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = Str::replace('{' . $key . '}', $value, $text);
        }

        return $text;
    }
}

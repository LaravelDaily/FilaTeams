<?php

namespace LaravelDaily\FilaTeams\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class TeamName implements ValidationRule
{
    /**
     * @var array<int, string>
     */
    protected static array $reservedNames = [
        'admin', 'administrator', 'api', 'auth', 'billing', 'blog',
        'cache', 'cdn', 'connect', 'contact', 'css', 'dashboard',
        'developer', 'docs', 'download', 'email', 'faq', 'feed',
        'fonts', 'forum', 'help', 'home', 'host', 'hosting',
        'images', 'img', 'info', 'invitations', 'invite', 'js',
        'legal', 'login', 'logout', 'mail', 'manage', 'media',
        'members', 'new', 'newsletter', 'notifications', 'null',
        'oauth', 'password', 'pay', 'payment', 'plans', 'portal',
        'pricing', 'privacy', 'profile', 'register', 'registration',
        'root', 'rss', 'search', 'security', 'settings', 'setup',
        'signin', 'signup', 'sitemap', 'smtp', 'ssl', 'staff',
        'static', 'status', 'store', 'subscribe', 'support', 'system',
        'team', 'teams', 'terms', 'test', 'undefined', 'unsubscribe',
        'update', 'upload', 'url', 'user', 'users', 'verify',
        'webhook', 'webhooks', 'www',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $slug = Str::slug($value);

        if (in_array($slug, static::$reservedNames, true)) {
            $fail('This team name is reserved and cannot be used.');

            return;
        }

        $routeSegments = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => Str::before($route->uri(), '/'))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (in_array($slug, $routeSegments, true)) {
            $fail('This team name conflicts with an existing route and cannot be used.');
        }
    }
}

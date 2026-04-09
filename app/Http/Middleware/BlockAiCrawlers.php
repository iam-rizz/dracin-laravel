<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks requests from known AI crawlers and training bots at the application level.
 *
 * robots.txt is advisory only — this middleware enforces the block by returning
 * a 403 response when a matching user-agent is detected.
 *
 * Controlled via BLOCK_AI_CRAWLERS in .env (default: true).
 */
class BlockAiCrawlers
{
    /**
     * User-agent substrings to match against. Case-insensitive.
     * Kept as partial matches to catch versioned variants (e.g. "GPTBot/1.0").
     */
    protected array $blockedBots = [
        // OpenAI
        'GPTBot',
        'ChatGPT-User',
        'OAI-SearchBot',

        // Anthropic
        'anthropic-ai',
        'Claude-Web',
        'ClaudeBot',

        // Google AI
        'Google-Extended',

        // Meta
        'FacebookBot',
        'Meta-ExternalAgent',
        'Meta-ExternalFetcher',

        // Apple
        'Applebot-Extended',

        // Common Crawl
        'CCBot',

        // Bytedance
        'Bytespider',

        // Amazon
        'Amazonbot',

        // Perplexity
        'PerplexityBot',

        // Cohere
        'cohere-ai',

        // AI2
        'Ai2Bot',
        'Ai2Bot-Dolma',

        // Diffbot
        'Diffbot',

        // Webz.io
        'Omgilibot',

        // Others
        'Sentibot',
        'iaskspider',
        'Timpibot',
        'YouBot',
        'Scrapy',

        // Aggressive SEO bots
        'PetalBot',
        'SemrushBot',
        'AhrefsBot',
        'MJ12bot',
        'DotBot',
        'BLEXBot',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.block_ai_crawlers', true)) {
            return $next($request);
        }

        $userAgent = $request->userAgent() ?? '';

        foreach ($this->blockedBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return response('Forbidden', 403);
            }
        }

        return $next($request);
    }
}

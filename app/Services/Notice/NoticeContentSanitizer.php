<?php

namespace App\Services\Notice;

use Illuminate\Support\Str;

/**
 * Keep notice formatting while removing markup that could run in a browser.
 */
class NoticeContentSanitizer
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_TAGS = [
        'p', 'strong', 'em', 's', 'h1', 'h2', 'ul', 'ol', 'li',
        'blockquote', 'pre', 'code', 'a', 'hr', 'br',
    ];

    public function sanitize(string $content): string
    {
        if (!str_contains($content, '<')) {
            $content = Str::markdown($content);
        }

        $content = strip_tags($content, '<'.implode('><', self::ALLOWED_TAGS).'>');

        return preg_replace_callback(
            '/<([a-z][a-z0-9]*)(?:\s+[^>]*)?>/i',
            function (array $matches): string {
                $tag = strtolower($matches[1]);

                if ($tag !== 'a') {
                    return "<$tag>";
                }

                preg_match(
                    '/\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i',
                    $matches[0],
                    $hrefMatches,
                );
                $href = $hrefMatches[1] ?? $hrefMatches[2] ?? $hrefMatches[3] ?? '';

                if ($href === '' || preg_match('/^(?:https?:\/\/|mailto:|\/|#)/i', $href) !== 1) {
                    return '<a>';
                }

                return '<a href="'.htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'">';
            },
            $content,
        ) ?? '';
    }
}

<?php

namespace HTMLPurifier\Filter;

use HTMLPurifier_Config;
use HTMLPurifier_Context;
use HTMLPurifier_Filter;

/**
 * Custom HTMLPurifier filter for IE conditional comments
 *
 * HTMLPurifier always removes IE conditional comments from HTML and there is no
 * way to alter that behavior without touching the sources.
 *
 * This filter disguises IE conditional comments as plain tags and reverts them
 * back after HTMLPurifier has purified HTML. This also means that HTML inside
 * conditional comments also gets purified.
 */
class ConditionalComments extends HTMLPurifier_Filter
{
    /**
     * Disguises IE conditional comments with plain tag
     *
     * @param string               $html
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return string
     */
    public function preFilter($html, $config, $context)
    {
        $regex = [
            '#<!--\[([^]]+)]><!-->#',
            '#<!--\[([^]]+)]>-->#',
            '#<!--\[([^]]+)]>#',

            '#<!--<!\[([^]]+)]-->#',
            '#<!\[([^]]+)]-->#',
            '#<\[([^]]+)]-->#',
        ];

        $replace = [
            '<span class="conditional-comment-open--revealed-1">$1</span>',
            '<span class="conditional-comment-open--revealed-2">$1</span>',
            '<span class="conditional-comment-open--hidden">$1</span>',

            '<span class="conditional-comment-close--revealed">$1</span>',
            '<span class="conditional-comment-close--hidden-1">$1</span>',
            '<span class="conditional-comment-close--hidden-2">$1</span>',
        ];

        return preg_replace($regex, $replace, $html);
    }

    /**
     * Removes disguise from IE conditional comments
     *
     * @param string               $html
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return string
     */
    public function postFilter($html, $config, $context)
    {
        $regex = [
            '#<span class="conditional-comment-open--revealed-1">(.*?)</span>#',
            '#<span class="conditional-comment-open--revealed-2">(.*?)</span>#',
            '#<span class="conditional-comment-open--hidden">(.*?)</span>#',

            '#<span class="conditional-comment-close--revealed">(.*?)</span>#',
            '#<span class="conditional-comment-close--hidden-1">(.*?)</span>#',
            '#<span class="conditional-comment-close--hidden-2">(.*?)</span>#',
        ];

        $replace = [
            '<!--[$1]><!-->',
            '<!--[$1]>-->',
            '<!--[$1]>',

            '<!--<![$1]-->',
            '<![$1]-->',
            '<[$1]-->',
        ];

        return preg_replace($regex, $replace, $html);
    }
}

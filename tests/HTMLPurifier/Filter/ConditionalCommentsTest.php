<?php

namespace HTMLPurifier\Filter;

use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier_Context;
use PHPUnit\Framework\TestCase;

class ConditionalCommentsTest extends TestCase
{
    /**
     * HTMLPurifier configuration
     */
    private HTMLPurifier_Config $config;

    /**
     * HTMLPurifier context
     */
    private HTMLPurifier_Context $context;

    /**
     * Instance of conditional comments filter to test
     */
    private ConditionalComments $filter;

    /**
     * HTMLPurifier instance
     */
    private HTMLPurifier $html_purifier;

    public function setUp(): void
    {
        $this->filter = new ConditionalComments();
        $this->context = new HTMLPurifier_Context();

        $this->config = HTMLPurifier_Config::createDefault();
        $this->config->set('Filter.Custom', [$this->filter]);

        $this->html_purifier = new HTMLPurifier($this->config);
    }

    /**
     * @dataProvider htmlProvider
     *
     * @param string $html     Source HTML
     * @param string $expected Expected HTML
     */
    public function testPreFilter(string $html, string $expected): void
    {
        $filtered = $this->filter->preFilter($html, $this->config, $this->context);

        self::assertSame($expected, $filtered);
    }

    /**
     * @dataProvider htmlProvider
     *
     * @param string $expected Expected HTML
     * @param string $html     Source HTML
     */
    public function testPostFilter(string $expected, string $html): void
    {
        $html = $this->filter->postFilter($html, $this->config, $this->context);

        self::assertSame($expected, $html);
    }

    /**
     * @dataProvider htmlProvider
     *
     * @param string $html Source HTML
     */
    public function testPurify(string $html): void
    {
        $purified = $this->html_purifier->purify($html);

        self::assertSame($html, $purified);
    }

    /**
     * Provides HTML to test upon
     *
     * @return array{0: string, 0: string}[]
     */
    public static function htmlProvider(): array
    {
        return [
            [
                '<div><!--[if IE]><table><tr><td><![endif]-->Content<!--[if IE]></td></tr></table><![endif]--></div>',
                '<div><span class="conditional-comment-open--hidden">if IE</span><table><tr><td><span class="conditional-comment-close--hidden-1">endif</span>Content<span class="conditional-comment-open--hidden">if IE</span></td></tr></table><span class="conditional-comment-close--hidden-1">endif</span></div>',
            ], [
                '<div><a href="#">Click me<!--[if gte mso 9]> baby<![endif]-->! One<!--[if gte mso 9]> more<![endif]--> time.</a></div>',
                '<div><a href="#">Click me<span class="conditional-comment-open--hidden">if gte mso 9</span> baby<span class="conditional-comment-close--hidden-1">endif</span>! One<span class="conditional-comment-open--hidden">if gte mso 9</span> more<span class="conditional-comment-close--hidden-1">endif</span> time.</a></div>',
            ], [
                '<div><p><!--[if !mso]>-->Storm, earth and fire — heed my call!<!--<![endif]--></p></div>',
                '<div><p><span class="conditional-comment-open--revealed-2">if !mso</span>Storm, earth and fire — heed my call!<span class="conditional-comment-close--revealed">endif</span></p></div>',
            ], [
                '<div><p>Don\'t you have<!--[if !mso]><!--> a kingdom<!--<![endif]--> to run?</p></div>',
                '<div><p>Don\'t you have<span class="conditional-comment-open--revealed-1">if !mso</span> a kingdom<span class="conditional-comment-close--revealed">endif</span> to run?</p></div>',
            ],
            [
                '<!--[if mso]><table><tr><td><![endif]--><!--[if mso]></td></tr></table><[endif]-->',
                '<span class="conditional-comment-open--hidden">if mso</span><table><tr><td><span class="conditional-comment-close--hidden-1">endif</span><span class="conditional-comment-open--hidden">if mso</span></td></tr></table><span class="conditional-comment-close--hidden-2">endif</span>',
            ],
        ];
    }
}

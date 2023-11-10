<?php

namespace HTMLPurifier\Filter;

use HTMLPurifier_Config;
use HTMLPurifier_Context;
use PHPUnit\Framework\TestCase;

class ConditionalCommentsTest extends TestCase {

	/**
	 * Filter instance
	 * @var ConditionalComments
	 */
	protected $filter;

	/**
	 * HTMLPurifier configuration
	 * @var HTMLPurifier_Config
	 */
	protected $config;

	/**
	 * HTMLPurifier context
	 * @var HTMLPurifier_Context
	 */
	protected $context;


	/**
	 * Sets up environment for each test
	 */
	public function setUp() {
		$this->filter = new ConditionalComments;
		$this->config = HTMLPurifier_Config::createDefault();
		$this->context = new HTMLPurifier_Context;
	}


	/**
	 * @dataProvider htmlProvider
	 *
	 * @param string $html     Source HTML
	 * @param string $expected Expected HTML
	 */
	public function testPreFilter($html, $expected) {
		$filtered = $this->filter->preFilter($html, $this->config, $this->context);

		$this->assertEquals($filtered, $expected);
	}

	/**
	 * @dataProvider htmlProvider
	 *
	 * @param string $expected Expected HTML
	 * @param string $html     Source HTML
	 */
	public function testPostFilter($expected, $html) {
		$html = $this->filter->postFilter($html, $this->config, $this->context);

		$this->assertEquals($html, $expected);
	}


	/**
	 * Provides HTML for testing purposes
	 *
	 * @return array[]
	 */
	public function htmlProvider() {
		return [[
			'<div><!--[if IE]><table><tr><td><![endif]-->Content<!--[if IE]></td></tr></table><![endif]--></div>',
			'<div><span class="conditional-comment-open--hidden">if IE</span><table><tr><td><span class="conditional-comment-close--hidden">endif</span>Content<span class="conditional-comment-open--hidden">if IE</span></td></tr></table><span class="conditional-comment-close--hidden">endif</span></div>',
		], [
			'<div><a href="#"><!--[if gte mso 9]>&nbsp;<![endif]-->Click me baby! One more time<!--[if gte mso 9]>&nbsp;<![endif]--></a></div>',
		    '<div><a href="#"><span class="conditional-comment-open--hidden">if gte mso 9</span>&nbsp;<span class="conditional-comment-close--hidden">endif</span>Click me baby! One more time<span class="conditional-comment-open--hidden">if gte mso 9</span>&nbsp;<span class="conditional-comment-close--hidden">endif</span></a></div>',
		], [
            '<div><p><!--[if !mso]>-->Storm, earth and fire — heed my call!<!--<![endif]--></p></div>',
            '<div><p><span class="conditional-comment-open--revealed-2">if !mso</span>Storm, earth and fire — heed my call!<span class="conditional-comment-close--revealed">endif</span></p></div>',
        ], [
            '<div><p>Don\'t you have<!--[if !mso]><!--> a kingdom<!--<![endif]--> to run?</p></div>',
            '<div><p>Don\'t you have<span class="conditional-comment-open--revealed-1">if !mso</span> a kingdom<span class="conditional-comment-close--revealed">endif</span> to run?</p></div>',
        ]];
	}

}

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
			'<div><span class="conditional-comment-open">if IE</span><table><tr><td><span class="conditional-comment-close">endif</span>Content<span class="conditional-comment-open">if IE</span></td></tr></table><span class="conditional-comment-close">endif</span></div>',
		], [
			'<div><a href="#"><!--[if gte mso 9]>&nbsp;<![endif]-->Click me baby! One more time<!--[if gte mso 9]>&nbsp;<![endif]--></a></div>',
		    '<div><a href="#"><span class="conditional-comment-open">if gte mso 9</span>&nbsp;<span class="conditional-comment-close">endif</span>Click me baby! One more time<span class="conditional-comment-open">if gte mso 9</span>&nbsp;<span class="conditional-comment-close">endif</span></a></div>',
		]];
	}

}

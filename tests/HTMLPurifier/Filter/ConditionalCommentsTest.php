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
			'<div><div class="conditional-comment-open">if IE</div><table><tr><td><div class="conditional-comment-close">endif</div>Content<div class="conditional-comment-open">if IE</div></td></tr></table><div class="conditional-comment-close">endif</div></div>',
		]];
	}

}

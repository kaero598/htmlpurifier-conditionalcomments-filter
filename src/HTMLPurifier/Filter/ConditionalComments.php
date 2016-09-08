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
class ConditionalComments extends HTMLPurifier_Filter {

	/**
	 * Class name to be applied to temporary element in place of closing
	 * conditional comment
	 *
	 * @var string
	 */
	protected $classNameClose = 'conditional-comment-close';

	/**
	 * Class name to be applied to temporary element in place of opening
	 * conditional comment
	 *
	 * @var string
	 */
	protected $classNameOpen = 'conditional-comment-open';


	/**
	 * Disguises IE conditional comments with plain tag
	 *
	 * @param string               $html
	 * @param HTMLPurifier_Config  $config
	 * @param HTMLPurifier_Context $context
	 *
	 * @return string
	 */
	public function preFilter($html, $config, $context) {
		$regex = [
			'#<!--\[([^]]+)\]>#i',
		    '#<!\[([^]]+)\]-->#i',
		];

		$replace = [
			'<div class="conditional-comment-open">$1</div>',
		    '<div class="conditional-comment-close">$1</div>',
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
	 * @return mixed
	 */
	public function postFilter($html, $config, $context) {
		$regex = [
			'#<div class="conditional-comment-open">(.*?)</div>#',
			'#<div class="conditional-comment-close">(.*?)</div>#',
		];

		$replace = [
			'<!--[$1]>',
		    '<![$1]-->',
		];

		return preg_replace($regex, $replace, $html);
	}

}
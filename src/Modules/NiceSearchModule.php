<?php

namespace BK\WPCleanup\Modules;

use function is_search;
use function wp_safe_redirect;
use function get_search_link;

/**
 * Redirects search results from /?s=query to /search/query/, converts %20 to +
 *
 * @link http://txfx.net/wordpress-plugins/nice-search/
 */
class NiceSearchModule {
	/** @var NiceSearchModule Singleton instance */
	private static NiceSearchModule $instance;

	public function __construct () {
		if (isset(self::$instance)) {
			return;
		}

		self::$instance = $this;

		$this->handleRedirect();
		$this->handleCompatibility();
	}

	/**
	 * Redirect query string search results to pretty URL.
	 */
	public function handleRedirect () : self {
		add_filter('template_redirect', function () {
			global $wp_rewrite;

			if (
				!isset($_SERVER['REQUEST_URI']) ||
				!isset($wp_rewrite) ||
				!is_object($wp_rewrite) ||
				!$wp_rewrite->get_search_permastruct()
			) {
				return;
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$request = wp_unslash(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));

			$search_base = $wp_rewrite->search_base;

			if (
				is_search() &&
				strpos($request, "/{$search_base}/") === false &&
				strpos($request, '&') === false &&
				wp_safe_redirect(get_search_link())
			) {
				exit;
			}
		});

		return $this;
	}

	/**
	 * Rewrite query string search URL as pretty URL.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function rewriteURL ($url) : string {
		return str_replace(
			'/?s=',
			'/search/',
			$url
		);
	}

	/**
	 * Add compatibility with third-party plugins.
	 */
	protected function handleCompatibility () : self {
		$this->handleYoastCompatibility();

		return $this;
	}

	/**
	 * Add compatibility for Yoast SEO.
	 *
	 * TODO - Test
	 */
	protected function handleYoastCompatibility () : self {
		add_filter('wpseo_json_ld_search_url',
			fn ($url) => $this->rewriteURL($url));

		return $this;
	}
}

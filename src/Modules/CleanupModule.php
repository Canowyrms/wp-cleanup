<?php

namespace BK\WPCleanup\Modules;

/**
 * Cleans up various aspects of WordPress, namely the admin dashboard.
 *
 * @link https://github.com/vincentorback/clean-wordpress-admin
 */
class CleanupModule {
	/** @var CleanupModule Singleton instance */
	private static CleanupModule $instance;

	public function __construct () {
		if (isset(self::$instance)) {
			return;
		}

		self::$instance = $this;

		$this->loadModules();
	}

	private function loadModules () {
		$this->adminBar();
		$this->adminFooter();
		$this->assets();
		$this->contextualTabs();
		$this->dashboard();
		$this->emojis();
		$this->head();
		$this->images();
		$this->updates();
	}

	private function adminBar () {
		/**
		 * Hide or create new menus and items in the admin bar.
		 * Indentation shows sub-items.
		 *
		 * @link https://developer.wordpress.org/reference/hooks/wp_before_admin_bar_render/
		 */
		add_action('wp_before_admin_bar_render', function () {
			global $wp_admin_bar;

			/* WP Logo menu */
			$wp_admin_bar->remove_menu('wp-logo');        // Remove the WordPress logo
			$wp_admin_bar->remove_menu('about');          // Remove the about WordPress link
			$wp_admin_bar->remove_menu('wporg');          // Remove the about WordPress link
			$wp_admin_bar->remove_menu('documentation');  // Remove the WordPress documentation link
			$wp_admin_bar->remove_menu('support-forums'); // Remove the support forums link
			$wp_admin_bar->remove_menu('feedback');       // Remove the feedback link

			/* Site name menu */
			//$wp_admin_bar->remove_menu('site-name');      // Remove the site name menu
			$wp_admin_bar->remove_menu('view-site');      // Remove the view site link
			$wp_admin_bar->remove_menu('dashboard');      // Remove the dashboard link
			$wp_admin_bar->remove_menu('themes');         // Remove the themes link
			$wp_admin_bar->remove_menu('widgets');        // Remove the widgets link
			$wp_admin_bar->remove_menu('menus');          // Remove the menus link

			/* Customize menu */
			//$wp_admin_bar->remove_menu('customize');      // Remove the site name menu

			/* Updates menu */
			//$wp_admin_bar->remove_menu('updates');        // Remove the updates link

			/* Comments menu */
			$wp_admin_bar->remove_menu('comments');       // Remove the comments link

			/* New content menu */
			$wp_admin_bar->remove_menu('new-content');    // Remove the content link
			//$wp_admin_bar->remove_menu('new-post');       // Remove the new post link
			//$wp_admin_bar->remove_menu('new-media');      // Remove the new media link
			//$wp_admin_bar->remove_menu('new-page');       // Remove the new page link
			//$wp_admin_bar->remove_menu('new-user');       // Remove the new user link

			/* Edit menu */
			//$wp_admin_bar->remove_menu('edit');           // Remove the edit link

			/* User account menu */
			//$wp_admin_bar->remove_menu('my-account');     // Remove the user details tab

			/* Front-end search bar */
			//$wp_admin_bar->remove_menu('search');         // Remove the search tab
		}, 999);

		/**
		 * Replace "Howdy, "-title and avatar with only users display_name
		 *
		 * @link https://developer.wordpress.org/reference/hooks/admin_bar_menu/
		 */
		add_filter('admin_bar_menu', function ($admin_bar) {
			$title = wp_get_current_user()->display_name;

			$admin_bar->add_node([
				'id'    => 'my-account',
				'title' => $title,
			]);
		}, 25);
	}

	private function adminFooter () {
		/**
		 * Remove left admin footer text
		 *
		 * @link https://developer.wordpress.org/reference/hooks/admin_footer_text/
		 */
		add_filter('admin_footer_text', '__return_empty_string');
	}

	private function assets () {
		/**
		 * Remove wordpress version from scripts
		 */
		add_filter('script_loader_src',
			fn ($src) => self::removeScriptVersion($src), 15, 1);
		add_filter('style_loader_src',
			fn ($src) => self::removeScriptVersion($src), 15, 1);

		/**
		 * Remove type from style and script tags
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
		 */
		add_action('after_setup_theme', function () {
			add_theme_support('html5', ['script', 'style']);
		});
	}

	public static function removeScriptVersion ($src) {
		global $wp_version;

		$parts = explode("?ver=$wp_version", $src);

		return $parts[0];
	}

	private function contextualTabs () {
		/**
		 * Remove Help Tabs
		 *
		 * @link https://developer.wordpress.org/reference/classes/wp_screen/remove_help_tabs/
		 * @link https://developer.wordpress.org/reference/classes/wp_screen/remove_help_tab/
		 */
		add_action('admin_head', function () {
			$screen = get_current_screen();

			// Remove all tabs
			$screen->remove_help_tabs();

			// Remove specific
			//$screen->remove_help_tab('id-of-tab-you-want-to-remove');
		});
	}

	private function dashboard () {
		/**
		 * Removing dashboard widgets.
		 *
		 * @link https://developer.wordpress.org/reference/functions/remove_meta_box/
		 */
		add_action('wp_dashboard_setup', function () {
			// 'Activity' metabox
			remove_meta_box('dashboard_activity', 'dashboard', 'normal');

			// 'At a Glance' metabox
			remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

			// 'Quick Draft' metabox
			remove_meta_box('dashboard_quick_press', 'dashboard', 'side');

			// 'Site health' metabox
			remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

			// 'Welcome' panel
			remove_action('welcome_panel', 'wp_welcome_panel');

			// 'WordPress News' metabox
			remove_meta_box('dashboard_primary', 'dashboard', 'side');

			// Plugin - 'OceanWP Extra'
			add_filter('oceanwp_news_enabled', '__return_true');
		});
	}

	private function emojis () {
		/**
		 * Remove emoji support
		 *
		 * @link https://wordpress.org/support/article/using-smilies/
		 */

		// Front-end
		add_action('init', function () {
			//@fmt:off
			remove_action('wp_head',         'print_emoji_detection_script', 7);
			remove_action('wp_print_styles', 'print_emoji_styles');

			// Feeds
			remove_filter('the_content_feed', 'wp_staticize_emoji');
			remove_filter('comment_text_rss', 'wp_staticize_emoji');

			// Embeds
			remove_filter('embed_head', 'print_emoji_detection_script');

			// Emails
			remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

			// Since WP 6.4.0
			remove_action('enqueue_scripts', 'wp_enqueue_emoji_styles');
			//@fmt:on
		});

		// Admin
		add_action('admin_init', function () {
			remove_action('admin_print_scripts', 'print_emoji_detection_script');
			remove_action('admin_print_styles', 'print_emoji_styles');

			// Since WP 6.4.0
			remove_action('admin_enqueue_scripts', 'wp_enqueue_emoji_styles');
		});

		// Disable from TinyMCE editor. Disabled in block editor by default
		// TODO
		add_filter('tiny_mce_plugins', function ($plugins) {
			if (is_array($plugins)) {
				$plugins = array_diff($plugins, ['wpemoji']);
			}

			return $plugins;
		});
	}

	private function head () {
		/**
		 * Remove feeds and wordpress-specific content that is generated on the wp_head hook.
		 *
		 * @link https://developer.wordpress.org/reference/hooks/wp_head/
		 */
		add_action('init', function () {
			// Remove the Really Simple Discovery service link
			remove_action('wp_head', 'rsd_link');

			// Remove the link to the Windows Live Writer manifest
			remove_action('wp_head', 'wlwmanifest_link');

			// Remove the general feeds
			remove_action('wp_head', 'feed_links', 2);

			// Remove the extra feeds, such as category feeds
			remove_action('wp_head', 'feed_links_extra', 3);

			// Remove the displayed XHTML generator
			remove_action('wp_head', 'wp_generator');

			// Remove the REST API link tag
			//remove_action('wp_head', 'rest_output_link_wp_head'); // TODO

			// Remove oEmbed discovery links.
			//remove_action('wp_head', 'wp_oembed_add_discovery_links'); // TODO

			// Remove rel next/prev links
			//remove_action('wp_head', 'adjacent_posts_rel_link');

			// Remove prefetch url
			//remove_action('wp_head', 'wp_resource_hints', 2);
		});
	}

	private function images () {
		// Disable generating multiple sizes for uploaded images.
		add_filter('intermediate_image_sizes', '__return_empty_array');
	}

	private function updates () {
		/**
		 * Remove actions that checks for updates
		 */
		add_action('admin_init', function () {
			remove_action('wp_maybe_auto_update', 'wp_maybe_auto_update');
			remove_action('admin_init', 'wp_maybe_auto_update');
			remove_action('admin_init', 'wp_auto_update_core');
			wp_clear_scheduled_hook('wp_maybe_auto_update');
		});
	}
}

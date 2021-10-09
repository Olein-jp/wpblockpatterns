<?php
/**
 * Plugin name: BlockPatterns.jp Customize
 * Description: wpblockpatterns.jp のカスタマイズ専用プラグイン
 * Version: 1.0.0
 *
 * @author Olein-jp
 * @license GPL-2.0+
 */

/**
 * Snow Monkey 以外のテーマを利用している場合は有効化してもカスタマイズが反映されないようにする
 */
$theme = wp_get_theme( get_template() );
if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
	return;
}

/**
 * Directory url of this plugin
 *
 * @var string
 */
define( 'MY_SNOW_MONKEY_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Directory path of this plugin
 *
 * @var string
 */
define( 'MY_SNOW_MONKEY_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Register style
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_style(
			'my-snow-monkey-style',
			MY_SNOW_MONKEY_URL . '/build/css/style.css',
			[ Framework\Helper::get_main_style_handle() ],
			filemtime( plugin_dir_path( __FILE__ ) )
		);

		wp_enqueue_script(
			'oleindesign-script',
			MY_SNOW_MONKEY_URL . '/build/js/scripts.js',
			null,
			filemtime( plugin_dir_path( __FILE__ ) ),
			true
		);
	}
);

/**
 * Register Style for Editor
 */
add_action(
	'after_setup_theme',
	function() {
		add_theme_support( 'editor-styles' );

		add_editor_style( plugins_url( 'build/css/editor-style.css', __FILE__ ) );
	}
);

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
			'wpblockpattern-style',
			MY_SNOW_MONKEY_URL . '/build/css/style.css',
			[ Framework\Helper::get_main_style_handle() ],
			filemtime( plugin_dir_path( __FILE__ ) )
		);

//		wp_enqueue_script(
//			'wpblockpattern-script',
//			MY_SNOW_MONKEY_URL . '/build/js/scripts.js',
//			null,
//			filemtime( plugin_dir_path( __FILE__ ) ),
//			true
//		);
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

/**
 * Show Tags list on archive summary in post meta area only with block pattern category
 *
 * hook to : template-parts/loop/entry-summary/meta/meta
 */
add_action(
	'snow_monkey_template_part_render_template-parts/loop/entry-summary/meta/meta',
	function ( $html ) {
//			wpblockpatterns_output_related_custom_taxonomy();
		return $html;
	}
);

/**
 * Insert heading of pattern
 *
 * hook to : snow_monkey_prepend_entry_content
 */
add_action(
	'snow_monkey_prepend_entry_content',
	function() {
		if ( 'block-pattern' === get_post_type() && is_singular( 'block-pattern' ) ) {
			$output_start = '<div class="p-related-block-list wp-block-group has-lightest-grey-background-color has-background"><p class="has-text-align-center"><strong>The blocks included in this block pattern are...</strong></p>';
			$output_end = '</div>';
			echo $output_start;
			wpblockpatterns_output_related_custom_taxonomy();
			echo $output_end;
		?>
		<h2 class="alignwide">Block Pattern</h2>
		<?php
		}
	}
);

/**
 * Insert Block patterns from content
 */
add_action(
	'snow_monkey_append_entry_content',
	function() {
		if ( 'block-pattern' === get_post_type() ) {
			$content = get_the_content();
			$content = str_replace( '<', '&lt;', $content );
			$content = str_replace( '>', '&gt;', $content );
			$content = str_replace( '&gt;&lt;', '&gt;' . "\n" . '&lt;', $content );
			$content = trim( $content, '	' );
		?>
		<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
		<h2 class="alignwide">Code</h2>
			<p class="has-lightest-grey-background-color has-background">ソースコード右上のボタンをクリックするとソースコードをクリップボードにコピーすることができます。<br><br>そして、<code><strong>Shift + ⌘ + V</strong></code>で<strong>スタイルを合わせて</strong>あなたのWordPress編集画面に<strong>ペースト</strong>することで同じものを用意することができます。</p>

			<div class="hcb_wrap">
				<pre class="prism off-numbers lang-html" data-lang="HTML" data-show-lang="0">
					<code><?php echo $content; ?></code>
				</pre>
			</div>

			<p class="has-lightest-grey-background-color has-background">You can copy the code by clicking on the button in the top right corner.<br>Then <strong>Paste and Match Style</strong> with <code><strong>Shift + ⌘ + V</strong></code> in your WordPress editor.</p>
		<?php
		}
	}
);

/**
 * Insert Custom Taxonomy lists
 */
add_action(
	'snow_monkey_after_entry_content',
	function() {
		if ( is_singular( 'block-pattern' ) ) {
			wpblockpatterns_output_related_custom_taxonomy();
		}
	}
);

/**
 * Function : Output having custom taxonomies
 */
function wpblockpatterns_output_related_custom_taxonomy( $original_class = 'p-related-taxonomy-list' ) {
	$block_pattern_tag_terms = get_terms( 'block_pattern_tag' );
	$output_start = '<ul class="' . $original_class . ' c-meta">';
	$output_content = '';
	$output_end = '</ul>';
	foreach ( $block_pattern_tag_terms as $term ) {
		$output_content .= '<li class="c-meta__item c-meta__item--block-pattern-taxonomy"><a href="' . get_term_link( $term ) . '">' . $term->name . '</a></li>';
	}

	echo $output_start . $output_content . $output_end;
}

/**
 * Rename of related posts section title
 */
add_filter(
	'snow_monkey_get_template_part_args_template-parts/content/related-posts',
	function ( $args ) {
		if ( 'block-pattern' === get_post_type() ) {
			$args[ 'vars' ][ '_title' ] = 'Related Block Patterns';
		} else {
			$args[ 'vars' ][ '_title' ] = 'Related Posts';
		}

		return $args;
	}
);

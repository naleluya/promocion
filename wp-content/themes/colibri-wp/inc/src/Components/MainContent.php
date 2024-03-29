<?php
/**
 * Created by PhpStorm.
 * User: yupal
 * Date: 2/11/2019
 * Time: 6:58 PM
 */

namespace ColibriWP\Theme\Components;


use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class MainContent extends ComponentBase {

	public static function selectiveRefreshSelector() {
		return "#content";
	}

	protected static function getOptions() {
		$prefix = 'content.';

		return array(
			"settings" => array(
				"blog_posts.pen" => array(
					'control' => array(
						'type'        => 'pen',
						'section'     => "content",
						'colibri_tab' => 'content',
					),
				),

				"blog_posts_per_row" => array(
					'transport' => 'refresh',
					'default'   => Defaults::get( "blog_posts_per_row" ),
					'control'   => array(
						'label'       => Translations::get( 'posts_per_row' ),
						'section'     => "content",
						'colibri_tab' => 'content',
						'type'        => 'button-group',
						'button_size' => 'medium',
						'choices'     => array(
							1 => '1',
							2 => '2',
							3 => '3',
							4 => '4',
						),
						'none_value'  => '',
					)
				),

				"{$prefix}separator1" => array(
					'transport' => 'refresh',
					'default'   => '',
					'control'   => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
				),

				"blog_sidebar_enabled" => array(
					'transport' => 'refresh',
					'default'   => Defaults::get( "blog_sidebar_enabled" ),
					'control'   => array(
						'label'       => Translations::get( 'show_blog_sidebar' ),
						'type'        => 'switch',
						'section'     => "content",
						'colibri_tab' => 'content',
					)
				),

				"blog_enable_masonry" => array(
					'transport' => 'refresh',
					'default'   => Defaults::get( "blog_enable_masonry" ),
					'control'   => array(
						'label'       => Translations::get( 'enable_masonry' ),
						'type'        => 'switch',
						'section'     => "content",
						'colibri_tab' => 'content',
					),

				),

				"{$prefix}separator3"               => array(
					'default' => '',
					'control' => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
				),
				"blog_show_post_thumb_placeholder"  => array(
					'transport' => 'refresh',
					'default'   => Defaults::get( "blog_show_post_thumb_placeholder" ),
					'control'   => array(
						'label'       => Translations::get( 'show_thumbnail_placeholder' ),
						'type'        => 'switch',
						'section'     => "content",
						'colibri_tab' => 'content',
					)
				),
				"blog_post_thumb_placeholder_color" => array(
					'transport'  => 'refresh',
					'default'    => Defaults::get( "blog_post_thumb_placeholder_color" ),
					'control'    => array(
						'label'       => Translations::get( 'thumbnail_placeholder_color' ),
						'type'        => 'color',
						'section'     => "content",
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector' => '.colibri-post-has-no-thumbnail.colibri-post-thumbnail-has-placeholder .colibri-post-thumbnail-content',
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'background-color',
						),
					),
				),

			),
			"sections" => array(

				"content" => array(
					'title'    => Translations::get( 'blog_settings' ),
					'priority' => 2,
					'panel'    => 'content_panel',
					'type'     => 'colibri_section',

				),
			),

			"panels" => array(
				"content_panel" => array(
					'priority'       => 2,
					'title'          => Translations::get( 'content_sections' ),
					'type'           => 'colibri_panel',
					'footer_buttons' => array(
						'change_header' => array(
							'label'   => Translations::get( 'add_section' ),
							'name'    => 'colibriwp_add_section',
							'classes' => array( 'colibri-button-large', 'button-primary' ),
							'icon'    => 'dashicons-plus-alt',
						)
					)
				),
			),
		);
	}


	public function printMasonryFlag() {
		$value = $this->mod( "blog_enable_masonry", false );
		if ( $value ) {
			wp_enqueue_script( 'jquery-masonry' );
			$value = 'true';
		} else {
			$value = 'false';
		}
		echo $value;
	}

	public function renderContent() {

		View::printIn( View::CONTENT_ELEMENT, function () {
			View::printIn( View::SECTION_ELEMENT, function () {
				View::printIn( View::ROW_ELEMENT, function () {
					View::printIn( View::COLUMN_ELEMENT, function () {
						View::partial( 'main', 'archive', array(
							"component" => $this,
						) );
					} );
					View::partial( 'sidebar', 'post', array(
						"component" => $this,
					) );
				} );

			} );
		}, array( array( 'blog-page', 'colibri-main-content-area' ) ) );
	}

}

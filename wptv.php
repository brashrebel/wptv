<?php

/*
Plugin Name: WPTV
Plugin URI: http://realbigplugins.com
Description: Integrates videos from WordPress.tv with the WordPress admin.
Version: 0.1
Author: Kyle Maurer
Author URI: http://kyleblog.net
License: GPL2
*/

/**
 * Class wptv
 */
class wptv {

	public $places = array(
		array(
			'screen' => 'tools',
			'tag' => 'api',
		),
		array(
			'screen' => 'plugins',
			'tag' => 'plugins'
		),
	);
	/**
	 * Initialize all the things
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'request' ) );
		add_action( 'current_screen', array( $this, 'add_help_tab' ) );
	}

	/**
	* request
	*/
	public function request( $tag ) {
		$url = 'http://wordpress.tv/?wptvapi=videos.json&posts_per_page=4&tag=';
		$request = wp_remote_get( $url . $tag );
		$request = wp_remote_retrieve_body( $request );
		$request = json_decode( $request );

//		var_dump( $request );
//		var_dump(get_current_screen());
		return $request;
	}

	public function add_help_tab() {
		$screen = get_current_screen();
		foreach ( $this->places as $place ) {
			if ( $place['screen'] == $screen->base ) {
				$screen->add_help_tab( array(
						'id' => 'videos',
						'title' => 'Videos',
						'content' => '',
						'callback' => array( $this, 'display' ),
					)
				);
			}
		}
	}

	public function display() {
		$screen = get_current_screen();
		foreach ( $this->places as $place ) {
			if ( $place['screen'] == $screen->base ) {
				$videos = $this->request( $place['tag'] );
				if ( $videos ) {
					echo '<ul>';
					foreach ( $videos->videos as $video ) {
						echo '<li>';
						echo '<a href="' . $video->permalink . '">';
						echo '<img src="' . $video->thumbnail . '" />';
						echo '<span>' . $video->title . '</span>';
						echo '</a>';
						echo '</li>';
					}
					echo '</ul>';
				}
			}
		}
	}

}
$wptv = new wptv();
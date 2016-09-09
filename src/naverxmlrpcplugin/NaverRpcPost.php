<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-16
 * Time: 오후 11:10
 */

namespace naverxmlrpcplugin;


use DOMDocument;
use DOMXPath;
use naverxmlrpc\model\RpcParam;

class NaverRpcPost {
	public $post_meta_sync_id = 'naver_sync_id';
	public $post_meta_sync_categories = 'naver_sync_categories';
	public $post_meta_sync_tags = 'naver_sync_tags';

	/**
	 * @var Plugin
	 */
	public $plugin;

	public $post_ID;

	/**
	 * PluginPost constructor.
	 *
	 * @param $plugin
	 * @param $post_ID
	 */
	public function __construct( $plugin, $post_ID ) {
		$this->plugin = $plugin;

		if ( $post_ID ) {
			$this->post_ID = $post_ID;

			add_post_meta( $post_ID, $this->post_meta_sync_id, false, true );
			add_post_meta( $post_ID, $this->post_meta_sync_categories, false, true );
			add_post_meta( $post_ID, $this->post_meta_sync_tags, false, true );
		}
	}

	public function get_auto_post_categories() {
		$naver_cate = $this->plugin->plugin_option->get_option( 'category' );
		$categories = wp_get_post_categories( $this->post_ID );

		$c = array();

		foreach ( $categories as $term_id ) {
			$category = @$naver_cate[ $term_id ];

			$c[] = $category;
		}

		return join( ',', $c );
	}

	public function get_auto_post_tags() {
		$t = array();

		$tags = wp_get_post_tags( $this->post_ID );

		foreach ( $tags as $tag ) {
			$t[] = $tag->name;
		}

		return $t;
	}

	public function rpc_param_from_post() {
		$post = get_post( $this->post_ID );

		$param = new RpcParam();
		$param->setTitle( $post->post_title );
		$content = $post->post_content . '<div>'.wp_specialchars_decode( $this->plugin->plugin_option->get_option( 'footerContent' ).'</div>', ENT_QUOTES );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( "\\\"", "\"", $content );

		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		$xpath      = new DOMXPath( $dom );
		$p_elements = $xpath->evaluate( '/html/body//p' );

		foreach ( $p_elements as $p ) {
			$p->setAttribute( "style", "margin:10px 0;" );
		}

		$param->setDescription( $dom->saveHTML() );

		$naver_sync_id = $this->get_sync_id();

		if ( $naver_sync_id ) {
			$param->setPost_id( $naver_sync_id );
			$param->setCategories( $this->get_categories() );
			$param->setTags( $this->get_tags() );
		}

		return $param;
	}

	public function get_categories() {
		return get_post_meta( $this->post_ID, $this->post_meta_sync_categories, true );
	}

	public function get_tags() {
		return get_post_meta( $this->post_ID, $this->post_meta_sync_tags, true );
	}

	public function get_sync_id() {
		return get_post_meta( $this->post_ID, $this->post_meta_sync_id, true );
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-16
 * Time: 오후 2:36
 */

namespace naverxmlrpcplugin;

use naverxmlrpc\model\RpcParam;

class PluginPostHandler {
	/**
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * PostInterceptor constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function run() {
		$use_yn = $this->plugin->plugin_option->get_option( 'auto_write_use_yn' );

		if ( $use_yn ) {
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

		add_action( 'trashed_post', array( $this, 'trashed_post' ) );
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
		add_action( 'untrashed_post', array( $this, 'untrashed_post' ) );
	}

	public function untrashed_post( $post_ID ) {
		$naver_post = new NaverRpcPost( $this->plugin, $post_ID );

		$naver_sync_id = $naver_post->get_sync_id();

		if ( $naver_sync_id ) {
			$param = $naver_post->rpc_param_from_post();
			$param->setPublish( true );
			$this->plugin->getRpcClient()->editPost( $param );
		}
	}

	public function before_delete_post( $post_ID ) {
		$naver_post    = new NaverRpcPost( $this->plugin, $post_ID );
		$naver_sync_id = $naver_post->get_sync_id();

		if ( $naver_sync_id ) {
			$param = new RpcParam();
			$param->setPost_id( $naver_sync_id );

			$this->plugin->getRpcClient()->deletePost( $param );
		}
	}

	public function trashed_post( $post_ID ) {
		$naver_post    = new NaverRpcPost( $this->plugin, $post_ID );
		$naver_sync_id = $naver_post->get_sync_id();

		if ( $naver_sync_id ) {
			$param = $naver_post->rpc_param_from_post();
			$param->setPublish( false );

			$this->plugin->getRpcClient()->editPost( $param );
		}
	}

	public function save_post( $post_ID ) {
		if ( get_post_type( $post_ID ) != 'post' ) {
			return false;
		}

		$plugin_post = new NaverRpcPost( $this->plugin, $post_ID );

		$post_status = get_post_status( $post_ID );

		$param = $plugin_post->rpc_param_from_post();

		$naver_sync_id = $plugin_post->get_sync_id();

		if ( ( $post_status == 'pending' || $post_status == 'draft' ) && $naver_sync_id ) {
			$param->setPublish( false );
			$this->plugin->getRpcClient()->editPost( $param );

			return true;
		}

		if ( $post_status == 'publish' || $post_status == 'private' ) {
			if ( $post_status == 'private' ) {
				$param->setPublish( false );
			} else {
				$param->setPublish( true );
			}

			if ( $this->plugin->plugin_option->get_option( 'auto_write_use_yn' ) ) {
				$c = $plugin_post->get_auto_post_categories();
				$param->setCategories( $c );
			}

			$t = $plugin_post->get_auto_post_tags();

			$param->setTags( $t );

			$naver_pre_check_post = $this->plugin->getRpcClient()->getPost( $naver_sync_id );

			if ( $this->plugin->plugin_option->get_option( 'only_link_send' ) ) {
				$desc = sprintf( '<a href="%s">%s LINK</a>', get_permalink( $post_ID ), get_the_title( $post_ID ) );
				$param->setDescription( $desc );
			}

			/**
			 * @var $param RpcParam
			 */
			$param = apply_filters( 'pre_naver_post_send', $param, $post_ID );

			if ( $naver_sync_id && $naver_pre_check_post->postid ) {
				$this->plugin->getRpcClient()->editPost( $param );
			} else {
				$v = $this->plugin->getRpcClient()->newPost( $param );

				update_post_meta( $post_ID, $plugin_post->post_meta_sync_id, $v->postid );
			}

			update_post_meta( $post_ID, $plugin_post->post_meta_sync_categories, $param->categories );
			update_post_meta( $post_ID, $plugin_post->post_meta_sync_tags, $param->tags );
		}

		return true;
	}
}
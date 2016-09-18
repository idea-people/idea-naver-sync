<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-16
 * Time: 오전 2:10
 */

namespace naverxmlrpcplugin;


use naverxmlrpc\client\RpcClient;
use naverxmlrpc\config\RpcConfig;
use naverxmlrpc\model\RpcParam;

class Plugin {
	public $support_php_version = 5.3;

	public $plugin_name = 'IdeaNaverSync';
	public $plugin_author_email = 'ideapeople@ideapeople.co.kr';

	public $plugin_dir;
	public $plugin_url;

	public $__FILE__;

	/**
	 * @var PluginOption
	 */
	public $plugin_option;

	/**
	 * @var PluginPostHandler
	 */
	public $post_handler;

	/**
	 * @var PluginMetaBox
	 */
	public $meta_box;

	public $file_lang;

	public function __construct( $__FILE__ ) {
		$this->__FILE__ = $__FILE__;

		new PluginActivator( $this );

		add_action( 'plugins_loaded', array( $this, 'run' ) );
	}

	public function run() {
		$this->plugin_dir = plugin_dir_path( $this->__FILE__ );
		$this->plugin_url = plugin_dir_url( $this->__FILE__ );

		$this->plugin_option = new PluginOption( $this );
		$this->plugin_option->run();

		$this->post_handler = new PluginPostHandler( $this );
		$this->post_handler->run();

		$this->meta_box = new PluginMetaBox( $this );
		$this->meta_box->run();

		if ( $this->plugin_option->get_option( 'auto_image_change_use_yn' ) ) {
			$this->file_lang = new PluginFileLang();
			$this->file_lang->run();
		}

		return true;
	}

	/**
	 * @return bool|RpcClient
	 */
	public function getRpcClient() {
		static $client;

		$blogID = $this->plugin_option->get_option( 'blogID' );
		$apiKey = $this->plugin_option->get_option( 'apiKey' );

		if ( ! $blogID || ! $apiKey ) {
			return false;
		}

		if ( ! $client ) {
			$config = new RpcConfig( $blogID, $apiKey );
			$client = new RpcClient( $config );
		}

		return $client;
	}

}


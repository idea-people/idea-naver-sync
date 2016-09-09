<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-16
 * Time: 오후 7:59
 */

namespace naverxmlrpcplugin;

class PluginActivator {
	/**
	 * @var Plugin
	 */
	public $plugin;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		register_activation_hook( $this->plugin->__FILE__, array( $this, 'register_activation_hook' ) );
		register_deactivation_hook( $this->plugin->__FILE__, array( $this, 'register_deactivation_hook' ) );
	}

	public function register_activation_hook() {
		$this->version_check();

		$this->notification( '플러그인이 활성화 되었습니다.' );
	}

	public function register_deactivation_hook() {
		$this->notification( '플러그인이 비활성화 되었습니다.' );
	}

	public function version_check() {
		if ( phpversion() < $this->plugin->support_php_version ) {
			deactivate_plugins( plugin_basename( $this->plugin->__FILE__ ) );

			wp_die( sprintf( 'This plugin requires PHP Version %s.  Sorry about that.', $this->plugin->support_php_version ) );

			return false;
		}

		return true;
	}

	public function notification( $title ) {
		$message = array(
			sprintf( "<h2>%s <h2><h3>%s</h3>", $this->plugin->plugin_name, $title ),
			sprintf( 'URL : %s', home_url() ),
			sprintf( 'admin_email :%s', get_bloginfo( 'admin_email' ) )
		);

		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );

		wp_mail( $this->plugin->plugin_author_email, sprintf( '%s가 %s', $this->plugin->plugin_name, $title ), join( '<br/>', $message ) );

		remove_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );
	}
}
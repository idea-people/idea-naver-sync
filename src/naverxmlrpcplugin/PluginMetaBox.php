<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-18
 * Time: 오전 1:07
 */

namespace naverxmlrpcplugin;

use naverxmlrpc\model\RpcParam;

class PluginMetaBox {
	public $id = 'naver_meta_box';
	public $nonce = 'naver_meta_nonce';

	public $metaKeys = array(
		'naver_category'
	);

	/**
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * PluginMetaBox constructor.
	 *
	 * @param $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	public function run() {
		$use_yn = $this->plugin->plugin_option->get_option( 'auto_write_use_yn' );

		if ( ! $use_yn ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		}
	}

	public function add_meta_boxes() {
		add_meta_box(
			$this->id,
			esc_html__( '네이버싱크' ),
			array( $this, 'callback' ),
			'post',
			'side',
			'default'
		);
	}

	public function save_post( $post_id, $post ) {
		if ( ! isset( $_POST[ $this->nonce ] ) || ! wp_verify_nonce( $_POST[ $this->nonce ], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		$post_type = get_post_type_object( $post->post_type );

		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->metaKeys as $meta_key ) {
			$new_meta_value = ( isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '' );
			$meta_value     = get_post_meta( $post_id, $meta_key, true );

			if ( $new_meta_value && '' == $meta_value ) {
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );
			} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
				update_post_meta( $post_id, $meta_key, $new_meta_value );
			} elseif ( '' == $new_meta_value && $meta_value ) {
				delete_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		$n = get_post_meta( $post_id, 'naver_category', true );

		if ( $n != 'no-use' ) {
			add_filter( 'pre_naver_post_send', array( $this, 'pre_naver_post_send' ), 10, 2 );
			$this->plugin->post_handler->save_post( $post_id );
			remove_filter( 'pre_naver_post_send', array( $this, 'pre_naver_post_send' ) );
		}

		return false;
	}

	/**
	 * @param $post_ID
	 * @param $param RpcParam
	 *
	 * @return RpcParam
	 */
	public function pre_naver_post_send( $param, $post_ID ) {
		$param->setCategories( get_post_meta( $post_ID, 'naver_category', true ) );

		return $param;
	}

	public function callback( $args ) {
		wp_nonce_field( basename( __FILE__ ), $this->nonce );
		$naver_categories = $this->plugin->getRpcClient()->getCategories()->getChangedOptionCategory();
		?>
		<label for="naver_category"></label>
		<select name="naver_category" id="naver_category">
		<?php foreach ( $naver_categories as $naver_category ) { ?>
			<option
				<?php echo $naver_category['value'] == get_post_meta( $args->ID, 'naver_category', true ) ? 'selected' : '' ?>
				value="<?php echo $naver_category['value']; ?>"><?php echo $naver_category['name']; ?></option>
		<?php } ?>
		</select><?php }
}
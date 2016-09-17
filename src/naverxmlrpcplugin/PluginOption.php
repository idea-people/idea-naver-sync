<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-16
 * Time: 오전 12:00
 */

namespace naverxmlrpcplugin;

class PluginOption {
	public $options = array();

	public $option_group = 'naverxmlrpc_option_group';
	public $option_name = 'naverxmlrpc_option';
	public $slug = 'IdeaNaverSync';

	/**
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * PluginOption constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function run() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_init() {
		if ( ! empty ( $GLOBALS[ 'pagenow' ] ) && ( 'options-general.php' === $GLOBALS[ 'pagenow' ] || 'options.php' === $GLOBALS[ 'pagenow' ] ) ) {
			$this->register_settings();
		}
	}

	public function admin_menu() {
		$this->settings_init();
	}

	public function settings_init() {
		add_options_page(
			$this->plugin->plugin_name,
			$this->plugin->plugin_name,
			'manage_options',
			$this->slug,
			array( $this, 'settings_page' )
		);
	}

	public function settings_page() {
		require_once $this->plugin->plugin_dir . 'views/setting.php';
	}

	public function register_settings() {
		register_setting( $this->option_group, $this->option_name, array( $this, 'validate_option' ) );

		add_settings_section(
			'section_1',
			'플러그인 환경설정',
			array( $this, 'view_section_1' ),
			$this->slug
		);

		$this->create_field( 'field1', '네이버 블로그 ID', 'blogID', '_create_text_field' );
		$this->create_field( 'field2', '네이버 블로그 글쓰기 api키', 'apiKey', '_create_text_field' );
		$this->create_field( 'field4', '자동 글쓰기 사용여부', 'auto_write_use_yn', '_create_checkbox_field', '체크하시고 저장하시면 카테고리 설정이 가능합니다' );

		if ( $this->get_option( 'auto_write_use_yn' ) ) {
			$this->create_field( 'field5', '자동 글쓰기 카테고리 연결', 'category', '_create_category_connect_field' );
		}

		$this->create_field( 'field6', '자동 이미지명변경 사용여부', 'auto_image_change_use_yn', '_create_checkbox_field', '네이버로 이미지 전송이 안될경우 활성화 해주세요' );

		$this->create_field( 'field8', '링크만 전송하기', 'only_link_send', '_create_checkbox_field', '워드프레스 링크만 전송됩니다.' );

		$this->create_field( 'field7', '하단 고정 콘텐츠 설정', 'footerContent', '_create_wpeditor_field' );

	}

	public function view_section_1() {
		echo '<p>API키는 블로그 -&gt; 내메뉴 -&gt; 관리 -&gt; 메뉴글관리 -&gt; 글쓰기 API설정에 나오는 API연결 암호입니다.</p>';
	}

	public function _create_categories_field( $args ) {
		if ( @$_REQUEST[ 'type' ] == 'getCategories' ) {
			$args[ 'value' ] = join( ',', $this->plugin->getRpcClient()->getCategories()->getChangedOptionCategory() );
		}
		?>
		<a href="?page=<?php echo $this->slug ?>&type=getCategories" class="button button-primary">네이버에서 카테고리 가저오기</a>
		<?php
		printf(
			'<p><textarea type="text" style="width:100%%; height:100px;" name="%1$s[%2$s]" id="%3$s"  class="regular-text">%4$s</textarea></p>',
			$args[ 'option_name' ],
			$args[ 'name' ],
			$args[ 'label_for' ],
			$args[ 'value' ]
		);
	}

	public function _create_wpeditor_field( $args ) {
		echo '<p>네이버로 전송되는 포스트에는 항상 아래 내용이 포함되어 전달됩니다.</p> <br/>';

		$name = $args[ 'option_name' ] . '[' . $args[ 'name' ] . ']';
		wp_editor( wp_specialchars_decode( $args[ 'value' ], ENT_QUOTES ), $args[ 'name' ], array() );

		echo "<input type='hidden' name='{$name}' id='{$args['name']}_' value='{$args['value']}'/>";
		echo "<script>
	jQuery(document).ready(function(){
		jQuery('#naverSyncForm').submit(function(){
			var editorID = '{$args['name']}';
			var content = '';

		    if (jQuery('#wp-'+editorID+'-wrap').hasClass(\"tmce-active\"))
		        content = tinyMCE.get(editorID).getContent();
		    else
	        	content = jQuery('#'+editorID).val();

			jQuery('#{$args['name']}_').val(content);
		});
	});
</script>";
	}

	public function _create_category_connect_field( $args ) { ?>
		<table class="wp-list-table ">
			<thead>
			<tr>
				<th>카테고리</th>
				<th>네이버카테고리</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$categories       = get_categories( array( 'type' => 'post', 'hide_empty' => false ) );
			$naver_categories = $this->plugin->getRpcClient()->getCategories()->getChangedOptionCategory();

			foreach ( $categories as $category ) {
				$value = $args[ 'value' ][ $category->term_id ];
				?>
				<tr>
					<td><label for=""><?php echo $category->name ?></label></td>
					<td style="width:350px;">
						<select
							id=""
							name="<?php echo sprintf( '%1$s[%2$s][' . $category->term_id . ']', $args[ 'option_name' ], $args[ 'name' ] ) ?>">

							<?php foreach ( $naver_categories as $naver_category ) { ?>
								<option
									<?php echo $naver_category[ 'value' ] == $value ? 'selected' : '' ?>
									value="<?php echo $naver_category[ 'value' ]; ?>"><?php echo $naver_category[ 'name' ] ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
	}

	public function create_field( $id, $label, $option_name, $func, $message = '' ) {
		$this->options[ $option_name ] = '';

		add_settings_field( $id, $label, array( $this, $func ), $this->slug, 'section_1',
			array(
				'label_for'   => $option_name,
				'name'        => $option_name,
				'value'       => $this->get_option( $option_name ),
				'option_name' => $this->option_name,
				'message'     => $message
			)
		);
	}

	public function _create_checkbox_field( $args ) {
		printf(
			'<input type="checkbox" name="%1$s[%2$s]" id="%3$s" value="1" class="regular-checkbox" %5$s>',
			$args[ 'option_name' ],
			$args[ 'name' ],
			$args[ 'label_for' ],
			$args[ 'value' ],
			checked( 1, $this->get_option( $args[ 'name' ] ), false )
		);

		if ( ! empty( $args[ 'message' ] ) ) {
			echo '<p>' . $args[ 'message' ] . '</p>';
		}
	}

	public function _create_text_field( $args ) {
		printf(
			'<input type="text" name="%1$s[%2$s]" id="%3$s" value="%4$s" class="regular-text">',
			$args[ 'option_name' ],
			$args[ 'name' ],
			$args[ 'label_for' ],
			$args[ 'value' ]
		);
	}

	public function validate_option( $values ) {
		$out = array();

		foreach ( $this->options as $key => $value ) {
			if ( empty ( $values[ $key ] ) ) {
				$out[ $key ] = $value;
			} else {
				$out[ $key ] = $values[ $key ];
			}
		}

		return $out;
	}

	public function get_options() {
		return get_option( $this->option_name );
	}

	public function get_option( $key ) {
		$v = $this->get_options();

		if ( isset( $v[ $key ] ) ) {
			if ( is_array( $v[ $key ] ) ) {
				foreach ( $v[ $key ] as &$value ) {
					$value = esc_attr( $value );
				}

				return $v[ $key ];
			} else {
				return esc_attr( $v[ $key ] );
			}
		}

		return false;
	}
}


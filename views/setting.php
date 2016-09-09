<?php
global $insp;
?>
<div class="wrap">
	<h2><?php _e( $insp->plugin_name ) ?>
		<form method="post" style="display: inline-block;">
			<?php submit_button( '연결검사' ); ?>

			<input type="hidden" name="method" value="connection_check">
		</form>
		<span class="fb-like" data-href="https://www.facebook.com/ipeople2014/" data-layout="button" data-action="like"
		      data-size="large" data-show-faces="true" data-share="true" style="margin-left: 50px;"></span>

		<span id="fb-root"></span>
		<script>(function (d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.7&appId=1778149215766306";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
	</h2>
	<p class="notice" style="border:1px solid #dedede;padding:10px;">
		이 플러그인은 <em>아이디어피플</em> 에서 무료로 배포하는 플러그인 입니다. <br/>
		<strong>좋아요</strong>와 <strong>공유하기</strong>를 눌러주시면 개발사에 큰 힘이 됩니다. <br>
		문의 사항은 <a href="http://www.ideapeople.co.kr" target="_blank">http://www.ideapeople.co.kr</a>에 방문하셔서 문의해 주세요.
	</p>

	<?php if ( isset( $_REQUEST[ 'method' ] ) && $_REQUEST[ 'method' ] == 'connection_check' ) : $user_info = $insp->getRpcClient()->getUserInfo(); ?>
		<style>
			.error-msg {
				font-weight: bold;
				color: red;
			}
		</style>
		<div class="updated notice is-dismissible">
			<?php if ( $user_info ) { ?>
				<?php if ( $user_info->nickname ) : ?>
					<p><strong><?php echo $user_info->nickname ?>으로 블로그에 연결되었습니다.</strong>
						<a href="<?php echo $user_info->url ?>" target="_blank"><?php echo $user_info->url ?></a></p>
				<?php else : ?>
					<p class="error-msg">일시적 장애인것 같습니다.</p>
					<p><?php echo esc_attr( $user_info->response->raw_data ) ?></p>
				<?php endif; ?>
			<?php } else { ?>
				<p class="error-msg">오류가 발생했습니다.</p>
				<p><?php echo esc_attr( $user_info->response->raw_data ) ?></p>
			<?php } ?>
		</div>
	<?php endif; ?>

	<form action="options.php" method="POST" id="naverSyncForm">
		<?php
		settings_fields( $insp->plugin_option->option_group );

		do_settings_sections( $insp->plugin_option->slug ); ?>

		<?php submit_button(); ?>
	</form>
</div>
<?php
/*
Plugin Name: idea-naver-sync
Plugin URI: http://www.ideapeople.co.kr
Description: 네이버싱크 플러그인은 네이버 블로그에 워드프레스에 동일한 글이 등록.수정.삭제될수 있는 기능을 제공하는 플러그인 입니다.
Version: 1.0
Author: ideapeople
Author URI: http://www.ideapeople.co.kr
*/

use naverxmlrpcplugin\Plugin;

define( 'IDEA_NSC_PATH', plugin_dir_path( __FILE__ ) );

$loader = require_once IDEA_NSC_PATH . 'vendor/autoload.php';
$loader->add( 'naverxmlrpc\\', IDEA_NSC_PATH . '/src/' );
$loader->add( 'naverxmlrpcplugin\\', IDEA_NSC_PATH . '/src/' );

$insp = new Plugin( __FILE__ );

$GLOBALS[ 'insp' ] = $insp;
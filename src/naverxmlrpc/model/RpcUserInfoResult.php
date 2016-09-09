<?php
namespace naverxmlrpc\model;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:06
 */
class RpcUserInfoResult extends RpcResult {
	var $nickname;
	var $userid;
	var $email;
	var $url;
	var $lastname;
	var $firstname;

	public function __construct( $response ) {
		parent::__construct( $response );

		$this->res = @$response->val->me[ 'struct' ];

		$this->mapping();
	}
}
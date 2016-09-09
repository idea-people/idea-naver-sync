<?php
namespace naverxmlrpc\model;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:06
 */
class RpcPostResult extends RpcResult {
	var $postid;
	var $permaLink;
	var $author;
	var $username;
	var $categories;
	var $pubDate;
	var $guid;
	var $link;
	var $title;
	var $dateCreated;
	var $description;
	var $tags;

	public function __construct( $response ) {
		parent::__construct( $response );

		$this->res = @$response->val->me[ 'struct' ];

		$this->mapping();
	}
}
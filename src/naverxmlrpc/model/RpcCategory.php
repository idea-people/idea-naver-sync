<?php
namespace naverxmlrpc\model;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:07
 */
class RpcCategory {
	var $htmlUrl;
	var $description;
	var $title;

	public function __construct( $htmlUrl, $description, $title ) {
		$this->htmlUrl     = $htmlUrl;
		$this->description = $description;
		$this->title       = $title;
	}
}
<?php

namespace naverxmlrpc\model;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:07
 */
class RpcCategoryResult extends RpcResult {
	var $categories = array();

	public function __construct( $response ) {
		parent::__construct( $response );

		$this->res = $response->val->me[ 'array' ];

		if ( ! is_array( $this->res ) ) {
			return false;
		}

		foreach ( $this->res as $struct ) {
			$htmlUrl     = array_pop( $struct->me[ 'struct' ][ 'htmlUrl' ]->me );
			$description = array_pop( $struct->me[ 'struct' ][ 'description' ]->me );
			$title       = array_pop( $struct->me[ 'struct' ][ 'title' ]->me );

			$this->categories[] = new RpcCategory( $htmlUrl, $description, $title );
		}

		return true;
	}

	public function getChangedOptionCategory() {
		$result   = array();
		$result[] = array( 'name' => '사용안함', 'value' => 'no-use' );

		foreach ( $this->categories as $cate ) {
			$result[] = array( 'name' => $cate->title, 'value' => $cate->title );
		}

		return $result;
	}
}
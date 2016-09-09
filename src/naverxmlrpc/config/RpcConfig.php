<?php
namespace naverxmlrpc\config;
use PhpXmlRpc\Value;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:10
 */
class RpcConfig {
	public $blogID;
	public $apiKey;

	/**
	 * NaverRpcConfig constructor.
	 *
	 * @param $blogID
	 * @param $apiKey
	 */
	public function __construct( $blogID, $apiKey ) {
		$this->blogID = $blogID;
		$this->apiKey = $apiKey;
	}

	/**
	 * @return mixed
	 */
	public function getBlogID() {
		return new Value( $this->blogID );
	}

	/**
	 * @return mixed
	 */
	public function getApiKey() {
		return new Value( $this->apiKey );
	}
}
<?php

namespace naverxmlrpc\client;

use naverxmlrpc\config\RpcConfig;
use naverxmlrpc\model\RpcCategoryResult;
use naverxmlrpc\model\RpcMediaParam;
use naverxmlrpc\model\RpcEditPostResult;
use naverxmlrpc\model\RpcParam;
use naverxmlrpc\model\RpcPostResult;

use naverxmlrpc\model\RpcResult;
use naverxmlrpc\model\RpcUserInfoResult;
use PhpXmlRpc\Client;
use PhpXmlRpc\Request;
use PhpXmlRpc\Value;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:10
 */
class RpcClient {
	static $RPC_URL = 'https://api.blog.naver.com/xmlrpc';

	/**
	 * @var RpcConfig
	 */
	public $config;

	/**
	 * @var Client
	 */
	public $client;

	public function __construct( $config = null ) {
		$this->client = new Client( self::$RPC_URL );
		$this->client->setSSLVerifyPeer( false );
		if ( $config ) {
			$this->config = $config;
		}
	}

	public function getUsersBlog() {
		$response = $this->client->send( new Request( 'blogger.getUsersBlogs',
				array(
					$this->config->getApiKey(),
					$this->config->getBlogID(),
					$this->config->getApiKey()
				) )
		);

		return new RpcResult( $response );
	}

	public function getUserInfo() {
		$response = $this->client->send( new Request( 'blogger.getUserInfo',
				array(
					$this->config->getApiKey(),
					$this->config->getBlogID(),
					$this->config->getApiKey()
				) )
		);

		return new RpcUserInfoResult( $response );
	}

	public function getPost( $postID ) {
		$response = $this->client->send( new Request( 'metaWeblog.getPost',
				array(
					new Value( $postID ),
					$this->config->getBlogID(),
					$this->config->getApiKey()
				) )
		);

		return new RpcPostResult( $response );//$response->value()->me[ 'struct' ];
	}

	public function editPost( RpcParam $param ) {
		$response = $this->client->send( new Request( 'metaWeblog.editPost',
				array(
					new Value( $param->post_id ),
					$this->config->getBlogID(),
					$this->config->getApiKey(),
					$param->getStruct(),
					$param->isPublish()
				) )
		);

		return new RpcPostResult( $response );
	}

	public function deletePost( RpcParam $param ) {
		$response = $this->client->send( new Request( 'blogger.deletePost',
				array(
					new Value( $this->config->apiKey ),
					new Value( $param->post_id ),
					$this->config->getBlogID(),
					$this->config->getApiKey(),
					$param->isPublish()
				) )
		);

		return new RpcPostResult( $response );
	}

	public function newPost( RpcParam $param ) {
		$response = $this->client->send( new Request( 'metaWeblog.newPost',
				array(
					$this->config->getBlogID(),
					$this->config->getBlogID(),
					$this->config->getApiKey(),
					$param->getStruct(),
					$param->isPublish()
				) )
		);

		return new RpcEditPostResult( $response );
	}

	public function getCategories() {
		$response = $this->client->send( new Request( 'metaWeblog.getCategories',
				array(
					$this->config->getBlogID(),
					$this->config->getBlogID(),
					$this->config->getApiKey()
				) )
		);

		return new RpcCategoryResult( $response );
	}

	public function newMediaObject( RpcMediaParam $param ) {
		$response = $this->client->send( new Request( 'metaWeblog.newMediaObject',
				array(
					$this->config->getBlogID(),
					$this->config->getBlogID(),
					$this->config->getApiKey(),
					$param->getStruct()
				) )
		);

		return $response;
	}
}
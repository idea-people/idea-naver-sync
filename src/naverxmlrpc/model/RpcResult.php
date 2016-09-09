<?php
namespace naverxmlrpc\model;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:06
 */
class RpcResult {
	var $response;
	var $res;

	public function __construct( $response ) {
		$this->response = $response;

		if ( $this->isError() ) {
		}
	}

	public function isError() {
		return $this->response->errno != 0;
	}

	public function getErrMessage() {
		return $this->response->errstr;
	}

	public function getValue( $key ) {
		if ( is_array( @$this->res[ $key ]->me ) ) {
			return array_pop( $this->res[ $key ]->me );
		}

		return false;
	}

	public function isSuccess() {
		return ! $this->isError();
	}

	public function mapping() {
		$reflection = new ReflectionClass( $this );
		$props      = $reflection->getProperties();

		foreach ( $props as $prop ) {
			if ( $prop->getName() != 'response' && $prop->getName() != 'res' ) {
				$value = $this->getValue( $prop->getName() );

				$prop->setValue( $this, $value );
			}
		}
	}
}
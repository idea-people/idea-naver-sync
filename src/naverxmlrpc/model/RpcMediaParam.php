<?php
namespace naverxmlrpc\model;
use PhpXmlRpc\Value;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:05
 */
class RpcMediaParam {
	var $file;
	var $name;
	var $type;
	var $bits;

	public function __construct( $file ) {
		$this->file = $file;
		$this->name = $this->getFileName();
		$this->type = $this->getMimeType( $this->getExtension() );
		$this->bits = file_get_contents( $file, FILE_BINARY );
	}

	public function getExtension() {
		$fileName = $this->getFileName();
		$ext      = substr( strrchr( $fileName, "." ), 1 );
		$ext      = strtolower( $ext );

		return $ext;
	}

	public function getFileName() {
		$n = explode( '/', $this->file );
		$n = $n[ count( $n ) - 1 ];

		return $n;
	}

	public function getStruct() {
		return new Value( array(
			'name' => new Value( $this->name ),
			'type' => new Value( $this->type ),
			'bits' => new Value( $this->bits, 'base64' )
		), 'struct' );
	}

	public function setFile( $file ) {
		$this->file = $file;
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function setType( $type ) {
		$this->type = $type;
	}

	public function setBits( $bits ) {
		$this->bits = $bits;
	}

	public function getMimeType( $type ) {
		$types = array(
			'gif'  => 'image/gif',
			'png'  => 'image/png',
			'jpeg' => 'image/jpeg',
			'jpg'  => 'image/jpeg',
			'bmp'  => 'image/bmp'
		);

		return $types[ $type ];
	}
}
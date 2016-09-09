<?php
namespace naverxmlrpc\model;

use PhpXmlRpc\Value;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:05
 */
class RpcParam {
	var $post_id = '';

	var $publish = true;
	var $title = '';
	var $description = '';
	var $categories = array();
	var $tags = array();

	/**
	 * @param array $tags
	 */
	public function setTags( $tags ) {
		$this->tags = $tags;
	}

	public function getStruct() {
		$param_tags     = array();
		$param_category = array();

		if ( is_string( $this->categories ) ) {
			$categories = explode( ',', $this->categories );
		} else {
			$categories = $this->categories;
		}

		if ( is_array( $categories ) ) {
			foreach ( $categories as &$category ) {
				if ( ! empty( $category ) ) {
					$param_category[] = new Value( $category );
				}
			}
		}

		if ( is_string( $this->tags ) ) {
			$tags = explode( ',', $this->tags );
		} else {
			$tags = $this->tags;
		}

		if ( is_array( $tags ) ) {
			foreach ( $tags as &$tag ) {
				$param_tags[] = new Value( $tag );
			}
		}

		$out = new Value( array(
			'title'       => new Value( $this->title ),
			'description' => new Value( $this->description ),
			'categories'  => new Value( $param_category, 'array' ),
			'tags'        => new Value( $param_tags, 'array' ),
		), 'struct' );

		return $out;
	}

	public function setPost_id( $post_id ) {
		$this->post_id = $post_id;
	}

	public function setPublish( $publish ) {
		$this->publish = $publish;
	}

	public function setTitle( $title ) {
		$this->title = $title;
	}

	public function setDescription( $description ) {
		$this->description = $description;
	}

	public function addCategory( $name ) {
		$this->categories[] = $name;
	}

	public function setCategories( $categories ) {
		$this->categories = $categories;
	}

	public static function fromArray( $args ) {
		$param = new RpcParam();
		$param->setPost_id( $args[ 'post_id' ] );
		$param->setDescription( $args[ 'description' ] );
		$param->setPublish( $args[ 'publish' ] );
		$param->setTitle( $args[ 'title' ] );
		$param->setCategories( $args[ 'categories' ] );

		return $param;
	}

	/**
	 * @return boolean
	 */
	public function isPublish() {
		return new Value( $this->publish, 'boolean' );
	}

	public function toArray() {
		return array(
			'categories'  => $this->categories,
			'title'       => $this->title,
			'description' => $this->description,
			'tags'        => $this->tags,
			'publish'     => $this->publish
		);
	}
}
<?php
namespace naverxmlrpc\model;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-15
 * Time: 오전 6:07
 */
class RpcEditPostResult extends RpcResult
{
    var $postid;

    public function __construct($response)
    {
        parent::__construct($response);

        $this->postid = $response->val->me['string'];
    }
}
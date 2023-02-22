<?php
namespace HamZone\QQAuth\Api\Controllers;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class QQResourceController implements ResourceOwnerInterface {
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;
    /**
     * Creates new resource owner.
     *
     * @param array  $response
     */
    public function __construct(array $response = array()) {
        $this->response = $response;
    }
    /**
     * Get resource owner id
     *
     * @return string|null
     */
    public function getId(){
        return $this->response['openid'] ?: null;
    }

    public function getNickname()
    {
        return $this->response['nickname'] ?: null;
    }

    public function getHeadImgUrl()
    {
        return $this->response['figureurl_qq_1'] ?: null;
    }

    public function getSex()
    {
        return $this->response['gender'] ?: null;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray(){
        return $this->response;
    }
}
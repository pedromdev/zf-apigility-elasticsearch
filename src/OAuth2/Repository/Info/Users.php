<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository\Info;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
trait Users
{
    
    /**
     * @return string
     */
    public function getIndex()
    {
        return 'oauth2';
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return 'users';
    }
}

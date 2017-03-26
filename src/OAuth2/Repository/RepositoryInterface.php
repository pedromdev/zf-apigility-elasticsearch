<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
interface RepositoryInterface
{
    
    /**
     * @return array
     */
    public function search();
    
    /**
     * @return array
     */
    public function get();
}

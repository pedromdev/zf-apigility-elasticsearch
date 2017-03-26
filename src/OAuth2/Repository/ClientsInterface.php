<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
interface ClientsInterface extends RepositoryInterface
{
    
    /**
     * @param string $clientId
     * @return $this
     */
    public function byClientId($clientId);
    
    /**
     * @param string $scope
     * @return $this
     */
    public function hasScope($scope);
}

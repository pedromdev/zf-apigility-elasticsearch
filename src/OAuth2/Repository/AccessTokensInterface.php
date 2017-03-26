<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
interface AccessTokensInterface extends RepositoryInterface
{
    
    /**
     * @param string $accessToken
     * @return $this
     */
    public function byAccessToken($accessToken);
    
    /**
     * @param string $userId
     * @return $this
     */
    public function fromUser($userId);
    
    /**
     * @param string $clientId
     * @return $this
     */
    public function fromClient($clientId);
    
    /**
     * @param string $scope
     * @return $this
     */
    public function hasScope($scope);
}

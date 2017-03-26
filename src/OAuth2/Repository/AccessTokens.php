<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class AccessTokens extends AbstractRepository implements AccessTokensInterface
{
    
    /**
     * @param string $accessToken
     * @return $this
     */
    public function byAccessToken($accessToken)
    {
        $this->parameters()->byId($accessToken);
        return $this;
    }

    /**
     * @param string $clientId
     * @return $this
     */
    public function fromClient($clientId)
    {
        $this->parameters()
            ->query()
            ->must(['term' => ['_client_id' => $clientId]]);
        return $this;
    }

    /**
     * @param string $userId
     * @return $this
     */
    public function fromUser($userId)
    {
        $this->parameters()
            ->query()
            ->must(['term' => ['_user_id' => $userId]]);
        return $this;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function hasScope($scope)
    {
        $this->parameters()
            ->query()
            ->must(['term' => ['_scope' => $scope]]);
        return $this;
    }

}

<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;


/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class Clients extends AbstractRepository implements ClientsInterface
{
    
    /**
     * @param string $clientId
     * @return $this
     */
    public function byClientId($clientId)
    {
        $this->parameters()->byId($clientId);
        return $this;
    }
    
    /**
     * @param string $scope
     * @return $this
     */
    public function hasScope($scope)
    {
        $this->parameters()->query()->match(['term' => ['_scopes' => $scope]]);
        return $this;
    }
}

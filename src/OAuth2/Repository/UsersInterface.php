<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
interface UsersInterface extends RepositoryInterface
{
    
    /**
     * @param string $username
     * @return $this
     */
    public function byUsername($username);
    
    /**
     * @param string $firstName
     * @return $this
     */
    public function byFirstName($firstName);
    
    /**
     * @param string $lastName
     * @return $this
     */
    public function byLastName($lastName);
}

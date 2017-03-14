<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class Users extends AbstractRepository implements UsersInterface
{

    /**
     * @param string $username
     * @return $this
     */
    public function byUsername($username)
    {
        $this->must($this->queryUsername($username))
            ->limitedTo(1)
            ->fromResult(0);
        return $this;
    }
    
    /**
     * @param string $firstName
     * @return $this
     */
    public function byFirstName($firstName)
    {
        return $this->match(['term' => ['first_name' => $firstName]]);
    }
    
    /**
     * @param string $lastName
     * @return $this
     */
    public function byLastName($lastName)
    {
        return $this->match(['term' => ['last_name' => $lastName]]);
    }

    /**
     * @param string $username
     * @return array
     */
    private function queryUsername($username)
    {
        return [
            'term' => [
                'username' => $username,
            ],
        ];
    }
}

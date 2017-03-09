<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

use Elasticsearch\Client;
use ElasticsearchModule\Apigility\Builder\Parameters;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class Users implements UsersInterface
{
    
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var Parameters
     */
    private $parameters;
    
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function byUsername($username)
    {
        $this->must($this->queryUsername($username))
            ->withMaxResults(1)
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
     * @return Parameters
     */
    protected function parameters()
    {
        if (is_null($this->parameters)) {
            $this->parameters = Parameters::getInstance('oauth2', 'users');
        }
        return $this->parameters;
    }
    
    /**
     * @param array $param
     * @param array ...$nextParams
     * @return $this
     */
    protected function must(array $param, ...$nextParams)
    {
        $query = $this->parameters()->query();
        $params = func_get_args();
        call_user_func_array([$query, 'must'], $params);
        return $this;
    }
    
    /**
     * @param array $param
     * @param array ...$nextParams
     * @return $this
     */
    protected function match(array $param, ...$nextParams)
    {
        $query = $this->parameters()->query();
        $params = func_get_args();
        call_user_func_array([$query, 'match'], $params);
        return $this;
    }
    
    /**
     * @param int $maxResults
     * @return $this
     */
    protected function withMaxResults($maxResults)
    {
        $this->parameters()->withMaxResults($maxResults);
        return $this;
    }
    
    /**
     * @param int $from
     * @return $this
     */
    protected function fromResult($from)
    {
        $this->parameters()->fromResult($from);
        return $this;
    }

    /**
     * @return array
     */
    public function search()
    {
        return $this->client->search($this->parameters()->build());
    }

    private function queryUsername($username)
    {
        return [
            'term' => [
                'username' => $username,
            ],
        ];
    }
}

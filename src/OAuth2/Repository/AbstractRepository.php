<?php

namespace ElasticsearchModule\Apigility\OAuth2\Repository;

use Elasticsearch\Client;
use ElasticsearchModule\Apigility\Builder\Parameters;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    
    /**
     * @var Client
     */
    private $elasticClient;
    
    /**
     * @var string
     */
    private $index;
    
    /**
     * @var string
     */
    private $type;
    
    /**
     * @var Parameters
     */
    private $parameters;
    
    /**
     * @param Client $elasticClient
     */
    public function __construct(Client $elasticClient, $index, $type)
    {
        $this->elasticClient = $elasticClient;
        $this->index = $index;
        $this->type = $type;
    }
    
    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return $this->elasticClient->get($this->parameters()->build());
    }

    /**
     * {@inheritDoc}
     */
    public function search()
    {
        return $this->elasticClient->search($this->parameters()->build());
    }

    /**
     * @return Client
     */
    public function getElasticClient()
    {
        return $this->elasticClient;
    }
    
    /**
     * @return Parameters
     */
    protected function parameters()
    {
        if (is_null($this->parameters)) {
            $this->parameters = Parameters::getInstance($this->index, $this->type);
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
    protected function limitedTo($maxResults)
    {
        $this->parameters()->limitedTo($maxResults);
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
}

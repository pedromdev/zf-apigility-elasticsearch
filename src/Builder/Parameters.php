<?php

namespace ElasticsearchModule\Apigility\Builder;

use Zend\Stdlib\ArrayUtils;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
final class Parameters implements BuilderInterface
{
    /**
     * @var array
     */
    private $params = [
        'body' => [],
    ];
    
    /**
     * @var Query
     */
    private $query;

    /**
     * Instances of this class cannot be created externality
     */
    private function __construct() {}
    
    /**
     * @param type $index
     * @param type $type
     * @return Parameters
     */
    public static function getInstance($index, $type)
    {
        $instance = new self();
        $instance->params['index'] = $index;
        $instance->params['type'] = $type;
        return $instance;
    }
    
    /**
     * @return Query
     */
    public function query()
    {
        if (is_null($this->query)) {
            $this->query = Query::getInstance();
        }
        return $this->query;
    }
    
    /**
     * @param int $maxResults
     * @return Parameters
     */
    public function withMaxResults($maxResults)
    {
        $this->params['body']['size'] = $maxResults;
        return $this;
    }
    
    /**
     * @param int $from
     * @return Parameters
     */
    public function fromResult($from)
    {
        $this->params['body']['from'] = $from;
        return $this;
    }
    
    /**
     * @return array
     */
    public function build()
    {
        $params = ArrayUtils::merge($this->params, [
            'body' => [
                'query' => $this->query()->build(),
            ],
        ]);
        return $params;
    }
}

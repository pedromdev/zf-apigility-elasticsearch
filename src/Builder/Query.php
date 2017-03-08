<?php

namespace ElasticsearchModule\Apigility\Builder;

use Zend\Stdlib\ArrayUtils;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
final class Query implements BuilderInterface
{
    
    /**
     * @var array
     */
    private $query = [];
    
    /**
     * Instances of this class cannot be created externality
     */
    private function __construct() {}
    
    /**
     * @return Query
     */
    public static function getInstance()
    {
        return new self();
    }
    
    /**
     * @param array $param
     * @param array ...$nextParams
     * @return Query
     */
    public function must(array $param, ...$nextParams)
    {
        $params = func_get_args();
        $this->query = ArrayUtils::merge($this->query, [
            'bool' => [
                'must' => $params
            ],
        ]);
        return $this;
    }
    
    /**
     * @return array
     */
    public function build()
    {
        return $this->query;
    }
}

<?php

namespace ElasticsearchModule\Apigility\Builder;

use Zend\Stdlib\ArrayUtils;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 * 
 * @method $this must(array $param, ...$nextParams)
 * @method $this match(array $param, ...$nextParams)
 */
final class Query implements BuilderInterface
{
    
    /**
     * @var array
     */
    private $query = [];
    
    /**
     * @var array
     */
    private $boolQueryMethods = ['must', 'match'];
    
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
     * @param string $name
     * @param mixed $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->boolQueryMethods)) {
            $arguments = array_slice(func_get_args(), 1);
            call_user_func_array([$this, 'boolQuery'], array_merge([$name], $arguments[0]));
        }
        return $this;
    }
    
    /**
     * @return array
     */
    public function build()
    {
        return $this->query;
    }
    
    /**
     * @param string $name
     * @param array $param
     * @param array ...$nextParams
     * @return $this
     */
    protected function boolQuery($name, $param = [],...$nextParams)
    {
        if (!isset($this->query['bool'][$name])) {
            $this->query['bool'][$name] = [];
        }
        $this->query['bool'][$name] = ArrayUtils::merge(
            $this->query['bool'][$name],
            array_slice(func_get_args(), 1)
        );
        return $this;
    }
}

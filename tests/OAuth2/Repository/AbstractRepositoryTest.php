<?php

namespace ElasticsearchModuleTest\Apigility\OAuth2\Repository;

use Elasticsearch\Client;
use ElasticsearchModule\Apigility\Builder\Parameters;
use PHPUnit_Framework_MockObject_Builder_InvocationMocker;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Crypt\Password\Bcrypt;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
abstract class AbstractRepositoryTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @param string $word
     * @return string
     */
    protected function getCryptedWord($word)
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->create($word);
    }
    
    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected function createClientMock(array $methods)
    {
        return $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
    
    /**
     * @param PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $methodName
     * @param mixed $willReturn
     * @param mixed ...$with
     * @return PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    protected function mockMethod($mock, $methodName, $willReturn = null, ...$with)
    {
        $with = array_slice(func_get_args(), 3);
        $method = $mock->expects($this->any())
            ->method($methodName);
        
        if (!is_null($willReturn)) {
            $method->willReturn($willReturn);
        }
        
        if (count($with) > 0) {
            call_user_func_array([$method, 'with'], $with);
        }
        return $method;
    }
    
    /**
     * 
     * @param PHPUnit_Framework_MockObject_MockObject $client
     * @param array $item
     * @param array ...$nextItem
     * @return PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    protected function mockSearchResult($client, array $item, ...$nextItem)
    {
        return $this->mockMethod($client, 'search', [
            'hits' => [
                'hits' => array_slice(func_get_args(), 1),
            ],
        ]);
    }
    
    /**
     * 
     * @param PHPUnit_Framework_MockObject_MockObject $client
     * @param array $return
     * @return PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    protected function mockGetResult($client, array $return)
    {
        return $this->mockMethod($client, 'get', $return);
    }
    
    /**
     * @param array $item
     */
    protected function assertSource(array $item)
    {
        $this->assertArrayHasKey('_source', $item);
        $keys = $this->getSourceKeys();
        
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $item['_source']);
        }
    }
    
    /**
     * @return Parameters
     */
    protected function parameters()
    {
        return Parameters::getInstance($this->getIndex(), $this->getType());
    }
    
    /**
     * @return array
     */
    abstract protected function getSourceKeys();
    
    /**
     * @return string
     */
    abstract public function getIndex();
    
    /**
     * @return string
     */
    abstract public function getType();
}

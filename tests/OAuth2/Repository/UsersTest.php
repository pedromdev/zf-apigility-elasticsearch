<?php

namespace ElasticsearchModuleTest\Apigility\OAuth2\Repository;

use Elasticsearch\Client;
use ElasticsearchModule\Apigility\Builder\Parameters;
use ElasticsearchModule\Apigility\OAuth2\Repository\Users;
use PHPUnit_Framework_MockObject_Builder_InvocationMocker;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\Crypt\Password\Bcrypt;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class UsersTest extends PHPUnit_Framework_TestCase
{
    
    public function testGetUserByUsername()
    {
        $username = 'testuser';
        $password = $this->getCryptedWord('testpass');
        $parameters = Parameters::getInstance('oauth2', 'users')
            ->withMaxResults(1)
            ->fromResult(0);
        $parameters->query()
            ->must(['term' => ['username' => $username]]);
        $client = $this->createClientMock(['search']);
        $this->mockSearchResult($client, $this->getUserStruct('Test', 'User', $username, $password))
            ->with($parameters->build());
        $users = new Users($client);
        
        $result = $users->byUsername('testuser')
            ->search();
        $user = $result['hits']['hits'][0];
        
        $this->assertArrayHasKey('_source', $user);
        $this->assertUserSource($user['_source']);
    }
    
    public function testSearchByFirstNameOrLastName()
    {
        $username = 'testuser';
        $password = $this->getCryptedWord('testpass');
        $parameters = Parameters::getInstance('oauth2', 'users');
        $parameters->query()
            ->match(
                ['term' => ['first_name' => 'test']],
                ['term' => ['last_name' => 'user']]
            );
        $client = $this->createClientMock(['search']);
        $this->mockSearchResult(
            $client,
            $this->getUserStruct('Test', 'User', $username, $password),
            $this->getUserStruct('Another', 'User', "another_user", $password)
        )->with($parameters->build());
        $users = new Users($client);
        
        $result = $users->byFirstName('test')
            ->byLastName('user')
            ->search();
        $filteredUsers = $result['hits']['hits'];
        
        $this->assertEquals(2, count($filteredUsers));
        $this->assertArrayHasKey('_source', $filteredUsers[0]);
        $this->assertArrayHasKey('_source', $filteredUsers[1]);
        $this->assertUserSource($filteredUsers[0]['_source']);
        $this->assertUserSource($filteredUsers[1]['_source']);
    }
    
    /**
     * @param string $word
     * @return string
     */
    private function getCryptedWord($word)
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->create($word);
    }
    
    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected function createClientMock(array $methods = [])
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
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $password
     * @return array
     */
    private function getUserStruct($firstName, $lastName, $username, $password)
    {
        return [
            '_index' => 'oauth2',
            '_type' => 'users',
            '_id' => 'AaBbCcDdEeFf',
            '_version' => 1,
            'found' => true,
            '_source' => [
                'username' => $username,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ],
        ];
    }
    
    /**
     * @param array $source
     */
    private function assertUserSource(array $source)
    {
        $this->assertArrayHasKey('username', $source);
        $this->assertArrayHasKey('password', $source);
        $this->assertArrayHasKey('first_name', $source);
        $this->assertArrayHasKey('last_name', $source);
    }
}

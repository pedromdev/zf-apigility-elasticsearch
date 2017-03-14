<?php

namespace ElasticsearchModuleTest\Apigility\OAuth2\Repository;

use ElasticsearchModule\Apigility\Builder\Parameters;
use ElasticsearchModule\Apigility\OAuth2\Repository\Info\Users as UsersInfo;
use ElasticsearchModule\Apigility\OAuth2\Repository\Users;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class UsersTest extends AbstractRepositoryTest
{
    use UsersInfo;
    
    public function testGetUserByUsername()
    {
        $username = 'testuser';
        $password = $this->getCryptedWord('testpass');
        $parameters = Parameters::getInstance('oauth2', 'users')
            ->limitedTo(1)
            ->fromResult(0);
        $parameters->query()
            ->must(['term' => ['username' => $username]]);
        $client = $this->createClientMock(['search']);
        $this->mockSearchResult($client, $this->getUserStruct('Test', 'User', $username, $password))
            ->with($parameters->build());
        $users = new Users($client, 'oauth2', 'users');
        
        $result = $users->byUsername('testuser')
            ->search();
        $user = $result['hits']['hits'][0];
        
        $this->assertArrayHasKey('_source', $user);
        $this->assertSource($user);
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
        $users = new Users($client, 'oauth2', 'users');
        
        $result = $users->byFirstName('test')
            ->byLastName('user')
            ->search();
        $filteredUsers = $result['hits']['hits'];
        
        $this->assertEquals(2, count($filteredUsers));
        $this->assertSource($filteredUsers[0]);
        $this->assertSource($filteredUsers[1]);
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
                'created_at' => '2017-03-14T17:37:41Z+0300',
                'updated_at' => '2017-03-14T17:47:56Z+0300',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSourceKeys()
    {
        return [
            'username',
            'password',
            'first_name',
            'last_name',
            'created_at',
            'updated_at',
        ];
    }
}

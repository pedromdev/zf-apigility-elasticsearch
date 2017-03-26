<?php

namespace ElasticsearchModuleTest\Apigility\OAuth2\Repository;

use ElasticsearchModule\Apigility\Builder\Parameters;
use ElasticsearchModule\Apigility\OAuth2\Repository\AccessTokens;
use ElasticsearchModule\Apigility\OAuth2\Repository\Info\AccessTokens as AccessTokensInfo;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class AccessTokensTest extends AbstractRepositoryTest
{
    
    use AccessTokensInfo;
    
    public function testGetAccessToken()
    {
        $accessTokenStr = 'a1b2c3d4e5f6a6b5c4d3e2f1';
        $parameters = $this->parameters()
            ->byId($accessTokenStr);
        $elasticClient = $this->createClientMock(['get']);
        $this->mockGetResult(
            $elasticClient,
            $this->getAccessTokenStruct($accessTokenStr, 'test_client_id', 'AaBbCcDdEeFf')
        )->with($parameters->build());
        $accessTokens = new AccessTokens($elasticClient, $this->getIndex(), $this->getType());
        
        $accessToken = $accessTokens->byAccessToken($accessTokenStr)
            ->get();
        
        $this->assertArrayHasKey('found', $accessToken);
        $this->assertTrue($accessToken['found'], 'The access token couldn\'t be found');
        $this->assertArrayHasKey('_id', $accessToken);
        $this->assertEquals($accessTokenStr, $accessToken['_id']);
        $this->assertSource($accessToken);
    }
    
    public function testSearchAccessTokensByUserOrClientId()
    {
        $userId = 'test_user';
        $clientId = 'test_client';
        $parameters = $this->parameters();
        $parameters->query()
            ->must(['term' => ['_user_id' => $userId]])
            ->must(['term' => ['_client_id' => $clientId]]);
        $elasticClient = $this->createClientMock(['search']);
        $this->mockSearchResult(
            $elasticClient,
            $this->getAccessTokenStruct('a1b2c3d4e5f6a6b5c4d3e2f1', $clientId, 'AaBbCcDdEeFf'),
            $this->getAccessTokenStruct('a6b5c4d3e2f1a1b2c3d4e5f6', 'test_client_id', $userId)
        );
        $accessTokens = new AccessTokens($elasticClient, $this->getIndex(), $this->getType());
        
        $listOfAccessTokens = $accessTokens->fromUser($userId)
            ->fromClient($clientId)
            ->search();
        
        $this->assertEquals(2, count($listOfAccessTokens['hits']['hits']));
        $this->assertSource($listOfAccessTokens['hits']['hits'][0]);
        $this->assertSource($listOfAccessTokens['hits']['hits'][1]);
    }
    
    public function testSearchAccessTokensByScope()
    {
        $userId = 'test_user';
        $clientId = 'test_client';
        $parameters = $this->parameters();
        $parameters->query()
            ->must(['term' => ['_scope' => 'unit']]);
        $elasticClient = $this->createClientMock(['search']);
        $this->mockSearchResult(
            $elasticClient,
            $this->getAccessTokenStruct('a1b2c3d4e5f6a6b5c4d3e2f1', $clientId, 'AaBbCcDdEeFf'),
            $this->getAccessTokenStruct('a6b5c4d3e2f1a1b2c3d4e5f6', 'test_client_id', $userId)
        );
        $accessTokens = new AccessTokens($elasticClient, $this->getIndex(), $this->getType());
        
        $listOfAccessTokens = $accessTokens->hasScope('unit')
            ->search();
        
        $this->assertEquals(2, count($listOfAccessTokens['hits']['hits']));
        $this->assertSource($listOfAccessTokens['hits']['hits'][0]);
        $this->assertSource($listOfAccessTokens['hits']['hits'][1]);
    }
    
    protected function getSourceKeys()
    {
        return [
            '_client_id',
            '_user_id',
            '_scopes',
            'created_at',
            'expires',
        ];
    }
    
    private function getAccessTokenStruct($accessToken, $clientId, $userId)
    {
        return [
            '_index' => 'oauth2',
            '_type' => 'access_tokens',
            '_id' => $accessToken,
            'found' => true,
            '_source' => [
                '_client_id' => $clientId,
                '_user_id' => $userId,
                '_scopes' => [
                    'unit',
                    'test',
                ],
                'created_at' => '2016-03-14T19:01:31Z+0300',
                'expires' => '2016-03-14T20:01:31Z+0300',
            ],
        ];
    }

}

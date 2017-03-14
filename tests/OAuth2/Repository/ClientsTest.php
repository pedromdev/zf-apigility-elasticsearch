<?php

namespace ElasticsearchModuleTest\Apigility\OAuth2\Repository;

use ElasticsearchModule\Apigility\Builder\Parameters;
use ElasticsearchModule\Apigility\OAuth2\Repository\Clients;
use ElasticsearchModule\Apigility\OAuth2\Repository\Info\Clients as ClientsInfo;

/**
 * @author Pedro Alves <pedro.m.develop@gmail.com>
 */
class ClientsTest extends AbstractRepositoryTest
{
    use ClientsInfo;
    
    public function testGetByClientId()
    {
        $clientId = "test_client_id";
        $clientSecret = $this->getCryptedWord("test_client_secret");
        $parameters = Parameters::getInstance('oauth2', 'clients')
            ->byId($clientId);
        $elasticClient = $this->createClientMock(['get']);
        $this->mockGetResult(
            $elasticClient,
            $this->getClientStruct($clientId, $clientSecret, 'AaBbCcDdEeFf')
        )->with($parameters->build());
        $clients = new Clients($elasticClient, 'oauth2', 'clients');
        
        $client = $clients->byClientId($clientId)
            ->get();
        
        $this->assertArrayHasKey('found', $client);
        $this->assertTrue($client['found'], 'The client couldn\'t be found');
        $this->assertArrayHasKey('_id', $client);
        $this->assertEquals($clientId, $client['_id']);
        $this->assertSource($client);
    }
    
    public function testSearchByScope()
    {
        $clientId = "test_client_id";
        $clientSecret = $this->getCryptedWord("test_client_secret");
        $parameters = Parameters::getInstance('oauth2', 'clients');
        $parameters->query()
            ->match(['term' => ['_scopes' => 'unit']]);
        $elasticClient = $this->createClientMock(['search']);
        $this->mockSearchResult($elasticClient, $this->getClientStruct($clientId, $clientSecret, 'AaBbCcDdEeFf'))
            ->with($parameters->build());
        $clients = new Clients($elasticClient, 'oauth2', 'clients');
        
        $listOfClients = $clients->hasScope('unit')
            ->search();
        
        $this->assertEquals(1, count($listOfClients['hits']['hits']));
        $this->assertSource($listOfClients['hits']['hits'][0]);
    }
    
    /**
     * 
     * @param string $clientId
     * @param string $clientSecret
     * @param string $userId
     * @return array
     */
    private function getClientStruct($clientId, $clientSecret, $userId)
    {
        return [
            '_index' => 'oauth2',
            '_type' => 'clients',
            '_id' => $clientId,
            'found' => true,
            '_source' => [
                'client_secret' => $clientSecret,
                'redirect_uri' => 'http://localhost/unit-test',
                'grant_types' => 'client_credentials',
                '_scopes' => [
                    'unit',
                    'test'
                ],
                '_user_id' => $userId,
                'created_at' => '2017-03-14T17:37:41Z+0300',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getSourceKeys()
    {
        return [
            'client_secret',
            'redirect_uri',
            'grant_types',
            '_scopes',
            '_user_id',
            'created_at',
        ];
    }

}

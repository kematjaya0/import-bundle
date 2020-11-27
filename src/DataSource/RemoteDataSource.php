<?php

namespace Kematjaya\ImportBundle\DataSource;

use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RemoteDataSource extends AbstractDataSource
{
    /**
     *
     * @var string
     */
    private $url;
    
    /**
     *
     * @var HttpClientInterface 
     */
    private $client;
    
    /**
     *
     * @var string
     */
    private $method = 'GET';
    
    public function __construct(string $url, HttpClientInterface $client = null) 
    {
        $this->url = $url;
        $this->client = $client ? $client : new CurlHttpClient();
    }

    function getMethod(): string 
    {
        return $this->method;
    }

    function setMethod(string $method): self 
    {
        $this->method = $method;
        
        return $this;
    }

    public function execute(): array 
    {
        $resultset = [];
        try{
            $response = $this->client->request(
                $this->getMethod(),
                $this->url
            );
            
            return json_decode($response->getContent(), true);
        } catch (Exception $ex) {
            throw $ex;
        }
            
        return $resultset;
    }

    function getClient(): HttpClientInterface 
    {
        return $this->client;
    }

    function setClient(HttpClientInterface $client): self 
    {
        $this->client = $client;
        
        return $this;
    }

}

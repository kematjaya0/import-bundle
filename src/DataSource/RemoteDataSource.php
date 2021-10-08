<?php

/**
 * This file is part of Kematjaya\ImportBundle
 */

namespace Kematjaya\ImportBundle\DataSource;

use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;

/**
 * Processing from URL (json) to array data
 * 
 * @category Kematjaya\ImportBundle
 * @package  Kematjaya\ImportBundle\Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
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

    /**
     * Get request method
     * 
     * @return string
     */
    public function getMethod(): string 
    {
        return $this->method;
    }

    /**
     * Set request method
     * 
     * @param  string $method
     * @return self
     */
    public function setMethod(string $method): self 
    {
        $this->method = $method;
        
        return $this;
    }
    
    /**
     * Execution from remote source to array
     * 
     * @return array
     * @throws Exception
     */
    public function execute(array $options = []): array 
    {
        try{
            $response = $this->client->request(
                $this->getMethod(),
                $this->url,
                $options
            );
            
            $rs = json_decode($response->getContent(), true);
            
            return is_array($rs) ? $rs : [];
        } catch (Exception $ex) {
            throw $ex;
        }
        
        return [];
    }

    /**
     * Get HTTP Client object
     * 
     * @return HttpClientInterface
     */
    public function getClient(): HttpClientInterface 
    {
        return $this->client;
    }

    /**
     * Set HTTP Client object
     * 
     * @param  HttpClientInterface $client
     * @return self
     */
    public function setClient(HttpClientInterface $client): self 
    {
        $this->client = $client;
        
        return $this;
    }
    
    function getUrl(): string 
    {
        return $this->url;
    }

}

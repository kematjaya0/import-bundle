<?php

namespace Kematjaya\ImportBundle\Tests\Model;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PostTest 
{
    /**
     *
     * @var int
     */
    private $userId;
    
    /**
     *
     * @var int
     */
    private $id;
    
    /**
     *
     * @var string
     */
    private $title;
    
    /**
     *
     * @var string
     */
    private $body;
    
    function getUserId(): int 
    {
        return $this->userId;
    }

    function getId(): int 
    {
        return $this->id;
    }

    function getTitle(): string 
    {
        return $this->title;
    }

    function getBody(): string 
    {
        return $this->body;
    }

    function setUserId(int $userId): self 
    {
        $this->userId = $userId;
        
        return $this;
    }

    function setId(int $id): self 
    {
        $this->id = $id;
        
        return $this;
    }

    function setTitle(string $title): self 
    {
        $this->title = $title;
        
        return $this;
    }

    function setBody(string $body): self 
    {
        $this->body = $body;
        
        return $this;
    }


}

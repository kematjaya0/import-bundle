<?php

namespace Kematjaya\ImportBundle\Exception;

use Exception;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class EmptyException extends Exception
{
    public function __construct(string $message = "empty data", int $code = -1, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

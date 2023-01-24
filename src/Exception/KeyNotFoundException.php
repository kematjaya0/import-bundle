<?php

namespace Kematjaya\ImportBundle\Exception;

use Exception;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class KeyNotFoundException extends Exception
{
    public function __construct(string $key, int $code = -1, \Throwable $previous = null)
    {
        $message = sprintf("cannot find key : %s", $key);
        parent::__construct($message, $code, $previous);
    }
}

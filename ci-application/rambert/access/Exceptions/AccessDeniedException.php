<?php
/**
 * Access denied exception
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Access\Exceptions;

use CodeIgniter\Exceptions\HTTPExceptionInterface;
use CodeIgniter\Exceptions\DebugTraceableTrait;
use CodeIgniter\Exceptions\ExceptionInterface;
use RuntimeException;

class AccessDeniedException extends RuntimeException implements
    ExceptionInterface, HTTPExceptionInterface
{
    use DebugTraceableTrait;

    /**
     * HTTP status code
     */
    protected $code = 403;

    public static function forPageAccessDenied(?string $message = null)
    {
        return new static($message ?? lang('access_lang.msg_error_access_denied'));
    }
}
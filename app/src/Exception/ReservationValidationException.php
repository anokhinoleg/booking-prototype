<?php

declare(strict_types = 1);

namespace App\Exception;

use Symfony\Component\Validator\Exception\RuntimeException;
use Throwable;

final class ReservationValidationException extends RuntimeException
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

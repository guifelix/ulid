<?php

/*
 * This file is part of the ULID package.
 *
 * (c) Guilherme Felix da Silva Maciel <12631274+guifelix@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Guifelix\Ulid\Exception;

/**
 * Thrown to indicate that the parsed ULID string length is invalid.
 */
class InvalidUlidLengthException extends \LengthException
{
    public function __construct(
        string $message = "",
        int $valid = 0,
        int $current = 0,
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        parent::__construct(
            $message ?: "Invalid ULID string length. Valid: $valid; Current: $current",
            $code,
            $previous
        );
    }
}

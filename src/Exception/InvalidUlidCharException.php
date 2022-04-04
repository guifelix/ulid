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
 * Thrown to indicate that the parsed ULID character is invalid.
 */
class InvalidUlidCharException extends \DomainException
{
    public function __construct(
        string $message = "",
        string $valid = "",
        string $invalid = "",
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        parent::__construct(
            $message ?: "Invalid ULID character. Valid (case insensitive): $valid; Invalid: $invalid",
            $code,
            $previous
        );
    }
}

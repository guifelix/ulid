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
 * Thrown to indicate that the parsed ULID timestamp is invalid.
 */
class InvalidUlidTimestampException extends \RangeException
{
    public function __construct(
        string $message = "",
        int $valid_min = null,
        int $valid_max = null,
        string|int|float $invalid = null,
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        $final_message = "Invalid ULID character.";

        if (! is_null($valid_min) && ! is_null($valid_max)) {
            $final_message .= " Valid: $valid_min to $valid_max.";
        }

        if ($invalid) {
            $final_message .= " Invalid: $invalid";
        }

        parent::__construct(
            $message ?: $final_message,
            $code,
            $previous
        );
    }
}

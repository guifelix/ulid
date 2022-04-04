<?php

/*
 * This file is part of the ULID package.
 *
 * (c) Guilherme Felix da Silva Maciel <12631274+guifelix@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Guifelix\Ulid;

use Guifelix\Ulid\Exception\InvalidUlidException;
use Guifelix\Ulid\Exception\InvalidUlidCharException;
use Guifelix\Ulid\Exception\InvalidUlidLengthException;
use Guifelix\Ulid\Exception\InvalidUlidTimestampException;

class Ulid
{
    public const CROCKFORD_CHARS = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    public const CROCKFORD_LENGTH = 32;

    public const TIME_MAX = 281474976710655;
    public const TIME_LENGTH = 10;

    public const RANDOM_LENGTH = 16;

    private static int $lastGenTime = 0;

    private static array $lastRandChars = [];

    private function __construct(
        private string $time,
        private string $randomness,
        private bool $lowercase = false
    ) {
        $this->time = $time;
        $this->randomness = $randomness;
        $this->lowercase = $lowercase;
    }

    public static function fromString(string $ulid, bool $lowercase = false): self
    {
        static::validate($ulid);
        return new static(
            time: substr($ulid, 0, static::TIME_LENGTH),
            randomness: substr($ulid, static::TIME_LENGTH, static::RANDOM_LENGTH),
            lowercase: $lowercase
        );
    }

    /**
     * Create a ULID using the given timestamp.
     *
     * @param int $milliseconds Number of milliseconds since the UNIX epoch for which to generate this ULID.
     * @param bool $lowercase True to output lowercase ULIDs.
     *
     * @return Ulid Returns a ULID object for the given microsecond time.
     */
    public static function fromTimestamp(int $milliseconds, bool $lowercase = false): self
    {
        static::validateTimestamp($milliseconds);
        $duplicateTime = $milliseconds === static::$lastGenTime;

        static::$lastGenTime = $milliseconds;

        $timeChars = '';
        $randChars = '';

        $encodingChars = static::CROCKFORD_CHARS;

        for ($i = static::TIME_LENGTH - 1; $i >= 0; $i--) {
            $mod = $milliseconds % static::CROCKFORD_LENGTH;
            $timeChars = $encodingChars[$mod] . $timeChars;
            $milliseconds = ($milliseconds - $mod) / static::CROCKFORD_LENGTH;
        }

        if (! $duplicateTime) {
            for ($i = 0; $i < static::RANDOM_LENGTH; $i++) {
                static::$lastRandChars[$i] = random_int(0, self::CROCKFORD_LENGTH - 1);
            }
        } else {
            // If the timestamp hasn't changed since last push,
            // use the same random number, except incremented by 1.
            for ($i = static::RANDOM_LENGTH - 1; $i >= 0 && static::$lastRandChars[$i] === self::CROCKFORD_LENGTH - 1; $i--) {
                static::$lastRandChars[$i] = 0;
            }

            static::$lastRandChars[$i]++;
        }

        for ($i = 0; $i < static::RANDOM_LENGTH; $i++) {
            $randChars .= $encodingChars[static::$lastRandChars[$i]];
        }

        return new static($timeChars, $randChars, $lowercase);
    }

    public static function generate(bool $lowercase = false): self
    {
        return static::fromTimestamp(
            (int)(round(microtime(true) * 1000)),
            $lowercase
        );
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getRandomness(): string
    {
        return $this->randomness;
    }

    public function isLowercase(): bool
    {
        return $this->lowercase;
    }

    public function toTimestamp(): int
    {
        return static::decodeTime($this->time);
    }

    public function __toString(): string
    {
        return ($value = $this->time . $this->randomness) && $this->lowercase ? strtolower($value) : strtoupper($value);
    }

    private static function decodeTime(string $time): int
    {
        try {
            $timeChars = str_split(strrev($time));
            $carry = 0;

            foreach ($timeChars as $index => $char) {
                $encodingIndex = strripos(static::CROCKFORD_CHARS, $char);
                $carry += ($encodingIndex * pow(static::CROCKFORD_LENGTH, $index));
            }

            return $carry;
        } catch (\Throwable $th) {
            throw new InvalidUlidException("Error decoding time");
        }
    }

    public static function validate(string $ulid): bool
    {
        static::validateLength($ulid);
        static::validateChars($ulid);
        static::validateTime($ulid);

        return true;
    }

    private static function validateLength(string $ulid): bool
    {
        if (strlen($ulid) !== static::TIME_LENGTH + static::RANDOM_LENGTH) {
            throw new InvalidUlidLengthException(
                valid:static::TIME_LENGTH + static::RANDOM_LENGTH,
                current: strlen($ulid)
            );
        }

        return true;
    }

    private static function validateChars(string $ulid): bool
    {
        $ulid = strtoupper($ulid);

        if (! preg_match(sprintf('!^[%s]{%d}$!', static::CROCKFORD_CHARS, static::TIME_LENGTH + static::RANDOM_LENGTH), $ulid)) {
            throw new InvalidUlidCharException(valid: static::CROCKFORD_CHARS, invalid: $ulid);
        }

        return true;
    }

    private static function validateTime(string $ulid): bool
    {
        $timestamp = static::decodeTime(substr($ulid, 0, static::TIME_LENGTH));
        if (!is_int($timestamp) || $timestamp < 0 || $timestamp > static::TIME_MAX) {
            throw new InvalidUlidTimestampException(
                valid_min: 0,
                valid_max:static::TIME_MAX,
                invalid: $timestamp
            );
        }

        return true;
    }

    private static function validateTimestamp($timestamp): bool
    {
        if (
            !is_int($timestamp) ||
            (is_int($timestamp) && ($timestamp < 0 || $timestamp > static::TIME_MAX))
        ) {
            throw new InvalidUlidTimestampException(
                valid_min: 0,
                valid_max:static::TIME_MAX,
                invalid: $timestamp
            );
        }

        return true;
    }
}

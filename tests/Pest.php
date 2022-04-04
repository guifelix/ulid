<?php

/*
 * This file is part of the ULID package.
 *
 * (c) Guilherme Felix da Silva Maciel <12631274+guifelix@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Guifelix\Ulid\Ulid;
use Guifelix\Ulid\Exception\InvalidUlidCharException;
use Guifelix\Ulid\Exception\InvalidUlidLengthException;
use Guifelix\Ulid\Exception\InvalidUlidTimestampException;

it('generates uppercase ULID by default', fn () =>
    expect((string) $ulid)->toMatch('/[0-9][A-Z]/')
    ->and($ulid->isLowercase())->toBeFalse()
)->with(fn () => Ulid::generate());

it('generates lowercase ULID when set', fn ($ulid) =>
    expect((string) $ulid)->toMatch('/[0-9][a-z]/')
    ->and($ulid->isLowercase())->toBeTrue()
)->with(fn () => Ulid::generate(true));

it('generates 26 character long ULID', fn ($ulid) =>
    expect((string)$ulid)->toHaveLength(26)
)->with(fn () => Ulid::generate());

it('tests Randomness by generating multiple ULIDs', function(){
    $a = Ulid::generate();
    $b = Ulid::generate();

    expect($a->toTimestamp())->toBe($b->toTimestamp());

    if (substr($a, -1, 1) == 'Z') {
        expect(substr($a, 0, -2))->toBe(substr($b, 0, -2));
    }

    if (substr($a, -1, 1) != 'Z') {
        expect(substr($a, 0, -1))->toBe(substr($b, 0, -1));
    }

    expect($a->getRandomness())->not->toBe($b->getRandomness());
});

it('generates lexicographically sortable ULIDs', function(){
    $a = Ulid::generate();
    microtime();
    $b = Ulid::generate();

    $ulids = [(string) $b, (string) $a];
    usort($ulids, 'strcmp');

    expect([(string) $a, (string) $b])->toBe($ulids);
});

it('generates from uppercase string')
    ->expect('01F3HFE5ANK3KSG1BKVE66AEYM')->toEqual((string) Ulid::fromString('01F3HFE5ANK3KSG1BKVE66AEYM'))
    ->and('01F3HFE5ANK3KSG1BKVE66AEYM')->toEqual((string) Ulid::fromString('01F3HFE5ANK3KSG1BKVE66AEYM', false))
    ->and('01f3hfe5ank3ksg1bkve66aeym')->toEqual((string) Ulid::fromString('01F3HFE5ANK3KSG1BKVE66AEYM', true));


it('generates from lowercase string')
    ->expect('01F3HFE5ANK3KSG1BKVE66AEYM')->toEqual((string) Ulid::fromString('01f3hfe5ank3ksg1bkve66aeym'))
    ->and('01F3HFE5ANK3KSG1BKVE66AEYM')->toEqual((string) Ulid::fromString('01f3hfe5ank3ksg1bkve66aeym', false))
    ->and('01f3hfe5ank3ksg1bkve66aeym')->toEqual((string) Ulid::fromString('01f3hfe5ank3ksg1bkve66aeym', true));

it('it throws exception on invalid character and valid length', fn () =>
    Ulid::fromString('not-valid-ulid-with-length')
)->throws(InvalidUlidCharException::class);

it('it throws exception on invalid character and invalid length', fn () =>
    Ulid::fromString("01F3HFE5ANK3KSG1BKVE66AEYM\n")
)->throws(InvalidUlidLengthException::class);

it('it throws exception on valid character and invalid length', fn () =>
    Ulid::fromString('000')
)->throws(InvalidUlidLengthException::class);


dataset('invalid crockford character', [
    'i' => ['0001eh8yaep8cxp4amwchhdbhi', false],
    'l' => ['0001eh8yaep8cxp4amwchhdbhl', false],
    'o' => ['0001eh8yaep8cxp4amwchhdbho', false],
    'u' => ['0001eh8yaep8cxp4amwchhdbhu', false],
    'I' => ['0001EH8YAEP8CXP4AMWCHHDBHI', true],
    'L' => ['0001EH8YAEP8CXP4AMWCHHDBHL', true],
    'O' => ['0001EH8YAEP8CXP4AMWCHHDBHO', true],
    'U' => ['0001EH8YAEP8CXP4AMWCHHDBHU', true],
]);

it('it throws exception on invalid ULID char', fn ($ulid, $case) =>
    Ulid::fromString($ulid, $case)
)->throws(InvalidUlidCharException::class)
    ->with('invalid crockford character');


it('converts to timestamp')
    ->expect(1561622862)
    ->toBe(Ulid::fromString('0001EH8YAEP8CXP4AMWCHHDBHJ')->toTimestamp())
    ->and(1561622862)
    ->toBe(Ulid::fromString('0001eh8yaep8cxp4amwchhdbhj', true)->toTimestamp());

it('generates from timestamp', function () {
    $milliseconds = 1593048767015;
    $ulid = Ulid::fromTimestamp($milliseconds);
    expect('01EBMHP6H7')->toBe(substr((string) $ulid, 0, 10))
    ->and('01EBMHP6H7')->toBe($ulid->getTime())
    ->and($milliseconds)->toBe($ulid->toTimestamp());
});

it('tests Randomness by generating multiple ULIDs from timestamp', function () {
    $milliseconds = 1593048767015;
    $a = Ulid::fromTimestamp($milliseconds);
    $b = Ulid::fromTimestamp($milliseconds);

    expect($a->getTime())->toBe($b->getTime());
    if (substr($a, -1, 1) === 'Z') {
        expect(substr($a, 0, -2))->toBe(substr($b, 0, -2));
    }

    if (substr($a, -1, 1) !== 'Z') {
        expect(substr($a, 0, -1))->toBe(substr($b, 0, -1));
    }
    expect($a->getRandomness())->not->toBe($b->getRandomness());
});

it('throws exception for invalid timestamp on ULID fromString', function () {
    Ulid::fromString('8ZZZZZZZZZP8CXP4AMWCHHDBHJ');
})->throws(InvalidUlidTimestampException::class);

it('throws exception for invalid timestamp fromTimestamp', function () {
    Ulid::fromTimestamp(1000000000000000);
})->throws(InvalidUlidTimestampException::class);

<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 package-url
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace PackageUrl\Tests;

use Generator;
use PackageUrl\PackageUrlParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PackageUrl\PackageUrlParser
 *
 * @psalm-import-type TDataSet from \PackageUrl\Tests\_data\TestSuiteData
 *
 * @author jkowalleck
 */
class PackageUrlParserTest extends TestCase
{
    /** @var PackageUrlParser */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new PackageUrlParser();
    }

    /**
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsToLowercase
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     */
    public function testNormalizeScheme(?string $input, ?string $expectedOutput): void
    {
        $normalized = $this->sut->normalizeScheme($input);
        self::assertSame($expectedOutput, $normalized);
    }

    /**
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsToLowercase
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     */
    public function testNormalizeType(?string $input, ?string $expectedOutput): void
    {
        $normalized = $this->sut->normalizeType($input);
        self::assertSame($expectedOutput, $normalized);
    }

    /**
     * @dataProvider dpNormalizeNamespace
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::normalizeNamespaceSpecials()
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     *
     * @psalm-param non-empty-string|null $type
     */
    public function testNormalizeNamespace(?string $input, ?string $expectedOutput, string $type = ''): void
    {
        $normalized = $this->sut->normalizeNamespace($input, $type);
        self::assertSame($expectedOutput, $normalized);
    }

    /**
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::normalizeNameSpecials
     * @dataProvider dpStringsToDecoded
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     *
     * @psalm-param non-empty-string|null $type
     */
    public function testNormalizeName(?string $input, ?string $expectedOutput, string $type = ''): void
    {
        $normalized = $this->sut->normalizeName($input, $type);
        self::assertSame($expectedOutput, $normalized);
    }

    /**
     * @dataProvider dpStringsToDecoded
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     */
    public function testNormalizeVersion(?string $input, ?string $expectedOutput): void
    {
        $normalized = $this->sut->normalizeVersion($input);
        self::assertSame($expectedOutput, $normalized);
    }

    /**
     * @dataProvider dpNormalizeQualifiers
     */
    public function testNormalizeQualifiers(?string $input, ?array $expectedQualifiers): void
    {
        $normalized = $this->sut->normalizeQualifiers($input);
        self::assertSame($expectedQualifiers, $normalized);
    }

    /**
     * @dataProvider dpNormalizeSubpath
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     */
    public function testNormalizeSubpath(?string $input, ?string $expectedOutcome): void
    {
        $decoded = $this->sut->normalizeSubpath($input);
        self::assertSame($expectedOutcome, $decoded);
    }

    /**
     * @dataProvider \PackageUrl\Tests\_data\TestSuiteData::data
     * @psalm-param TDataSet $data
     */
    public function testParseAndNormalize(array $data): void
    {
        $expected = [
            'type' => $data['type'],
            'namespace' => $data['namespace'],
            'name' => $data['name'],
            'version' => $data['version'],
            'qualifiers' => $data['qualifiers'],
            'subpath' => $data['subpath'],
        ];

        $parsed = $this->sut->parse($data['purl']);
        [$normalizedQualifiers, $normalizedChecksums] = $this->sut->normalizeQualifiers($parsed['qualifiers']);

        $normalized = [
            'type' => $this->sut->normalizeType($parsed['type']),
            'namespace' => $this->sut->normalizeNamespace($parsed['namespace'], $parsed['type']),
            'name' => $this->sut->normalizeName($parsed['name'], $parsed['type']),
            'version' => $this->sut->normalizeVersion($parsed['version']),
            'qualifiers' => $normalizedQualifiers,
            'subpath' => $this->sut->normalizeSubpath($parsed['subpath']),
        ];

        if ($data['is_invalid']) {
            self::assertNotSame($expected, $normalized);
        } else {
            self::assertSame($expected, $normalized);
        }
    }

    /**
     * @psalm-return Generator<non-empty-string, array{string, string}>
     */
    public static function dpNormalizeNamespace(): Generator
    {
        yield 'empty/empty' => ['/', null];
        yield 'some Namespace' => ['some/Namespace', 'some/Namespace'];
        yield 'some/empty Namespace' => ['some//Namespace', 'some/Namespace'];
        yield 'encoded Namespace' => ['some/Name%20space', 'some/Name space'];
        yield 'complex Namespace' => ['/yet/another//Name%20space/', 'yet/another/Name space'];
    }

    /**
     * @psalm-return Generator<non-empty-string, array{string, array<string, string>}>
     */
    public static function dpStringsToDecoded(): Generator
    {
        yield 'some string' => ['someString', 'someString'];
        yield 'encoded string' => ['some%20%22encoded%22%20string', 'some "encoded" string'];
    }

    public static function dpNormalizeQualifiers(): Generator
    {
        yield 'null' => [null, [null, null]];
        yield 'empty' => ['', [null, null]];
        yield 'some empty value' => ['k=', [null, null]];
        yield 'some none value' => ['k', [null, null]];
        yield 'some kv' => ['k=v', [['k' => 'v'], null]];
        yield 'some KV' => ['K=V', [['k' => 'V'], null]];
        yield 'some encoded value' => ['k=a%20value', [['k' => 'a value'], null]];
        yield 'multiple KVs' => ['k1=v1&k2=v2&k3=&k4', [['k1' => 'v1', 'k2' => 'v2'], null]];
        yield 'checksums' => ['checksum=foo:bar', [null, ['foo:bar']]];
    }

    /**
     * @psalm-return Generator<non-empty-string, array{string, string}>
     */
    public static function dpNormalizeSubpath(): Generator
    {
        yield 'dot' => ['.', null];
        yield 'dot dot' => ['..', null];
        yield 'path' => ['path', 'path'];
        yield 'some/path' => ['some/path', 'some/path'];
        yield 'surrounding slashes' => ['/path//', 'path'];
        yield 'inner slashes' => ['some//path/', 'some/path'];
        yield 'encoded' => ['some%20path/', 'some path'];
        yield 'complex' => ['//.foo/./bar./..//Baz%20ooF/', '.foo/bar./Baz ooF'];
        yield 'dot complex' => ['/./..//./', null];
    }
}

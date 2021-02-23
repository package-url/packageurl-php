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

use DomainException;
use Generator;
use PackageUrl\PackageUrlBuilder;
use PackageUrl\Tests\_data\TestSuiteData;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PackageUrl\PackageUrlBuilder
 *
 * @psalm-import-type TDataSet from \PackageUrl\Tests\_data\TestSuiteData
 *
 * @author jkowalleck
 */
class PackageUrlBuilderTest extends TestCase
{
    /** @var PackageUrlBuilder */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new PackageUrlBuilder();
    }

    /**
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsToLowercase
     */
    public function testNormalizeType(string $input, string $expectedOutput): void
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
     * @dataProvider dpStringsToEncoded
     *
     * @psalm-param non-empty-string|null $type
     */
    public function testNormalizeName(?string $input, ?string $expectedOutput, string $type = ''): void
    {
        $normalized = $this->sut->normalizeName($input, $type);
        self::assertSame($expectedOutput, $normalized);
    }

    public function testNormalizeNameSlash(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/name .*empty/i');
        $this->sut->normalizeName('///', '');
    }

    /**
     * @dataProvider dpStringsToEncoded
     * @dataProvider \PackageUrl\Tests\_data\MiscProvider::stringsEmptyAndNull
     */
    public function testNormalizeVersion(?string $input, ?string $expectedOutput): void
    {
        $normalized = $this->sut->normalizeVersion($input);
        self::assertSame($expectedOutput, $normalized);
    }

    /**
     * @dataProvider dpNormalizeQualifiers
     * @dataProvider dpNormalizeQualifiersChecksum
     */
    public function testNormalizeQualifiers(?array $input, ?string $expectedOutcome): void
    {
        $normalized = $this->sut->normalizeQualifiers($input);
        self::assertSame($expectedOutcome, $normalized);
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

    public function testBuildEmptyType(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/type .*empty/i');
        $this->sut->build(
            '',
            null,
            'name',
            null,
            null,
            null
        );
    }

    public function testBuildEmptyName(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/name .*empty/i');
        $this->sut->build(
            'type',
            null,
            '',
            null,
            null,
            null
        );
    }

    /**
     * @dataProvider dpValidTestData
     * @psalm-param TDataSet $data
     */
    public function testBuild(array $data): void
    {
        $expected = $data['canonical_purl'];
        $built = $this->sut->build(
            $data['type'],
            $data['namespace'],
            $data['name'],
            $data['version'],
            $data['qualifiers'],
            $data['subpath'],
        );

        self::assertEquals($expected, $built);
    }

    /**
     * @psalm-return Generator<non-empty-string, array{TDataSet}>
     */
    public static function dpValidTestData(): Generator
    {
        foreach (TestSuiteData::data() as $label => [$data]) {
            if (true === $data['is_invalid']) {
                continue;
            }
            yield $label => [$data];
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
        yield 'encoded Namespace' => ['some/Name space', 'some/Name%20space'];
        yield 'complex Namespace' => ['/yet/another//Name space/', 'yet/another/Name%20space'];
    }

    /**
     * @psalm-return Generator<non-empty-string, array{string, array<string, string>}>
     */
    public static function dpStringsToEncoded(): Generator
    {
        yield 'some:string' => ['some:String', 'some:String'];
        yield 'some/string' => ['some/String', 'some/String'];
        yield 'encoded string' => ['some "encoded" string', 'some%20%22encoded%22%20string'];
    }

    /**
     * @psalm-return Generator<non-empty-string, array{null|array<string, null|string>, null|string}>
     */
    public static function dpNormalizeQualifiers(): Generator
    {
        yield 'null' => [null, null];
        yield 'empty' => [[], null];
        yield 'some empty value' => [['k' => ''], null];
        yield 'some none value' => [['k' => null], null];
        yield 'some kv' => [['k' => 'v'], 'k=v'];
        yield 'some kn' => [['k' => 23], 'k=23'];
        yield 'some KV' => [['K' => 'V'], 'k=V'];
        yield 'empty key' => [['' => 'foo'], null];
        yield 'some encoded value' => [['k' => 'a value'], 'k=a%20value'];
        yield 'multiple KVs' => [['k1' => 'v1', 'k2' => 'v2', 'k3' => '', 'k4' => null], 'k1=v1&k2=v2'];
    }

    public static function dpNormalizeQualifiersChecksum(): Generator
    {
        yield 'checksum null' => [['checksum' => null], null];
        yield 'checksum empty string' => [['checksum' => ''], null];
        yield 'checksum empty array' => [['checksum' => []], null];
        yield 'checksum string' => [['checksum' => 'md5:1234'], 'checksum=md5:1234'];
        yield 'checksum array' => [['checksum' => ['md5:1234', 'sha1:456']], 'checksum=md5:1234,sha1:456'];
        yield 'checksum bool' => [['checksum' => false], null];
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
        yield 'encoded' => ['some path/', 'some%20path'];
        yield 'complex' => ['//.foo/./bar./..//Baz ooF/', '.foo/bar./Baz%20ooF'];
        yield 'dot complex' => ['/./..//./', null];
    }
}

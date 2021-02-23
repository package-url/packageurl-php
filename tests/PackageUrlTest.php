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
use PackageUrl\PackageUrl;
use PackageUrl\PackageUrlBuilder;
use PackageUrl\PackageUrlParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PackageUrl\PackageUrl
 */
class PackageUrlTest extends TestCase
{
    /** @var PackageUrl */
    private $sut;

    public function setUp(): void
    {
        $randomString = bin2hex(random_bytes(255));
        $this->sut = (new PackageUrl($randomString, $randomString))
            ->setNamespace($randomString)
            ->setType($randomString)
            ->setQualifiers([$randomString => $randomString])
            ->setChecksums(['md5:'.$randomString])
            ->setSubpath($randomString);
    }

    // region type setter&getter

    public function testTypeSetterGetter(): void
    {
        $value = bin2hex(random_bytes(random_int(3, 32)));
        $this->sut->setType($value);
        self::assertSame($value, $this->sut->getType());
    }

    public function testTypeSetterInvalid(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/empty/i');
        $this->sut->setType('');
    }

    // endregion type setter&getter

    // region name setter&getter

    public function testNameSetterGetter(): void
    {
        $value = bin2hex(random_bytes(random_int(3, 32)));
        $this->sut->setName($value);
        self::assertSame($value, $this->sut->getName());
    }

    public function testNameSetterInvalid(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/empty/i');
        $this->sut->setName('');
    }

    // endregion name setter&getter

    // region namespace setter&getter

    public function testNamespaceSetterGetter(): void
    {
        $value = bin2hex(random_bytes(random_int(3, 32)));
        $this->sut->setNamespace($value);
        self::assertSame($value, $this->sut->getNamespace());
    }

    public function testNamespaceSetterEmpty(): void
    {
        $this->sut->setNamespace('');
        self::assertNull($this->sut->getNamespace());
    }

    public function testNamespaceSetterNull(): void
    {
        $this->sut->setNamespace(null);
        self::assertNull($this->sut->getNamespace());
    }

    // endregion namespace setter&getter

    // region version setter&getter

    public function testVersionSetterGetter(): void
    {
        $value = bin2hex(random_bytes(random_int(3, 32)));
        $this->sut->setVersion($value);
        self::assertSame($value, $this->sut->getVersion());
    }

    public function testVersionSetterEmpty(): void
    {
        $this->sut->setVersion('');
        self::assertNull($this->sut->getVersion());
    }

    public function testVersionSetterNull(): void
    {
        $this->sut->setVersion(null);
        self::assertNull($this->sut->getVersion());
    }

    // endregion version setter&getter

    // region Qualifiers setter&getter

    public function testQualifiersSetterGetter(): void
    {
        $qualifiers = ['v'.bin2hex(random_bytes(32)) => 'k'.bin2hex(random_bytes(32))];
        $this->sut->setQualifiers($qualifiers);
        self::assertEquals($qualifiers, $this->sut->getQualifiers());
    }

    public function testQualifiersSetterWithChecksums(): void
    {
        $qualifiers = ['checksum' => 'md5:'.bin2hex(random_bytes(32))];
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/checksum/i');
        $this->sut->setQualifiers($qualifiers);
    }

    // endregion Qualifiers setter&getter

    // region Qualifiers setter&getter

    public function testChecksumsSetterGetter(): void
    {
        $checksums = ['md5:'.bin2hex(random_bytes(32))];
        $this->sut->setChecksums($checksums);
        self::assertEquals($checksums, $this->sut->getChecksums());
    }

    // endregion Qualifiers setter&getter

    // region subpath setter&getter

    public function testSubpathSetterGetter(): void
    {
        $value = bin2hex(random_bytes(random_int(3, 32)));
        $this->sut->setSubpath($value);
        self::assertSame($value, $this->sut->getSubpath());
    }

    public function testSubpathSetterEmpty(): void
    {
        $this->sut->setSubpath('');
        self::assertNull($this->sut->getSubpath());
    }

    public function testSubpathSetterNull(): void
    {
        $this->sut->setSubpath(null);
        self::assertNull($this->sut->getSubpath());
    }

    // endregion subpath setter&getter

    // region fromString

    public function testFromStringEmpty(): void
    {
        $parser = $this->createMock(PackageUrlParser::class);
        $purl = $this->sut::fromString('', $parser);
        self::assertNull($purl);
    }

    public function testFromStringInvalidScheme(): void
    {
        $parser = $this->createMock(PackageUrlParser::class);
        $parser->expects(self::once())->method('parse')
            ->willReturn(self::parsedToNulls());
        $parser->expects(self::once())->method('normalizeScheme')
            ->willReturn(null);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/mismatching scheme/i');
        $this->sut::fromString('something', $parser);
    }

    public function testFromStringEmptyType(): void
    {
        $parser = $this->createMock(PackageUrlParser::class);
        $parser->expects(self::once())->method('parse')
            ->willReturn(self::parsedToNulls());
        $parser->method('normalizeScheme')
            ->willReturn($this->sut::SCHEME);
        $parser->expects(self::once())->method('normalizeType')
            ->willReturn(null);
        $parser->method('normalizeName')
            ->willReturn('something');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/type .*empty/i');
        $this->sut::fromString('something', $parser);
    }

    public function testFromStringEmptyName(): void
    {
        $parser = $this->createMock(PackageUrlParser::class);
        $parser->expects(self::once())->method('parse')
            ->willReturn(self::parsedToNulls());
        $parser->method('normalizeScheme')
            ->willReturn($this->sut::SCHEME);
        $parser->method('normalizeType')
            ->willReturn('something');
        $parser->expects(self::once())->method('normalizeName')
            ->willReturn(null);
        $this->expectExceptionMessageMatches('/name .*empty/i');
        $this->sut::fromString('something', $parser);
    }

    public function testFromString(): void
    {
        // arrange
        $purlString = $this->sut::SCHEME.':type/namespace/name@version?qualifiers=true#subpath';
        $purlParsed = [
            'scheme' => uniqid('parsedScheme', true),
            'type' => uniqid('parsedType', true),
            'namespace' => uniqid('parsedNamespace', true),
            'name' => uniqid('parsedName', true),
            'version' => uniqid('parsedVersion', true),
            'qualifiers' => uniqid('parsedQualifiers', true),
            'subpath' => uniqid('parsedSubpath', true),
        ];
        $purlNormalized = [
            'scheme' => $this->sut::SCHEME,
            'type' => uniqid('normalizedType', true),
            'namespace' => uniqid('normalizedNamespace', true),
            'name' => uniqid('normalizedName', true),
            'version' => uniqid('normalizedVersion', true),
            'qualifiers' => [uniqid('normalizedKeyQualifiers', true) => uniqid('normalizedValue', true)],
            'subpath' => uniqid('normalizedSubpath', true),
        ];
        $parser = $this->createMock(PackageUrlParser::class);
        $normalizeWithType = self::logicalOr($purlParsed['type'], $purlNormalized['type']);
        $parser->expects(self::once())->method('parse')->with($purlString)->willReturn($purlParsed);
        $parser->method('normalizeScheme')->with($purlParsed['scheme'])->willReturn($purlNormalized['scheme']);
        $parser->method('normalizeType')->with($purlParsed['type'])->willReturn($purlNormalized['type']);
        $parser->method('normalizeNamespace')->with($purlParsed['namespace'], $normalizeWithType)->willReturn(
            $purlNormalized['namespace']
        );
        $parser->method('normalizeName')->with($purlParsed['name'], $normalizeWithType)->willReturn(
            $purlNormalized['name']
        );
        $parser->method('normalizeVersion')->with($purlParsed['version'])->willReturn($purlNormalized['version']);
        $parser->method('normalizeQualifiers')->with($purlParsed['qualifiers'])->willReturn(
            [$purlNormalized['qualifiers'], null]
        );
        $parser->method('normalizeSubpath')->with($purlParsed['subpath'])->willReturn($purlNormalized['subpath']);
        // act
        $purl = $this->sut::fromString($purlString, $parser);
        // assert
        self::assertInstanceOf(get_class($this->sut), $purl);
        self::assertEquals($purlNormalized['type'], $purl->getType());
        self::assertEquals($purlNormalized['namespace'], $purl->getNamespace());
        self::assertEquals($purlNormalized['name'], $purl->getName());
        self::assertEquals($purlNormalized['version'], $purl->getVersion());
        self::assertEquals($purlNormalized['qualifiers'], $purl->getQualifiers());
        self::assertEquals($purlNormalized['subpath'], $purl->getSubpath());
    }

    private static function parsedToNulls(): array
    {
        return [
            'scheme' => null,
            'type' => null,
            'namespace' => null,
            'name' => null,
            'version' => null,
            'qualifiers' => [null, null],
            'subpath' => null,
        ];
    }

    // endregion fromString

    // region toString

    public function testAsString(): void
    {
        $expected = bin2hex(random_bytes(32));
        $sut = $this->createPartialMock(get_class($this->sut), ['toString']);
        $sut->expects(self::once())->method('toString')->willReturn($expected);
        $toString = (string) $sut;
        self::assertEquals($expected, $toString);
    }

    public function testToString(): void
    {
        $expected = bin2hex(random_bytes(32));
        $builder = $this->createMock(PackageUrlBuilder::class);
        $qualifiers = $this->sut->getQualifiers();
        $qualifiers['checksum'] = $this->sut->getChecksums();
        $builder->expects(self::once())->method('build')
            ->with(
                $this->sut->getType(),
                $this->sut->getName(),
                $this->sut->getNamespace(),
                $this->sut->getVersion(),
                $qualifiers,
                $this->sut->getSubpath()
            )
            ->willReturn($expected);
        $asString = $this->sut->toString($builder);
        self::assertEquals($expected, $asString);
    }

    // endregion toString
}

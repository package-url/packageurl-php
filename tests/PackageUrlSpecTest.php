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

use PackageUrl\PackageUrl;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PackageUrl\PackageUrl
 */
final class PackageUrlSpecTest extends TestCase
{
    private static function getSpecFilePath(): string
    {
        return \dirname(__DIR__).'/spec/tests/spec/specification-test.json';
    }

    //    private static function getTypesSpecDir(): string
    //    {
    //        return dirname(__DIR__) . '/spec/tests/types';
    //    }

    private static function loadSpecFile(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Spec file not found: {$path}");
        }
        $json = file_get_contents($path);
        if (false === $json) {
            throw new \RuntimeException("Unable to read file: {$path}");
        }
        $data = json_decode($json, true);
        if (!\is_array($data)) {
            throw new \RuntimeException("Invalid JSON in: {$path}");
        }

        return $data;
    }

    /**
     * @return array[]
     */
    public static function parseProvider(): array
    {
        $spec = self::loadSpecFile(self::getSpecFilePath());
        $cases = [];
        foreach ($spec['tests'] as $test) {
            if ('parse' === $test['test_type']) {
                $cases[] = [
                    $test['description'],
                    $test['input'],
                    $test['expected_output'] ?? null,
                    $test['expected_failure'] ?? false,
                ];
            }
        }

        return $cases;
    }

    /**
     * @return array[]
     */
    public static function buildProvider(): array
    {
        $spec = self::loadSpecFile(self::getSpecFilePath());
        $cases = [];
        foreach ($spec['tests'] as $test) {
            if ('build' === $test['test_type']) {
                $cases[] = [
                    $test['description'],
                    $test['input'],
                    $test['expected_output'] ?? null,
                    $test['expected_failure'] ?? false,
                ];
            }
        }

        return $cases;
    }

    //    /**
    //     * @return array[]
    //     */
    //    public static function typeCaseProvider(): array
    //    {
    //        $specDir = self::getTypesSpecDir();
    //        $cases = [];
    //        if (!is_dir($specDir)) {
    //            return $cases;
    //        }
    //        foreach (scandir($specDir) as $file) {
    //            if (str_ends_with($file, '-test.json')) {
    //                $data = self::loadSpecFile($specDir . '/' . $file);
    //                foreach ($data['tests'] as $case) {
    //                    $cases[] = [$file, $case['description'], $case];
    //                }
    //            }
    //        }
    //        return $cases;
    //    }

    /**
     * @dataProvider parseProvider
     */
    public function testParse(string $description, string $input, $expectedOutput, bool $expectedFailure): void
    {
        if ($expectedFailure) {
            $this->expectException(\Exception::class);
            PackageUrl::fromString($input);
        } else {
            $purl = PackageUrl::fromString($input);
            $this->assertSame($expectedOutput, $purl->toString(), "Failed: {$description}");
        }
    }

    /**
     * @dataProvider buildProvider
     */
    public function testBuild(string $description, array $input, $expectedOutput, bool $expectedFailure): void
    {
        if ($expectedFailure) {
            $this->expectException(\Exception::class);
        }

        $type = $input['type'] ?? '';
        $name = $input['name'] ?? '';

        $purl = new PackageUrl($type, $name);
        $namespace = $input['namespace'] ?? null;
        $version = $input['version'] ?? null;
        $qualifiers = $input['qualifiers'] ?? null;
        $subpath = $input['subpath'] ?? null;

        $checksums = null;
        if (\is_array($qualifiers) && \array_key_exists(PackageUrl::QUALIFIER_CHECKSUM, $qualifiers)) {
            $checksumValue = $qualifiers[PackageUrl::QUALIFIER_CHECKSUM];
            unset($qualifiers[PackageUrl::QUALIFIER_CHECKSUM]);
            $checksums = \is_array($checksumValue) ? $checksumValue : [$checksumValue];
        }

        $purl
            ->setNamespace($namespace)
            ->setVersion($version)
            ->setQualifiers($qualifiers)
            ->setChecksums($checksums)
            ->setSubpath($subpath);

        $this->assertSame($expectedOutput, $purl->toString(), "Failed: {$description}");
    }

    //    /**
    //     * @dataProvider typeCaseProvider
    //     */
    //    public function test_package_type_case(string $filename, string $description, array $case): void
    //    {
    //        $testType = $case['test_type'];
    //        $expectedFailure = $case['expected_failure'] ?? false;
    //
    //        if ($expectedFailure) {
    //            $this->expectException(\Exception::class);
    //            $this->runTestCase($case, $testType, $description);
    //        } else {
    //            $this->runTestCase($case, $testType, $description);
    //        }
    //    }

    private function runTestCase(array $case, string $testType, string $desc): void
    {
        switch ($testType) {
            case 'parse':
                $purl = PackageUrl::fromString($case['input']);
                $expected = $case['expected_output'];
                $this->assertSame($expected['type'], $purl->getType());
                $this->assertSame($expected['namespace'], $purl->getNamespace());
                $this->assertSame($expected['name'], $purl->getName());
                $this->assertSame($expected['version'], $purl->getVersion());
                if (isset($expected['qualifiers'])) {
                    $this->assertSame($expected['qualifiers'], $purl->getQualifiers());
                } else {
                    $this->assertEmpty($purl->getQualifiers());
                }
                $this->assertSame($expected['subpath'], $purl->getSubpath());
                break;

            case 'roundtrip':
                $purl = PackageUrl::fromString($case['input']);
                $this->assertSame($case['expected_output'], $purl->toString());
                break;

            case 'build':
                $input = $case['input'];
                $purl = new PackageUrl(
                    $input['type'] ?? null,
                    $input['namespace'] ?? null,
                );
                $purl
                    ->setNamespace($input['namespace'] ?? null)
                    ->setVersion($input['version'] ?? null)
                    ->setQualifiers($input['qualifiers'] ?? null)
                    ->setSubpath($input['subpath'] ?? null);

                $this->assertSame($case['expected_output'], $purl->toString());
                break;

            default:
                $this->fail("Unknown test type '{$testType}' in {$desc}");
        }
    }
}

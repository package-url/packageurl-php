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

namespace PackageUrl\Tests\_data;

use Generator;

/**
 * @psalm-type TDataSet = array{
 *      description: string,
 *      purl: string,
 *      canonical_purl: string,
 *      type: string|null,
 *      namespace: string|null,
 *      name: string|null,
 *      version: string|null,
 *      qualifiers: string|null,
 *      subpath: string|null,
 *      is_invalid: bool
 *  }
 */
abstract class TestSuiteData
{
    /**
     * data example
     *  - "description": "valid maven purl",
     *  - "purl": "pkg:maven/org.apache.commons/io@1.3.4",
     *  - "canonical_purl": "pkg:maven/org.apache.commons/io@1.3.4",
     *  - "type": "maven",
     *  - "namespace": "org.apache.commons",
     *  - "name": "io",
     *  - "version": "1.3.4",
     *  - "qualifiers": null,
     *  - "subpath": null,
     *  - "is_invalid": false,
     * .
     *
     * @psalm-return Generator<non-empty-string, array{TDataSet}>
     */
    public static function data(): Generator
    {
        $testSuite = json_decode(file_get_contents(__DIR__.'/../_examples/test-suite-data.json'), true, 521, JSON_THROW_ON_ERROR);
        foreach ($testSuite as $data) {
            yield $data['description'] => [$data];
        }
    }
}

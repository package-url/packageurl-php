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

abstract class MiscProvider
{
    /**
     * @psalm-return Generator<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function stringsToLowercase(): Generator
    {
        yield 'lowercase' => ['something', 'something'];
        yield 'UPPERCASE' => ['SOMETHING', 'something'];
        yield 'mIxeDCase' => ['sOmetHIng', 'something'];
    }

    /**
     * @psalm-return Generator<non-empty-string, array{string|null, string|null}>
     */
    public static function stringsEmptyAndNull(): Generator
    {
        yield 'empty' => ['', null];
        yield 'null' => [null, null];
    }

    /**
     * based on {@link https://github.com/package-url/purl-spec#known-purl-types Known purl types}.
     *
     * @psalm-return Generator<non-empty-string, array{string, string, non-empty-string}>
     */
    public static function normalizeNamespaceSpecials(): Generator
    {
        yield 'bitbucket: lowercase' => ['FoO', 'foo', 'bitbucket'];
        yield 'deb: lowercase' => ['FoO', 'foo', 'deb'];
        yield 'github: lowercase' => ['FoO', 'foo', 'github'];
        yield 'golang: lowercase' => ['FoO', 'foo', 'golang'];
        yield 'hex: lowercase' => ['FoO', 'foo', 'hex'];
        yield 'rpm: lowercase' => ['FoO', 'foo', 'rpm'];
    }

    /**
     * based on {@link https://github.com/package-url/purl-spec#known-purl-types Known purl types}.
     *
     * @psalm-return Generator<non-empty-string, array{string, string, non-empty-string}>
     */
    public static function normalizeNameSpecials(): Generator
    {
        yield 'bitbucket: lowercase' => ['FoO', 'foo', 'bitbucket'];
        yield 'deb: lowercase' => ['FoO', 'foo', 'deb'];
        yield 'github: lowercase' => ['FoO', 'foo', 'github'];
        yield 'golang: lowercase' => ['FoO', 'foo', 'golang'];
        yield 'hex: lowercase' => ['FoO', 'foo', 'hex'];
        yield 'pypi: lowercase' => ['FoO', 'foo', 'pypi'];
        yield 'pypi: underscores' => ['foo_bar', 'foo-bar', 'pypi'];
    }
}

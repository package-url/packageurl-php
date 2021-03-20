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

use PackageUrl\PackageUrl;

require_once __DIR__.'/../vendor/autoload.php';

$purl = (new PackageUrl('maven', 'myartifact'))
    ->setNamespace('mygroup')
    ->setVersion('1.0.0 Final')
    ->setQualifiers(['mykey' => 'my value'])
    ->setChecksums(['md5:46d2ff0ce36bd553a394e8fa1fa846c7'])
    ->setSubpath('my/sub/path');

$purlString = $purl->toString();

// string(117) "pkg:maven/mygroup/myartifact@1.0.0%20Final?checksum=md5:46d2ff0ce36bd553a394e8fa1fa846c7&mykey=my%20value#my/sub/path"
var_dump($purlString);

// string(117) "pkg:maven/mygroup/myartifact@1.0.0%20Final?checksum=md5:46d2ff0ce36bd553a394e8fa1fa846c7&mykey=my%20value#my/sub/path"
var_dump((string) $purl);

$purl2 = PackageUrl::fromString($purlString);

// bool(true)
var_dump($purl == $purl2);

// bool(false)
var_dump($purl === $purl2);

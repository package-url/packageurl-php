[![shield_gh-workflow-test]][link_gh-workflow-test]
[![shield_packagist-version]][link_packagist]
[![shield_license]][license_file]

----

# Package URL (purl) for PHP

A parser and builder based on the [package url spec]
implemented for PHP.

License: MIT

## Install

```shell
composer require package-url/packageurl-php
```

## Usage

```php
<?php

use PackageUrl\PackageUrl;

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
```

## Contributing

Feel free to open pull requests.  
See the [contribution docs][contributing_file] for details.

[package url spec]: https://github.com/package-url/purl-spec

[license_file]: https://github.com/package-url/packageurl-php/blob/main/LICENSE
[contributing_file]: https://github.com/package-url/packageurl-php/blob/main/CONTRIBUTING.md

[shield_gh-workflow-test]: https://img.shields.io/github/actions/workflow/status/package-url/packageurl-php/php.yml?branch=main&?logo=GitHub&logoColor=white "build"
[shield_packagist-version]: https://img.shields.io/packagist/v/package-url/packageurl-php?logo=&logoColor=white "packagist"
[shield_license]: https://img.shields.io/github/license/package-url/packageurl-php "license"
[link_gh-workflow-test]: https://github.com/package-url/packageurl-php/actions?workflow=PHP+CI
[link_packagist]: https://packagist.org/packages/package-url/packageurl-php

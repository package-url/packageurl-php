# Package URL (purl) for PHP

A parser and builder based on the [package url spec]
implemented for PHP.

License: MIT

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

### Run tests

install setup and tools:

```shell
composer dev-setup
```

fix code styles:

```shell
composer cs-fix
```

run tests:

```shell
composer test
```

[package url spec]: https://github.com/package-url/purl-spec

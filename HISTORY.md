# Changes

## unreleased

* Added
  * Constant `PackageUrl::QUALIFIER_REPOSITORY_URL` ([#54] via [#])
  * Constant `PackageUrl::QUALIFIER_DOWNLOAD_URL` ([#54] via [#])
  * Constant `PackageUrl::QUALIFIER_VCS_URL` ([#54] via [#])
  * Constant `PackageUrl::QUALIFIER_FILE_NAME` ([#54] via [#])
  * Constant `PackageUrl::QUALIFIER_CHECKSUM` ([#54] via [#])
* Deprecated
  * Constant `PackageUrl::CHECKSUM_QUALIFIER` -> use `PackageUrl::QUALIFIER_CHECKSUM` instead ([#54] via [#])

[#54]: https://github.com/package-url/packageurl-php/issues/54
[#]

## 1.0.6 - 2023-03-18

Maintenance release.

## 1.0.5 - 2023-03-03

Maintenance release.

## 1.0.4 - 2022-01-04

Maintenance release:

* Docs
  * Upgraded contributing instructions in `CONTRIBUTING.md` & `README.md`.
* Misc
  * Assured php 8.1 compatibility.
  * Normalized composer manifest. 
  * Upgraded (dev-)tools. 

## 1.0.3 - 2021-05-13

Maintenance release:

* Misc
  * Removed `php-cs-fixer` config from dist-release.

## 1.0.2 - 2021-05-13

Maintenance release:

* Misc
  * Applied QA tool `php-cs-fixer` rule `@Symfony:risky`.

## 1.0.1 - 2021-05-11

Maintenance release:

* Docs
  * Upgraded install instructions in the `README.md`.
* Misc
  * Upgraded the internally used QA tools in development processes,  
    Update `php-cs-fixer` to v3.

## 1.0.0 - 2021-04-02

* First implementation

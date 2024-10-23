# Changes

## unreleased

<!-- new unreleased items here -->

* Style
  * Applied latest code style (via [#196])

[#196]: https://github.com/package-url/packageurl-php/pull/196

## 1.1.2 - 2024-02-05

Maintenance release.

## 1.1.1 - 2023-11-27

Maintenance release:

* Misc
 * Assured php 8.3 compatibility (via [#160])

[#160]: https://github.com/package-url/packageurl-php/pull/160

## 1.1.0 - 2023-11-18

* Added
  * Constant `PackageUrl::QUALIFIER_REPOSITORY_URL` ([#54] via [#158])
  * Constant `PackageUrl::QUALIFIER_DOWNLOAD_URL` ([#54] via [#158])
  * Constant `PackageUrl::QUALIFIER_VCS_URL` ([#54] via [#158])
  * Constant `PackageUrl::QUALIFIER_FILE_NAME` ([#54] via [#158])
  * Constant `PackageUrl::QUALIFIER_CHECKSUM` ([#54] via [#158])
* Deprecated
  * Constant `PackageUrl::CHECKSUM_QUALIFIER` -> use `PackageUrl::QUALIFIER_CHECKSUM` instead ([#54] via [#158])

[#54]: https://github.com/package-url/packageurl-php/issues/54
[#158]: https://github.com/package-url/packageurl-php/pull/158

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

# Gatekeeper Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.2.1 - 2020-11-17
### Fix
- psr-4 autoloading standard error (Composer 2 compatibility)
- Error on redirect from missing namespace (Thanks [st-mcd](https://github.com/st-mcd))

## 1.2.0 - 2019-06-03
### Add
- 'duration' (cookie expiration) setting
- A warning on settings screen if values are being overridden in config file

### Fix
- Issue #7 by adding an 'enabled' setting

## 1.1.1 - 2019-03-25
### Fix
- Prepend baseUrl to redirects if present

## 1.1.0 - 2018-10-22
### Added
- Added a Cookie to redirect to the referer #2

## 1.0.3 - 2018-05-07
### Fix
- Template not found when accessing a multi-segment URL

## 1.0.2 - 2018-03-20
### Added
- A notice can now displayed above the password form

### Fix
- Fix composer install command

## 1.0.1 - 2018-03-20
### Fix
- Fix composer settings

## 1.0.0 - 2018-03-20
### Added
- Initial release

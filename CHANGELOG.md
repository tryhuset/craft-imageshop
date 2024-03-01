# Imageshop Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.0 - 2018-12-11
### Released
- Initial release

## 1.0.1 - 2018-12-11
### Bug fix
- Fixed bug in editor

## 1.0.3 - 2018-12-13
### Fixed vulnerabilities
- Fixed vulnerability with node-sass dependency 'cryptiles'
- Fixed vulnerability with node-sass dependency 'hoek'

## 1.0.4 - 2018-12-13
### Extended caching for image transforms
- Transforms are fairly slow to create, after checking with Imageshop the urls can be cached for a very long time. Set cache duration for images to 1 year

## 1.0.5 - 2018-12-13
### Readme bug

## 1.0.6 - 2018-12-14
### Bugfix
- Added translation filter to button text

## 1.0.7 - 2018-12-14
### Feature
- Added content table image preview

## 1.0.8 - 2018-12-14
### Bugfix
- Fixed issue with table image preview when empty

## 1.0.9 - 2018-12-14
### Feature
- Added base64 encoded transparent pixel property
- Added original url property
- Fixed documentation

## 1.0.10 - 2019-03-29
### Bugfix
- Fixed typos in documentation
- Set permalink cache to 1 year

## 1.0.14 - 2020-01-22
### Bugfix
- Fixed issue with image that has been removed from ImageShop causing error

## 1.0.15 - 2020-04-14
### Bugfix
- Fixed issue with where interface was unavailable when only one was returned from Imageshop

## 1.0.16 - 2020-04-14
### Bugfix
- Follow up, fixed interface bug when selecting images as well

## 1.0.17 - 2020-04-14
### Bugfix
- Fixed image height stretch in 3.4

## 1.0.18 - 2020-04-14
### Bugfix
- Fixed a bug where unspecified ratio would always default to 1

## 1.0.19 - 2020-11-17
### Bugfix
- Fixed compatibility w/Composer 2 (Bug #15)

## 1.0.20 - 2020-11-17
### Bugfix
- Reverted accidental v2 changes

## 1.0.21 - 2020-11-17
### Bugfix
- Removed deprecation warning

## 1.0.22 - 2020-12-17
### Bugfix
- Fixed references to old asset bundle path causing errors fro some users
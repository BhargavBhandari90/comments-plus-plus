# Comments Plus Plus #
[![Lint JS](https://github.com/BhargavBhandari90/comments-plus-plus/actions/workflows/lint-js.yml/badge.svg)](https://github.com/BhargavBhandari90/comments-plus-plus/actions/workflows/lint-js.yml)
[![WPCS](https://github.com/BhargavBhandari90/comments-plus-plus/actions/workflows/wpcs.yml/badge.svg)](https://github.com/BhargavBhandari90/comments-plus-plus/actions/workflows/wpcs.yml)

**Contributors:** [bhargavbhandari90](https://profiles.wordpress.org/bhargavbhandari90/)  
**Donate link:** https://www.paypal.me/BnB90/20  
**Tags:** plugin  
**Requires at least:** 6.6  
**Tested up to:** 6.7  
**Stable tag:** 1.0.0  
**Requires PHP:** 5.6  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

## Description ##

A different experience of commenting in WordPress.

## Features ##

- Gmail like auto-complete replies while typing using following AIs.
    - Gemini
    - Ollama
    - OpenAI

## Prerequisites
- [Node/NPM](https://nodejs.org/en/download/)
- [Composer](https://getcomposer.org/)

## Development Setup
1. Go to plugin's root
2. Run `composer install`
2. Run `npm install`
5. To watch for changes, run `npm start`

## Development

To auto-load files, run:

    composer dump-autoload

To create a publish ready plugin, run:

	npm run build

To format code, run:

	npm run format

To lint JS, run:

	npm run lint:js

To lint CSS, run:

	npm run lint:css


## Changelog ##

### 1.0.0 ###
* Initial Release.

# Pushbullet

[![License MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A PHP library for the [Pushbullet](https://www.pushbullet.com/) API.

## Requirements

* PHP 7.0 or newer
* curl extension
* Pushbullet access token

## Usage

```
$pb = new Pushbullet\Pushbullet($token);
$pb->pushNote($title, $body);
$pb->pushLink($title, $body, $url);
$pb->pushFile($filePath, $title, $body);
```

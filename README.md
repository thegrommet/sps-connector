# SPS-Connector

A PHP library for integrating with SPS Commerce via SFTP.

### Build status

[![CircleCI](https://circleci.com/gh/thegrommet/sps-connector.svg?style=svg)](https://circleci.com/gh/thegrommet/sps-connector)

## Install

```shell
composer require thegrommet/sps-connector
```

## Usage

### GS1 Label Generation

> See [generate-label.php](example/generate-label.php)

```php
use SpsConnector\Document\ShippingLabel;
use SpsConnector\LabelService;

$labelDocument = new ShippingLabel();
$label = $labelDocument->addLabel();
// add label specifics

$service = new LabelService('username', 'password');
$pdf = $service->getLabel($labelDocument->__toString(), '5311', $service::FORMAT_PDF);
file_put_contents('label.pdf', $pdf);
```

## Running tests

```shell
composer test
```

## Code sniff & fix

```shell
# sniff src folder
composer cs
# sniff tests folder
composer cs-tests

# fix src folder
composer cbf
# fix tests folder
composer cbf-tests
```

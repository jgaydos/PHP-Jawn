# PHP-Jawn
My collection of use(less|full) PHP classes to make life easier.

**Word of warning: This is likely still teeming with bugs. Like lots.**

## Minimum Requirements

 * PHP 7.2
 * Composer

## Installation

```bash
composer require jgaydos/php-jawn
```
## Usage

### Quick Setup
Basic example of writing messages to the console.
```php
<?php

use Jawn\Console;

require_once(__DIR__ . '/../vendor/autoload.php');

Console::info('Info message');
Console::success('Success message');
Console::warning('Warning message');
Console::danger('Danger message');
```

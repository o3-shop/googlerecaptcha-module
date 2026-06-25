<?php

/**
 * This file is part of O3-Shop.
 *
 * O3-Shop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>
 *
 * @copyright  Copyright (c) 2026 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

declare(strict_types=1);

/*
 * Standalone test bootstrap for the un-installed module.
 *
 * It reuses the shop's Composer autoloader (which provides the O3-Shop core
 * CAPTCHA contracts, PHPUnit and the google/recaptcha library) and registers
 * this module's own PSR-4 namespaces on top, so the module's tests can run
 * before the module is composer-installed into the shop.
 */

$shopAutoload = getenv('O3_SHOP_AUTOLOAD') ?: '/var/www/html/vendor/autoload.php';

if (!is_file($shopAutoload)) {
    fwrite(STDERR, "Shop autoloader not found at '$shopAutoload'. Set O3_SHOP_AUTOLOAD.\n");
    exit(1);
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require $shopAutoload;

$moduleRoot = dirname(__DIR__);
$loader->addPsr4('O3Shop\\ReCaptcha\\', $moduleRoot . '/src');
$loader->addPsr4('O3Shop\\ReCaptcha\\Tests\\', $moduleRoot . '/tests');
$loader->register();

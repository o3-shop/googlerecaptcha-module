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

$sMetadataVersion = '2.1';
$aModule = [
    'id'          => 'google-recaptcha',
    'title'       => 'Google reCAPTCHA',
    'description' => 'Google reCAPTCHA v2/v2 Invisible/v3 provider for the O3-Shop core CAPTCHA layer',
    'version'     => '1.1.0',
    'author'      => 'O3-Shop',
    'url'         => 'https://www.o3-shop.com/',
    'email'       => 'info@o3-shop.com',
    'extend'      => [
        \OxidEsales\Eshop\Application\Component\UserComponent::class =>
            \O3Shop\ReCaptcha\Component\UserComponent::class,
        \OxidEsales\Eshop\Application\Controller\NewsletterController::class =>
            \O3Shop\ReCaptcha\Controller\NewsletterController::class,
    ],
    'blocks'      => [
        ['template' => 'form/contact.tpl',               'block' => 'captcha_form', 'file' => 'views/blocks/captcha_form_contact.tpl'],
        ['template' => 'form/forgotpwd_email.tpl',       'block' => 'captcha_form', 'file' => 'views/blocks/captcha_form_forgotpwd.tpl'],
        ['template' => 'form/fieldset/user_billing.tpl', 'block' => 'captcha_form', 'file' => 'views/blocks/captcha_form_register.tpl'],
        ['template' => 'form/newsletter.tpl',            'block' => 'captcha_form', 'file' => 'views/blocks/captcha_form_newsletter.tpl'],
    ],
];

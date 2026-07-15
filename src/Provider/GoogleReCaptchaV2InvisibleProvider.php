<?php

/**
 * This file is part of O3-Shop.
 *
 * O3-Shop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>
 *
 * @copyright  Copyright (c) 2026 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

declare(strict_types=1);

namespace O3Shop\ReCaptcha\Provider;

use O3Shop\ReCaptcha\Verifier\CaptchaVerifierInterface;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\EshopCommunity\Internal\Domain\Captcha\Configuration\CaptchaConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Captcha\Field\CaptchaConfigField;
use OxidEsales\EshopCommunity\Internal\Domain\Captcha\Provider\CaptchaProviderInterface;

final class GoogleReCaptchaV2InvisibleProvider implements CaptchaProviderInterface
{
    public const ID = 'google_recaptcha_v2_invisible';
    private const TOKEN_FIELD = 'g-recaptcha-response';

    public function __construct(
        private CaptchaConfigurationInterface $configuration,
        private CaptchaVerifierInterface $verifier
    ) {}

    public function getId(): string    { return self::ID; }
    public function getTitle(): string { return 'O3_CAPTCHA_PROVIDER_GOOGLE_V2_INVISIBLE'; }

    public function getConfigFields(): array
    {
        return [
            new CaptchaConfigField('siteKey',   'O3_CAPTCHA_SITE_KEY',   CaptchaConfigField::TYPE_TEXT),
            new CaptchaConfigField('secretKey', 'O3_CAPTCHA_SECRET_KEY', CaptchaConfigField::TYPE_PASSWORD),
        ];
    }

    public function isConfigured(): bool
    {
        return $this->siteKey() !== '' && $this->secretKey() !== '';
    }

    public function getHeadScript(): ?string
    {
        if ($this->siteKey() === '') { return null; }
        return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    }

    public function renderWidget(string $formId): string
    {
        if ($this->siteKey() === '') { return ''; }
        $site = htmlspecialchars($this->siteKey(), ENT_QUOTES);

        return <<<HTML
<style>.grecaptcha-badge{visibility:hidden}</style>
<div class="g-recaptcha-v2-invisible"></div>
<script>
(function () {
    var s = document.currentScript;
    var form = s ? s.closest('form') : null;
    var container = s ? s.previousElementSibling : null;
    if (!form || !container) { return; }
    var widgetId = null;
    var pending = false;
    window.addEventListener('load', function () {
        widgetId = grecaptcha.render(container, {
            sitekey: '{$site}',
            size: 'invisible',
            callback: function () {
                form.dataset.o3CaptchaDone = '1';
                form.submit();
            }
        });
        if (pending) { grecaptcha.execute(widgetId); }
        document.querySelectorAll('.recaptcha-notice a').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                window.open(a.href, '_blank', 'noopener,noreferrer');
            }, true);
        });
    });
    form.addEventListener('submit', function (e) {
        if (form.dataset.o3CaptchaDone === '1') { return; }
        e.preventDefault();
        if (widgetId !== null) {
            grecaptcha.execute(widgetId);
        } else {
            pending = true;
        }
    });
})();
</script>
HTML;
    }

    public function verify(Request $request, string $formId): bool
    {
        $token = trim((string) $request->getRequestEscapedParameter(self::TOKEN_FIELD));
        if ($token === '') { return false; }
        return $this->verifier->verify($this->secretKey(), $token, $this->remoteIp(), null, null)->isSuccess();
    }

    private function siteKey(): string
    {
        return (string) $this->configuration->getProviderSetting(self::ID, 'siteKey', '');
    }

    private function secretKey(): string
    {
        return (string) $this->configuration->getProviderSetting(self::ID, 'secretKey', '');
    }

    private function remoteIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        return is_string($ip) && $ip !== '' ? $ip : null;
    }
}

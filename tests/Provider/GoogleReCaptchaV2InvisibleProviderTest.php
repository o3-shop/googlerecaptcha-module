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

namespace O3Shop\ReCaptcha\Tests\Provider;

use O3Shop\ReCaptcha\Provider\GoogleReCaptchaV2InvisibleProvider;
use O3Shop\ReCaptcha\Verifier\CaptchaVerifierInterface;
use O3Shop\ReCaptcha\Verifier\VerificationResult;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\EshopCommunity\Internal\Domain\Captcha\Configuration\CaptchaConfigurationInterface;
use PHPUnit\Framework\TestCase;

class GoogleReCaptchaV2InvisibleProviderTest extends TestCase
{
    private function config(array $values): CaptchaConfigurationInterface
    {
        $config = $this->createMock(CaptchaConfigurationInterface::class);
        $config->method('getProviderSetting')->willReturnCallback(
            fn (string $providerId, string $key, $default = null) => $values[$key] ?? $default
        );
        return $config;
    }

    public function testIdAndTitle(): void
    {
        $provider = new GoogleReCaptchaV2InvisibleProvider($this->config([]), $this->createMock(CaptchaVerifierInterface::class));
        $this->assertSame('google_recaptcha_v2_invisible', $provider->getId());
        $this->assertSame('O3_CAPTCHA_PROVIDER_GOOGLE_V2_INVISIBLE', $provider->getTitle());
    }

    public function testConfigFieldKeysAreSiteKeyAndSecretKey(): void
    {
        $provider = new GoogleReCaptchaV2InvisibleProvider($this->config([]), $this->createMock(CaptchaVerifierInterface::class));
        $keys = array_map(fn ($f) => $f->getKey(), $provider->getConfigFields());
        $this->assertSame(['siteKey', 'secretKey'], $keys);
    }

    public function testIsConfiguredRequiresBothKeys(): void
    {
        $this->assertFalse((new GoogleReCaptchaV2InvisibleProvider($this->config(['siteKey' => 'a']), $this->createMock(CaptchaVerifierInterface::class)))->isConfigured());
        $this->assertFalse((new GoogleReCaptchaV2InvisibleProvider($this->config(['secretKey' => 'b']), $this->createMock(CaptchaVerifierInterface::class)))->isConfigured());
        $this->assertTrue((new GoogleReCaptchaV2InvisibleProvider($this->config(['siteKey' => 'a', 'secretKey' => 'b']), $this->createMock(CaptchaVerifierInterface::class)))->isConfigured());
    }

    public function testHeadScriptUsesPlainApiJsWithoutRenderParam(): void
    {
        $provider = new GoogleReCaptchaV2InvisibleProvider($this->config(['siteKey' => 'SITE']), $this->createMock(CaptchaVerifierInterface::class));
        $script = $provider->getHeadScript();
        $this->assertNotNull($script);
        $this->assertStringContainsString('api.js', $script);
        $this->assertStringNotContainsString('?render=', $script);
    }

    public function testRenderWidgetContainsSiteKeyAndInvisiblePattern(): void
    {
        $provider = new GoogleReCaptchaV2InvisibleProvider($this->config(['siteKey' => 'SITE123']), $this->createMock(CaptchaVerifierInterface::class));
        $html = $provider->renderWidget('newsletter');
        $this->assertStringContainsString('SITE123', $html);
        $this->assertStringContainsString("size: 'invisible'", $html);

        $empty = new GoogleReCaptchaV2InvisibleProvider($this->config([]), $this->createMock(CaptchaVerifierInterface::class));
        $this->assertSame('', $empty->renderWidget('newsletter'));
    }

    public function testVerifyDelegatesToVerifierAndShortCircuitsOnEmptyToken(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getRequestEscapedParameter')->with('g-recaptcha-response')->willReturn('');
        $verifier = $this->createMock(CaptchaVerifierInterface::class);
        $verifier->expects($this->never())->method('verify');
        $provider = new GoogleReCaptchaV2InvisibleProvider($this->config(['secretKey' => 'SECRET']), $verifier);
        $this->assertFalse($provider->verify($request, 'newsletter'));

        $request2 = $this->createMock(Request::class);
        $request2->method('getRequestEscapedParameter')->with('g-recaptcha-response')->willReturn('tok');
        $verifier2 = $this->createMock(CaptchaVerifierInterface::class);
        $verifier2->expects($this->once())->method('verify')
            ->with('SECRET', 'tok', $this->anything(), null, null)
            ->willReturn(new VerificationResult(true));
        $provider2 = new GoogleReCaptchaV2InvisibleProvider($this->config(['secretKey' => 'SECRET']), $verifier2);
        $this->assertTrue($provider2->verify($request2, 'newsletter'));
    }
}

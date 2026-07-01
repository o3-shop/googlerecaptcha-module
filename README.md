# O3-Shop Google reCAPTCHA Module

Google reCAPTCHA **v2** (checkbox) and **v3** (invisible/score) providers for the
O3-Shop core CAPTCHA layer.

The module ships no controllers, templates or blocks of its own — the storefront
widget rendering and the admin CAPTCHA configuration screen already live in the
O3-Shop core. This module only contributes two CAPTCHA *providers*, which plug
into the core via the `oxid.captcha.provider` DI tag. Once activated, the two
Google providers appear in **Admin → CAPTCHA** and can be configured there.

## Requirements

- **PHP 8.0+** (the module depends on [`google/recaptcha`](https://github.com/google/recaptcha), which requires `php >= 8`)
- O3-Shop with the core pluggable CAPTCHA layer (`CaptchaProviderInterface` + the `oxid.captcha.provider` tag)

## Install

```bash
composer require o3-shop/google-recaptcha-module
```

This pulls in `google/recaptcha` and copies the module into
`source/modules/o3-shop/google-recaptcha`.

## Activate

```bash
./vendor/bin/oe-console oe:module:activate google-recaptcha
```

(or activate it from the admin module list).

On activation, the core's `CaptchaProviderLocator` collects every service tagged
`oxid.captcha.provider` — including this module's two providers — so they become
selectable immediately. No further wiring is required.

## Configure

In **Admin → CAPTCHA**:

1. Select **Google reCAPTCHA v2 (checkbox)** or **Google reCAPTCHA v3 (invisible/score)** as the active provider.
2. Enter your **Site key** and **Secret key** (from the [Google reCAPTCHA admin console](https://www.google.com/recaptcha/admin)).
3. For v3, optionally set the **Minimum score to pass (0.0–1.0)** (default `0.5`).

## How it works

| Class | Role |
|---|---|
| `O3Shop\ReCaptcha\Provider\GoogleReCaptchaV2Provider` | v2 checkbox provider (`google_recaptcha_v2`) |
| `O3Shop\ReCaptcha\Provider\GoogleReCaptchaV3Provider` | v3 invisible/score provider (`google_recaptcha_v3`) |
| `O3Shop\ReCaptcha\Verifier\GoogleReCaptchaVerifier` | server-side token verification via `google/recaptcha` |
| `O3Shop\ReCaptcha\Verifier\VerificationResult` | immutable verification outcome (success/score/action/errors) |

The providers read their credentials through the core
`CaptchaConfigurationInterface` (keys namespaced as `sCaptcha_<providerId>_<fieldKey>`)
and delegate verification to the module's `CaptchaVerifierInterface`.

## License

GPL-3.0

# O3-Shop Google reCAPTCHA Module

Google reCAPTCHA **v2** (checkbox), **v2 Invisible** (challenge only when needed),
and **v3** (invisible/score) providers for the O3-Shop core CAPTCHA layer.

The module registers three CAPTCHA providers via the `oxid.captcha.provider` DI tag.
Once activated, they appear in **Admin → CAPTCHA** and can be configured there.

The module also ships block templates for the standard O3-Shop frontend forms
(contact, newsletter, register, forgot-password). Each block renders the active
CAPTCHA widget and — when a CAPTCHA is active — adds a disclosure notice per
Google's reCAPTCHA Terms of Service:

> This site is protected by reCAPTCHA –
> [Privacy](https://policies.google.com/privacy) &
> [Terms](https://policies.google.com/terms) apply.

This satisfies the ToS requirement for installations using v2 Invisible (badge hidden)
or v3 (no visible widget).

## Requirements

- **PHP 8.0+** (the module depends on [`google/recaptcha`](https://github.com/google/recaptcha), which requires `php >= 8`)
- O3-Shop with the core pluggable CAPTCHA layer (`CaptchaProviderInterface` + the `oxid.captcha.provider` DI tag)

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
`oxid.captcha.provider` — including this module's three providers — so they become
selectable immediately. No further wiring is required.

## Configure

In **Admin → CAPTCHA**:

1. Select the active provider.
2. Enter your **Site key** and **Secret key** (from the [Google reCAPTCHA admin console](https://www.google.com/recaptcha/admin)).
3. For v3, optionally set the **Minimum score to pass (0.0–1.0)** (default `0.5`).

## How it works

| Class | Role |
|---|---|
| `GoogleReCaptchaV2Provider` | v2 checkbox provider (`google_recaptcha_v2`) |
| `GoogleReCaptchaV2InvisibleProvider` | v2 Invisible provider (`google_recaptcha_v2_invisible`) |
| `GoogleReCaptchaV3Provider` | v3 invisible/score provider (`google_recaptcha_v3`) |
| `GoogleReCaptchaVerifier` | server-side token verification via `google/recaptcha` |
| `VerificationResult` | immutable verification outcome (success / score / errors) |

The providers read their credentials through the core
`CaptchaConfigurationInterface` (keys namespaced as `sCaptcha_<providerId>_<fieldKey>`)
and delegate verification to the module's `CaptchaVerifierInterface`.

### v2 Invisible notes

- Badge hidden via CSS (`.grecaptcha-badge { visibility: hidden }`); disclosure text
  in block templates satisfies Google ToS.
- Widget rendered programmatically via `grecaptcha.render()` inside
  `window.addEventListener('load', ...)` — not `grecaptcha.ready()` — to avoid a
  `ReferenceError` that occurs when the IIFE runs before the async `api.js` has loaded.
- A `pending` flag handles form submits that fire before `window.load`.

## License

GPL-3.0

[{assign var="sWidget" value=$oViewConf->getCaptchaWidget('contact')}][{$sWidget}][{if $sWidget}]
<p class="recaptcha-notice" style="font-size:11px;color:#888;margin-top:4px;">This site is protected by reCAPTCHA - <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Privacy</a> &amp; <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">Terms</a> apply.</p>
[{/if}]

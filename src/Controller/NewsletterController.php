<?php

declare(strict_types=1);

namespace O3Shop\ReCaptcha\Controller;

use OxidEsales\EshopCommunity\Internal\Domain\Captcha\CaptchaServiceInterface;
use OxidEsales\Eshop\Core\Registry;

class NewsletterController extends NewsletterController_parent
{
    public function send(): void
    {
        $captchaService = $this->getContainer()->get(CaptchaServiceInterface::class);
        if (!$captchaService->verifyForForm('newsletter', Registry::getRequest())) {
            Registry::getUtilsView()->addErrorToDisplay('O3_CAPTCHA_FAILED');
            $this->_aRegParams = Registry::getRequest()->getRequestEscapedParameter('editval');
            return;
        }
        parent::send();
    }
}

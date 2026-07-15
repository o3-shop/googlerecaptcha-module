<?php

declare(strict_types=1);

namespace O3Shop\ReCaptcha\Component;

use OxidEsales\EshopCommunity\Internal\Domain\Captcha\CaptchaServiceInterface;
use OxidEsales\Eshop\Core\Registry;

class UserComponent extends UserComponent_parent
{
    public function createUser()
    {
        if (Registry::getRequest()->getRequestParameter('fnc') !== 'registeruser') {
            $captchaService = $this->getContainer()->get(CaptchaServiceInterface::class);
            if (!$captchaService->verifyForForm('register', Registry::getRequest())) {
                Registry::getUtilsView()->addErrorToDisplay('O3_CAPTCHA_FAILED');
                return false;
            }
        }
        return parent::createUser();
    }
}

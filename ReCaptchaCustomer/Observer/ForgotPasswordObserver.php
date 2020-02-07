<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptchaCustomer\Model\IsEnabledForCustomerForgotPasswordInterface;
use Magento\ReCaptchaFrontendUi\Model\CaptchaRequestHandlerInterface;

/**
 * ForgotPasswordObserver
 */
class ForgotPasswordObserver implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var IsEnabledForCustomerForgotPasswordInterface
     */
    private $isEnabledForCustomerForgotPassword;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param UrlInterface $url
     * @param IsEnabledForCustomerForgotPasswordInterface $isEnabledForCustomerForgotPassword
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        UrlInterface $url,
        IsEnabledForCustomerForgotPasswordInterface $isEnabledForCustomerForgotPassword,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->url = $url;
        $this->isEnabledForCustomerForgotPassword = $isEnabledForCustomerForgotPassword;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForCustomerForgotPassword->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}

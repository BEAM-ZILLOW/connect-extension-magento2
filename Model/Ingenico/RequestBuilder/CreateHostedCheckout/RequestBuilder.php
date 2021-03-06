<?php

namespace Netresearch\Epayments\Model\Ingenico\RequestBuilder\CreateHostedCheckout;

use Ingenico\Connect\Sdk\Domain\Hostedcheckout\CreateHostedCheckoutRequest;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\CreateHostedCheckoutRequestFactory;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\Definitions\HostedCheckoutSpecificInput;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\Definitions\HostedCheckoutSpecificInputFactory;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Netresearch\Epayments\Model\Config;
use Netresearch\Epayments\Model\ConfigInterface;
use Netresearch\Epayments\Model\Ingenico\RequestBuilder\Common\RequestBuilder as CommonRequestBuilder;
use Netresearch\Epayments\Model\Ingenico\Token\TokenServiceInterface;

/**
 * Class CreateHostedCheckoutRequestBuilder
 */
class RequestBuilder
{
    /**
     * @var TokenServiceInterface
     */
    private $tokenService;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var HostedCheckoutSpecificInputFactory
     */
    private $hostedCheckoutSpecificInputFactory;

    /**
     * @var CommonRequestBuilder
     */
    private $requestBuilder;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CreateHostedCheckoutRequestFactory
     */
    private $createHostedCheckoutRequestFactory;

    /**
     * RequestBuilder constructor.
     *
     * @param TokenServiceInterface $tokenService
     * @param ResolverInterface $resolver
     * @param UrlInterface $urlBuilder
     * @param CreateHostedCheckoutRequestFactory $createHostedCheckoutRequestFactory
     * @param HostedCheckoutSpecificInputFactory $hostedCheckoutSpecificInputFactory
     * @param ConfigInterface $config
     * @param CommonRequestBuilder $requestBuilder
     */
    public function __construct(
        TokenServiceInterface $tokenService,
        ResolverInterface $resolver,
        UrlInterface $urlBuilder,
        CreateHostedCheckoutRequestFactory $createHostedCheckoutRequestFactory,
        HostedCheckoutSpecificInputFactory $hostedCheckoutSpecificInputFactory,
        ConfigInterface $config,
        CommonRequestBuilder $requestBuilder
    ) {
        $this->tokenService = $tokenService;
        $this->resolver = $resolver;
        $this->urlBuilder = $urlBuilder;
        $this->createHostedCheckoutRequestFactory = $createHostedCheckoutRequestFactory;
        $this->hostedCheckoutSpecificInputFactory = $hostedCheckoutSpecificInputFactory;
        $this->requestBuilder = $requestBuilder;
        $this->config = $config;
    }

    /**
     * @param Order $order
     * @return CreateHostedCheckoutRequest
     */
    public function create(Order $order)
    {
        $request = $this->createHostedCheckoutRequestFactory->create();
        $request = $this->requestBuilder->create($request, $order);

        $request->hostedCheckoutSpecificInput = $this->buildHostedCheckoutSpecificInput($order);

        return $request;
    }

    /**
     * @param Order $order
     * @return HostedCheckoutSpecificInput
     */
    private function buildHostedCheckoutSpecificInput(Order $order)
    {
        $specificInput = $this->hostedCheckoutSpecificInputFactory->create();
        $specificInput->locale = $this->resolver->getLocale();
        $specificInput->returnUrl = $this->urlBuilder->getUrl(CommonRequestBuilder::HOSTED_CHECKOUT_RETURN_URL);
        $specificInput->showResultPage = false;
        $specificInput->tokens = $this->getTokens($order);
        $specificInput->validateShoppingCart = true;
        $specificInput->returnCancelState = true;
        if ($variant = $this->config->getHostedCheckoutVariant($order->getStoreId())) {
            $specificInput->variant = $variant;
        }

        return $specificInput;
    }

    /**
     * @param Order $order
     * @return null|string  String of comma separated token values
     */
    private function getTokens(Order $order)
    {
        $customerId = $order->getCustomerId();
        $tokenizationRequested = ($order->getPayment()->getAdditionalInformation(Config::PRODUCT_TOKENIZE_KEY) === '1');
        if (!$customerId || !$tokenizationRequested) {
            return null;
        }
        $tokens = $this->tokenService->find($customerId);
        if (empty($tokens)) {
            return null;
        }

        return implode(',', $tokens);
    }
}

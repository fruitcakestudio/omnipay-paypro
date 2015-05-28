<?php

namespace Omnipay\PayPro;

use Omnipay\Tests\GatewayTestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class GatewayTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    /** @var  array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $post = array('amount' => '1234', 'type' => 'Verkoop');
        $server = array('REMOTE_ADDR' => '178.22.62.12');
        $httpRequest = new HttpRequest(array(), $post, array(), array(), array(), $server);

        $this->gateway = new Gateway($this->getHttpClient(), $httpRequest);

        $this->gateway->initialize(array(
            'apiKey' => 'YOUR API KEY',
        ));

        $this->options = array(
          'amount' => 12.34,
          'description' => 'Payment test',
          'return_url' => 'omnipay-paypro.fcs/return.php',
        );
    }

    public function testPurchase()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        /** @var Message\PurchaseResponse $response */
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('4d17eb61649e82d226f69603de8ad', $response->getTransactionReference());
        $this->assertEquals('https://www.paypro.nl/betalen/4d17eb61649e82d226f69603de8ad', $response->getRedirectUrl());
        $this->assertNull($response->getMessage());
    }

    public function testPurchaseFail()
    {
        $this->setMockHttpResponse('PurchaseFail.txt');

        /** @var Message\PurchaseResponse $response */
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertEquals('Invalid product ID', $response->getMessage());
    }

    public function testCompletePurchase()
    {
        /** @var Message\PurchaseResponse $response */
        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(12.34, $response->getAmount());
    }

    public function testFetchIssuersSuccess()
    {
        $this->setMockHttpResponse('FetchPaymentMethodsSuccess.txt');

        /** @var Message\FetchIssuersResponse $response */
        $response = $this->gateway->fetchIssuers($this->options)->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testFetchIssuersSuccessFail()
    {
        $this->setMockHttpResponse('FetchPaymentMethodsFail.txt');

        /** @var Message\FetchIssuersResponse $response */
        $response = $this->gateway->fetchIssuers($this->options)->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function testPaymentMethodsSuccess()
    {
        $this->setMockHttpResponse('FetchPaymentMethodsSuccess.txt');

        /** @var Message\FetchPaymentMethodsResponse $response */
        $response = $this->gateway->fetchPaymentMethods($this->options)->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testPaymentMethodsFail()
    {
        $this->setMockHttpResponse('FetchPaymentMethodsFail.txt');

        /** @var Message\FetchPaymentMethodsResponse $response */
        $response = $this->gateway->fetchPaymentMethods($this->options)->send();

        $this->assertFalse($response->isSuccessful());
    }
}

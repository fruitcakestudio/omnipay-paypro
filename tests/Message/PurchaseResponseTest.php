<?php

namespace Omnipay\PayPro\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    public function testConstruct()
    {
        // response should keep the array data
        $response = new PurchaseResponse($this->getMockRequest(), array('example' => 'value', 'foo' => 'bar'));
        $this->assertEquals(array('example' => 'value', 'foo' => 'bar'), $response->getData());
    }

    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $response = new PurchaseResponse($this->getMockRequest(), $httpResponse->json());

        $this->assertEquals('4d17eb61649e82d226f69603de8ad', $response->getTransactionReference());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response->getRedirectResponse());
        $this->assertEquals('https://www.paypro.nl/betalen/4d17eb61649e82d226f69603de8ad', $response->getRedirectUrl());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertNull($response->getRedirectData());
        $this->assertNull($response->getMessage());
    }
}

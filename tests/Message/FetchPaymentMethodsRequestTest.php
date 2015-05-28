<?php

namespace Omnipay\PayPro\Message;

use Omnipay\Tests\TestCase;

class FetchPaymentMethodsRequestTest extends TestCase
{
    /**
     * @var FetchIssuersRequest
     */
    private $request;

    protected function setUp()
    {
        $this->request = new FetchPaymentMethodsRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('FetchPaymentMethodsSuccess.txt');

        /** @var FetchPaymentMethodsResponse $response */
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());

        $methods = $response->getPaymentMethods();
        $this->assertEquals(8, count($methods));

        /** @var \Omnipay\Common\PaymentMethod $method */
        $method = $methods[0];
        $this->assertInstanceOf('\Omnipay\Common\PaymentMethod', $method);
        $this->assertEquals('sofort', $method->getId());
        $this->assertEquals('Sofort', $method->getName());
    }
}

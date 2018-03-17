<?php
namespace IngresseTest\API\v1;

class UserTest extends \PHPUnit\Framework\TestCase {

    private $http;

    public function setUp()
    {
        $this->http = new \GuzzleHttp\Client(['base_uri' => "http://localhost:1010/api/v1/"]);
    }

    public function testGetAll()
    {
        $response = $this->http->request('GET', 'users');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json", $contentType);
    }

    public function testGetShoulReturn204()
    {
        $response = $this->http->request('GET', 'users/0');

        $this->assertEquals(204, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];

        $this->assertEquals("application/json", $contentType);
    }

    public function tearDown()
    {
        $this->http = null;
    }
}
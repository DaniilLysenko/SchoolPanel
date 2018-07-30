<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiControllerTest extends WebTestCase
{
    public function testSingleStudentAction()
    {
        $client = static::createClient();
        $client->request('GET', '/api/student/140');
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testAddAction()
    {
        $data = ["student" =>
            ["name" => "Name Vut", "age" => 2, "sex" => "1", "phone" => "0987654321"]
        ];
        $client = new Client(['base_uri' => 'http://symstud.loc/api/']);
        $response = $client->post('add', [RequestOptions::JSON => $data]);
        $data = json_decode($response->getBody()->getContents());
        $this->assertTrue($data->student->id !== NULL);
    }
}
<?php
namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagApiTest extends WebTestCase
{
    public function testGetAllTags(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tags/');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testGetTagById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tags/1');

        $response = $client->getResponse();
        if ($response->getStatusCode() === 404) {
            $this->markTestIncomplete('Tag with ID 1 not found.');
        }

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }
}

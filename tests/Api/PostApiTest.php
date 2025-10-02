<?php
namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostApiTest extends WebTestCase
{
    public function testGetAllPosts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/posts/');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetPostById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/posts/1');

        $response = $client->getResponse();
        if ($response->getStatusCode() === 404) {
            $this->markTestIncomplete('Post with ID 1 not found.');
        }

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
    }

    public function testGetPostsByTagId(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/posts/?tagId=1');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testGetPostsByTagName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/posts/?tagName=Symfony');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }
}

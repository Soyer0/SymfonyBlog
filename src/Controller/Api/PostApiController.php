<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/posts')]
class PostApiController extends AbstractController
{
    #[Route('/', name: 'api_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();
        return $this->json($this->serializePosts($posts));
    }

    #[Route('/{id}', name: 'api_post_show', methods: ['GET'])]
    public function show(PostRepository $postRepository, int $id): JsonResponse
    {
        $post = $postRepository->find($id);
        if (!$post) {
            return $this->json(['error' => 'Post not found'], 404);
        }
        return $this->json($this->serializePost($post));
    }

    #[Route('/by-tag/{tagId}', name: 'api_post_by_tag', methods: ['GET'])]
    public function byTag(PostRepository $postRepository, int $tagId): JsonResponse
    {
        $posts = $postRepository->findByTagId($tagId);
        return $this->json($this->serializePosts($posts));
    }

    private function serializePosts(array $posts): array
    {
        return array_map([$this, 'serializePost'], $posts);
    }

    private function serializePost($post): array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'tags' => $post->getTags()->map(fn($t) => $t->getName())->toArray(),
        ];
    }
}

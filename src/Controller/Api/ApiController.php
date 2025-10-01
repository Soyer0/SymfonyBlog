<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/tags', name: 'api_tags', methods: ['GET'])]
    public function getTags(TagRepository $tagRepository, Request $request): JsonResponse
    {
        $id = $request->query->get('id');

        if ($id) {
            $tag = $tagRepository->find($id);
            if (!$tag) {
                return $this->json(['error' => 'Tag not found'], 404);
            }

            $data = [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ];
        } else {
            $tags = $tagRepository->findAll();
            $data = array_map(fn($tag) => [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ], $tags);
        }

        return $this->json($data);
    }

    #[Route('/posts', name: 'api_posts', methods: ['GET'])]
    public function getPosts(PostRepository $postRepository, Request $request): JsonResponse
    {
        $tagId = $request->query->get('tag');
        $postId = $request->query->get('id');
        $slug = $request->query->get('slug');

        if ($postId) {
            $posts = [$postRepository->find($postId)];
        } elseif ($slug) {
            $post = $postRepository->findOneBy(['slug' => $slug]);
            $posts = $post ? [$post] : [];
        } elseif ($tagId) {
            $posts = $postRepository->createQueryBuilder('p')
                ->join('p.tags', 't')
                ->where('t.id = :tagId')
                ->setParameter('tagId', $tagId)
                ->getQuery()
                ->getResult();
        } else {
            $posts = $postRepository->findAll();
        }

        if (!$posts) {
            return $this->json(['error' => 'Post(s) not found'], 404);
        }

        $data = array_map(fn($post) => [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'tags' => $post->getTags()->map(fn($tag) => $tag->getName())->toArray(),
        ], $posts);

        return $this->json($data);
    }
}

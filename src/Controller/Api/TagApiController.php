<?php

namespace App\Controller\Api;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tags')]
class TagApiController extends AbstractController
{
    #[Route('/', name: 'api_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): JsonResponse
    {
        $tags = $tagRepository->findAll();
        return $this->json(
            array_map(fn($tag) => [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ], $tags)
        );
    }

    #[Route('/{id}', name: 'api_tag_show', methods: ['GET'])]
    public function show(TagRepository $tagRepository, int $id): JsonResponse
    {
        $tag = $tagRepository->find($id);
        if (!$tag) {
            return $this->json(['error' => 'Tag not found'], 404);
        }

        return $this->json([
            'id' => $tag->getId(),
            'name' => $tag->getName(),
        ]);
    }
}

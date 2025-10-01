<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/posts')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index')]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $tagId = $request->query->get('tag');
        if ($tagId) {
            $posts = $postRepository->createQueryBuilder('p')
                ->join('p.tags', 't')
                ->where('t.id = :tagId')
                ->setParameter('tagId', $tagId)
                ->getQuery()
                ->getResult();
        } else {
            $posts = $postRepository->findAll();
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'post_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());

            $existingPost = $em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()]);
            if ($existingPost) {
                return $this->render('post/new.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Slug already exists! Please choose another.',
                ]);
            }

            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Post added!');

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'post_show')]
    public function show(PostRepository $postRepository, string $slug): Response
    {
        $post = $postRepository->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{slug}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post,
        EntityManagerInterface $em
    ): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTimeImmutable());

            $existingPost = $em->getRepository(Post::class)
                ->createQueryBuilder('p')
                ->where('p.slug = :slug')
                ->andWhere('p.id != :id')
                ->setParameter('slug', $post->getSlug())
                ->setParameter('id', $post->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingPost) {
                return $this->render('post/edit.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Slug already exists! Please choose another.',
                ]);
            }

            $em->flush();
            $this->addFlash('success', 'Post updated!');
            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/{slug}/delete', name: 'post_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post,
        EntityManagerInterface $em
    ): Response
    {
        if ($this->isCsrfTokenValid('delete-post-' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Post deleted!');
        }

        return $this->redirectToRoute('post_index');
    }

}

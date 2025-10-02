<?php

namespace App\Controller\Web;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/tags')]
class TagController extends AbstractController
{
    #[Route('/', name: 'tag_index')]
    public function index(TagRepository $tagRepository, Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash('success', 'Tag added!');
            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
}

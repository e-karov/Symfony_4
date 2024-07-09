<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Service\FileUploader;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AlbumController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $albums = $doctrine->getRepository(Album::class)->findAll();

        return $this->render('album/index.html.twig', [
            'albums' => $albums
        ]);
    }

    #[Route('/create', name:'create')]
    public function create(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $album = new Album();
        $album->setCreateDate(new \DateTime('now'));

        $form = $this->createForm(AlbumType::class, $album);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $album = $form->getData();
            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                $pictureFileName = $fileUploader->upload($pictureFile);
                $album->setPicture($pictureFileName);
            }

            $em = $doctrine->getManager();
            $em->persist($album);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('album/create.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/details/{id}', name:'details')]
    public function detailsPage($id, ManagerRegistry $doctrine): Response
    {
        $album = $doctrine->getRepository(Album::class)->find($id);

        return $this->render('album/details.html.twig', ['album'=> $album]);
    }

    #[Route('/edit/{id}', name:'edit')]
    public function editPage($id, ManagerRegistry $doctrine, Request $request, FileUploader $fileUploader): Response
    {
        $album = $doctrine->getRepository(Album::class)->find($id);

        $form = $this->createForm(AlbumType::class, $album);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $album = $form->getData();

            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                ($album->getPicture()) ? unlink("pictures/{$album->getPicture()}") : "";
                $pictureFileName = $fileUploader->upload($pictureFile);
                $album->setPicture($pictureFileName);
            }


            $em = $doctrine->getManager();
            $em->persist($album);
            $em->flush();

            $this->addFlash("notice", "Album Information Edited");

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('album/edit.html.twig', ['form'=> $form]);
    }

    #[Route('/delete/{id}', name:'delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $album = $doctrine->getRepository(Album::class)->find($id);
        $em->remove($album);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}

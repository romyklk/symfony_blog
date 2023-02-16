<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {

        $user = $this->getUser(); // Pour récupérer l'utilisateur connecté
        $articles = $articleRepository->findByAuthor($user); // Pour récupérer les articles de l'utilisateur connecté


        return $this->render('article/index.html.twig', [
            'articles' => $articles, // On passe les articles à la vue
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser()); // On récupère l'utilisateur connecté
            $article->setCreatedAt(new \DateTime()); // On récupère la date actuelle
            $articleRepository->save($article, true);
            // Traitement de l'image
            /*  $file = $form['imageFile']->getData(); // On récupère le fichier
            $fileName = $upload->uploadImage($file); // On enregistre le fichier */

            //dd($fileName);
            /*             $file = $form['imageFile']->getData(); // On récupère le fichier
            $newName = 'article_' . time() . '_' . random_int(55, 999999) . '_' . uniqid();
            while (strlen($newName) < 50) {
                $newName .= random_int(0, 999999);
            }
            $fileName = $newName . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('image_dir'), $fileName);

            $file_url = $this->getParameter('image_dir') . '/' . $fileName;
           // dd($fileName);
            $article->setImageUrl($file_url); // On enregistre le nom de l'image dans la base de données
            $article->setAuthor($this->getUser()); // On récupère l'utilisateur connecté */

            $file = $form['imageFile']->getData();
            $code = "aze86rt3yu1iop9qsd8f7gh5jklm2w8xc6vbn";
            $result = "";

            while (strlen($result) < 30) { // On veut un nom de 20 caractères
                $result .= $code[rand(0, strlen($code) - 1)]; // On tire un caractère au hasard
            }

            $extension = $file->guessExtension();
            $filename = 'article_' . time() . '_' . random_int(55, 999999) . '_' . uniqid().".".$extension;
            $file->move($this->getParameter('image_dir'), $filename);
            $file_url = $this->getParameter('image_dir') . '/' . $filename;
            $article->setImageUrl($file_url); // On enregistre le nom de l'image dans la base de données
            $article->setAuthor($this->getUser()); // On récupère l'utilisateur connecté




            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}

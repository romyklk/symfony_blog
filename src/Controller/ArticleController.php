<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Cocur\Slugify\Slugify;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account')]
class ArticleController extends AbstractController
{


    // Fonction pour enregistrer un fichier dans le dossier articles
    public function saveFile($file)
    {
        // On génère un nouveau nom de fichier
        // $newName = $this->renameFile();
        // On récupère le nom du fichier
        $fileName = $file->getClientOriginalName();

        // On génère un nouveau nom de fichier
        $fileName = 'article_' . time() . '_' . random_int(55, 999999) . '_' . uniqid() . '_' . bin2hex(random_bytes(10)) . '.' . $file->guessExtension();

        // On déplace le fichier dans le dossier images
        $file->move($this->getParameter('image_dir'), $fileName);
        return "/assets/images/articles/" . $fileName;
    }

    // Fonction pour mettre à jour un fichier

    public function updateFile($file, $oldFileName)
    {
        // Je sauvegarde le nouveau fichier
        $file_url = $this->saveFile($file);

        try {
            // On supprime l'ancien fichier
        unlink($this->getParameter('static_dir') . $oldFileName);
        } catch (\Throwable $th) {
            //throw $th; // On ne fait rien si le fichier n'existe pas
        }
        

        return $file_url;
    }


    public function removeFile($file)
    {
        try {
            // On supprime l'ancien fichier
        unlink($this->getParameter('static_dir') . $file);
        } catch (\Throwable $th) {
            //throw $th; // On ne fait rien si le fichier n'existe pas
        }
        return true; // On retourne true si tout s'est bien passé
    }


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
    public function new(Request $request, ArticleRepository $articleRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slugify = new Slugify(); // On instancie Slugify pour créer un slug à partir du titre
            $article->setCreatedAt(new \DateTime()); // On récupère la date actuelle
            $article->setSlug($slugify->slugify($article->getTitle())); // On crée le slug

            $file = $form['imageFile']->getData(); // On récupère le fichier

            // j'appelle la fonction saveFile pour enregistrer l'image
            $imgLink = $this->saveFile($file);

            // dd($imgLink);

            // On enregistre le nom du fichier dans la base de données

            $article->setImageUrl($imgLink);
            // dd($file);

            $article->setAuthor($this->getUser()); // On récupère l'utilisateur connecté


            // On enregistre l'article en base de données
            // $articleRepository->save($article, true);

            // Je fais une boucle sur les catégories de l'article pour les ajouter à l'article
/*             foreach ($article->getCategories()->getValues() as $category) {
                // On ajoute l'article à la catégorie
                $category->addArticle($article);

                // On enregistre la catégorie en base de données
                $entityManager->persist($category);
            }
 */

            $entityManager->persist($article);
            $entityManager->flush(); // On enregistre en base de données

            // On redirige l'utilisateur vers la page de l'article
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
        return $this->render('home/oneArticle.html.twig', [
            'article' => $article,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setUpdatedAt(new \DateTime()); // On récupère la date actuelle

            $file = $form['imageFile']->getData(); // On récupère le fichier

            if ($file) {
                // On appelle la fonction updateFile pour mettre à jour l'image
                $imgLink = $this->updateFile($file, $article->getImageUrl());

                //dd($imgLink);

                // On enregistre le nom du fichier dans la base de données
                $article->setImageUrl($imgLink);
            } else { // Si l'utilisateur n'a pas changé l'image
                // On enregistre le nom de l'ancienne image
                $article->setImageUrl($article->getImageUrl());
            }

            // Je fais une boucle sur les catégories de l'article pour les ajouter à l'article
           /*  foreach ($article->getCategories()->getValues() as $category) {
                // On ajoute l'article à la catégorie
                $category->addArticle($article);

                // On enregistre la catégorie en base de données
                $entityManager->persist($category);
            }
 */

            $entityManager->persist($article);
            $entityManager->flush(); // On enregistre en base de données
            // $articleRepository->save($article, true);

            // On redirige l'utilisateur vers la page de l'article modifié (app_one_article)
            return $this->redirectToRoute('app_one_article', ["slug"=>$article->getSlug()], Response::HTTP_SEE_OTHER);
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
            // On appelle la fonction removeFile pour supprimer l'image
            $this->removeFile($article->getImageUrl()); 

            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}

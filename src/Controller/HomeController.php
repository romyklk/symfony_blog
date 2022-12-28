<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    // 1
    #[Route('/', name: 'app_home')]
    // injection de dépendance : on demande à Symfony de nous fournir un objet de la classe ArticleRepository qui nous permettra de récupérer les articles de la BDD (via Doctrine) qu'on stockera dans la variable $repoArticles. Je recupère aussi les catégories pour les afficher dans le menu
    public function index(ArticleRepository $repoArticles,CategoryRepository $repoCategories): Response 
    { 

        $articles = $repoArticles->findAll(); // SELECT * FROM article pour récupérer tous les articles
        $repoCategories = $repoCategories->findAll(); // SELECT * FROM category pour récupérer toutes les catégories

        // dd($articles); // dump and die : affiche les données et arrête le script

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'categories' => $repoCategories,
        ]);
    }


    // 2
    #[Route('/article/{slug}', name: 'app_one_article')]
    // injection de dépendance : on demande à Symfony de nous fournir un objet de la classe ArticleRepository qui nous permettra de récupérer les articles de la BDD (via Doctrine) qu'on stockera dans la variable $repoArticles . Je recupères aussi toutes les catégories dans lesquel se trouve l'article 
    public function article(ArticleRepository $repoArticles,string $slug,CategoryRepository $repoCategories): Response
    {
        $article = $repoArticles->findOneBySlug($slug); // SELECT * FROM article WHERE slug = $slug
        $repoCategories = $repoCategories->findAll();

        return $this->render('home/oneArticle.html.twig', [
            'article' => $article,
            'categories' => $repoCategories,
        ]);
    }


    // 3
    #[Route('/category/{slug}', name: 'app_one_category')]

    public function category(ArticleRepository $repoArticles,CategoryRepository $repoCategories,string $slug): Response
    {
        $category = $repoCategories->findOneBySlug($slug); // SELECT * FROM category WHERE slug = $slug

        $articles = []; // on initialise un tableau vide pour stocker les articles de la catégorie

        if($category){ // si la catégorie existe
            $articles = $category->getArticles(); // on récupère les articles de la catégorie
        }

        $repoCategories = $repoCategories->findAll(); // SELECT * FROM category

        return $this->render('home/oneCategory.html.twig', [
            'articles' => $articles,
            'categories' => $repoCategories,
            'category' => $category,

        ]);
    }
}

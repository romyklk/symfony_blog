<?php

namespace App\Controller;


use App\service\CategoriesServices;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

        // on créé un constructeur qui va récupérer le service CategoryService et le stocker dans la variable $categoryService
        public function __construct(CategoriesServices $categoriesServices)
        {
            $categoriesServices->updateSession(); // on appelle la méthode updateSession() du service CategoryService
        } 


    // 1
    #[Route('/', name: 'app_home')]
    // injection de dépendance : on demande à Symfony de nous fournir un objet de la classe ArticleRepository qui nous permettra de récupérer les articles de la BDD (via Doctrine) qu'on stockera dans la variable $repoArticles. Je recupère aussi les catégories pour les afficher dans le menu
    public function index(Request $request ,ArticleRepository $repoArticles): Response 
    { 


        $articles = $repoArticles->findAll(); // SELECT * FROM article pour récupérer tous les articles
       // $repoCategories = $repoCategories->findAll(); // SELECT * FROM category pour récupérer toutes les catégories

        // dd($articles); // dump and die : affiche les données et arrête le script

        // Création de la session. On peut y stocker des données qui seront accessibles sur toutes les pages du site
     /*     $session = $request->getSession(); // on récupère la session
        $session->set('categories',$repoCategories); // on stocke une donnée dans la session
 */
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
          //  'categories' => $repoCategories,
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
           // 'categories' => $repoCategories,
            'category' => $category,

        ]);
    }
}

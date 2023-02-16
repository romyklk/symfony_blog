<?php

namespace App\service;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\RequestStack;

// un service est une classe qui contient des méthodes qui peuvent être utilisées dans d'autres classes

// on va créer un service qui va nous permettre de récupérer les catégories de la BDD

class CategoriesServices // on crée une classe CategoryService
{
    private $repoCat; // on stocke le repository des catégories
    private $requestStack; // on stocke le service RequestStack

    public function __construct(RequestStack $requestStack,CategoryRepository $repoCat)
    {
        $this->repoCat = $repoCat; // on récupère le repository des catégories
        $this->requestStack = $requestStack; // on récupère le service RequestStack
    }

    public function updateSession() // on crée une méthode qui va récupérer les catégories de la BDD et les stocker dans la session
    {
        $session = $this->requestStack->getSession(); // on récupère la session

        $categories = $this->repoCat->findAll();  // SELECT * FROM category

        $session->set('categories',$categories); // on stocke les catégories dans la session
    }
}
<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\service\CategoriesServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{

    // on créé un constructeur qui va récupérer le service CategoryService et le stocker dans la variable $categoryService
    public function __construct(CategoriesServices $categoriesServices)
    {
        $categoriesServices->updateSession(); // on appelle la méthode updateSession() du service CategoryService
    }



    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $contact = new Contact();

        $contactForm = $this->createForm(ContactType::class, $contact);

        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {

            $contact->setCreatedAt(new \DateTimeImmutable());


            $contact = $contactForm->getData(); // on récupère les données du formulaire
            $em->persist($contact); // on prépare l'insertion en base de données
            $em->flush(); // on exécute l'insertion en base de données

            // dd($contact); // on affiche les données du formulaire

            // Si tout se passe bien, je vais vider le formulaire donc je crée un nouveau contact vide
            $contact = new Contact();
            $contactForm = $this->createForm(ContactType::class, $contact);

            
            $this->addFlash('success', 'Votre message a bien été envoyé !'); // on ajoute un message flash
            return $this->redirectToRoute('app_contact');

        }elseif ($contactForm->isSubmitted() && !$contactForm->isValid()) {
            $this->addFlash('danger', 'Votre message n\'a pas été envoyé !'); // on ajoute un message flash
        }else{
            $this->addFlash('info', 'Veuillez remplir le formulaire'); // on ajoute un message flash
        }




        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contactForm' => $contactForm->createView()
        ]);
    }
}

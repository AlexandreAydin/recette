<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{

    /**
     * Undocumented function
     * 
     * Le controller permet d'afficher tout les ingrédients 
     * 
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */    

    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            // permet d'appeler tout les ingrédient qui sont dans la base de données
                 // $repository ->findAll(),
            //Permet d'appeler uniquement les ingrédients que l'utilisateur a crée
            $repository ->findBy( ['user' => $this ->getUser()]),
            $request->query->getInt('page', 1), /*Numéro de page*/
            10 /*limit par page*/
        );


        return $this->render('pages/ingredient/index.html.twig', [
            // permet de les passer à la vue dans ingredient.hyml.twig
            'ingredients'=>$ingredients
        ]);
    }

  
    /**
     * Le controller permet de crée et afficher 1 ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'app_ingredient.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ) : Response
        
    {

        $ingredient = new Ingredient();
        // Permet de récupéré Formulaire qui se trouve dans IngredientType
        $form= $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        //si la formulaire a était soumit et valid 
        if($form->isSubmitted() && $form->isValid()){
            // Enregistre Le formulaire dans labase de donnéé
            $ingredient = $form->getData();
            //permet de attaché l'ingrédient à l'utilisateur qui la crée
            $ingredient->setUser($this->getUser());

            $manager->persist($ingredient);
            $manager->flush();       

            // Message pour affiché la validation 
          

            return $this->redirectToRoute('app_ingredient');

        }

       return $this->render('pages/ingredient/new.html.twig', [
        //permet d'affiché le formulaire sur la page twig
        'form' => $form->createView()
       ]);
    }


    // #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/edition/{id}', 'app_ingredient.edit', methods: ['GET' , 'POST'])]

    public function edit(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $manager
        ) : Response 
    {

        // permet de récupéré ingrédient par id (paramconverter symfony permet de récuperer le id sans passer par repository )
        // Permet de récupéré Formulaire qui se trouve dans IngredientType
        $form= $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        //si la formulaire a était soumit et valid 
        if($form->isSubmitted() && $form->isValid()){
            // Enregistre Le formulaire dans labase de donnéé
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();       

            // Message pour affiché la validation 
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès!'
            );

            return $this->redirectToRoute('app_ingredient');
    }

        return $this->render('pages/ingredient/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/suppression/{id}', 'app_ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient ) : Response 
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès!'
        );

        return $this->redirectToRoute('app_ingredient');
    }

}

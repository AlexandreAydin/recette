<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;


class UserController extends AbstractController
{
   /**
     * This controller allow us to edit user's profile
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'app_user.edit')]
   
    public function edit(
    User $choosenUser, 
    Request $request,
    EntityManagerInterface $manager,
    UserPasswordHasherInterface $hasher,
    ): Response
    {
        // // Si l'utilisateur n'est pas connecté on le dirige vers la page de connexion
        // if(!$this->getUser()){
        //     return $this->redirectToRoute('app_security.login');
        // }

        // // on verifie si l'utilisateur qu'on a récupéré correspond bien l'utilisateur qu'on a par rapport à id
        // //Si ce n'est pas le cas on le dirige vers recette
        // if ($this->getUser() !== $user){
        //     return $this->redirectToRoute('app_recipe');
        // }

        //on crée le formulaire
        $form = $this->createForm(userType::class,  $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             // si le mot de passe est correcte alors on autorise la modification
            if ($hasher->isPasswordValid( $choosenUser, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées.'
                );

                return $this->redirectToRoute('app_recipe');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    /**
    * Undocumented function
    *
    * @param User  $choosenUser
    * @param Request $request
    * @param EntityManagerInterface $manager
    * @param UserPasswordHasherInterface $hasher
    * @return Response
    */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'app_user.edit.password', methods : ['GET', 'POST'])]
    public function editPassword(
        User  $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid( $choosenUser, $form->getData()['plainPassword'])) {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié.'
                );

                $manager->persist( $choosenUser);
                $manager->flush();

                return $this->redirectToRoute('app_recipe');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }


   

}

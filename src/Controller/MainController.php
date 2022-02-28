<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
         
    //  CTRL + D 
    /**
     * @Route("/about/", name="about")
     */
    public function about(): Response
    {
        return $this->render('main/about.html.twig', [
            'titre' => 'A propos',
        ]);
    }


    //Afficher la liste des voeux


     /**
     * @Route("/", name="list")   
     * */
    public function list(WishRepository $repo): Response
    {
        $wishes = $repo->findBy([],["dateCreated"=>"DESC"]);
        //var_dump($wishes);
        return $this->render('wish/list.html.twig', [
            'titre' => 'Mes voeux',
            'wishes' => $wishes,
            
        ]);
    }


    //Voir le détail du voeu

    /**
     * @Route("/wishes/{id}", name="wish_detail")
     */
    public function detail(Wish $w): Response
    {
        return $this->render('wish/detail.html.twig', [
            'titre' => 'Détail du voeu',
            'wish'=>$w,
        ]);
    }

    
    // Ajouter un voeu

    /**
     * @Route("/add/", name="wish_add")
     */
    public function add(Request $req, EntityManagerInterface $em)
    {
        $wish = new Wish();//Création de l'objet
        //Création du Form avec assoc. avec $wish
        $form= $this->createForm(WishType::class,$wish);
      // auto hydration
      $form->handleRequest($req);
      if ($form->isSubmitted() && $form->isValid()) {
        // $age
        $age = $form->get('age')->getData();
        if ($age >= 18) {
          $this->addFlash(
            'success',
            'Votre wish est ajouté :'.$wish->getTitle()
        );
          $wish->setIsPublished(true);
          $wish->setDateCreated(new \DateTime());
          $em->persist($wish);
          $em->flush();
          return $this->redirectToRoute('list');
        }else{
          $this->addFlash(
            'danger',
            'Vous devez être majeur'
        );
        }
      }
      // on envoie le formulaire à twig
      return $this->render('wish/add.html.twig', [
        'formulaire' => $form->createView(),
      ]);
    }
   

    //Mettre à jour le voeu

    /**
     * @Route("/update/{id}", name="wish_update")
     */
    public function update(Wish $w, Request $req, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(WishType::class, $w);
        $form-> handleRequest($req);
        if ($form -> isSubmitted()){
            $em->flush();
            return $this->redirectToRoute('list');
        }
        return $this -> render('wish/update.html.twig',
        ['formulaire'=> $form->createView()]);

    }

    /**
     * @Route("delete/{id}", name="wish_delete", methods={"GET"})
     */
    public function delete(Request $request, Wish $wish, EntityManagerInterface $entityManager): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$wish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wish);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('list', [], Response::HTTP_SEE_OTHER);
    }
}




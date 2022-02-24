<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

     /**
     * @Route("/list", name="list")   
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


   /**
     * @Route("/add/", name="wish_add")
     */
    public function add(EntityManagerInterface $em): Response
    {
       //dd($p);
       // je crée un objet à partir de l'entity
       $w = new Wish();
       $w->setTitle("add Test titre");
       $w->setDescription("add test description");
       $w->setAuthor("add test auteur");
       // persist uniquement creation 
       $em->persist($w);
       $em->flush(); // SAVE execute la requete SQL

       //dd($p->getId());
       // rediriger vers home
       return $this->redirectToRoute('list'); 
        
    }

/**
 * @Route("/delete/{id}", name="wish_delete")
 */

 public function delete(EntityManagerInterface $em, Wish $wish) : Response
 {

    $em->remove($wish);
    $em-> flush();
    return $this->redirectToRoute('list');

 }

}

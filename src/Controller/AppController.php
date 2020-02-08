<?php

namespace App\Controller;

use App\Entity\Parametres\UsersGroups\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="dash_board")
     */
    public function index(Breadcrumbs $breadcrumbs)
    {
       // $breadcrumbs->addItem("Accueil", $this->get("router")->generate("dash_board"));


      // $user= $this->get('security.token_storage')->getToken()->getUser();
       $user = $this->getUser();
       //TokenInterface::getUser()
       $prenom = "test " ; //
      $prenom = $user->getPrenom();

    // Add "homepage" route link at the start of the breadcrumbs
    $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        return $this->render('dashboard/dashboard/index.html.twig', [
            'controller_name' => 'DashBoardController',
            'user' => $prenom
        ]);
    }

}

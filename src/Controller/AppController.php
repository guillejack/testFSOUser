<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="dash_board")
     */
    public function index(Breadcrumbs $breadcrumbs)
    {
       // $breadcrumbs->addItem("Accueil", $this->get("router")->generate("dash_board"));




    // Add "homepage" route link at the start of the breadcrumbs
    $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        return $this->render('dashboard/dashboard/index.html.twig', [
            'controller_name' => 'DashBoardController',
        ]);
    }
}

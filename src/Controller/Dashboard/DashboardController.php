<?php

namespace App\Controller\Dashboard;

use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard/dashboard", name="dashboard_dashboard")
     */
    public function index(Breadcrumbs $breadcrumbs)
    {
       // $breadcrumbs->addItem("Accueil", $this->get("router")->generate("dash_board"));




    // Add "homepage" route link at the start of the breadcrumbs
    $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashBoardController',
        ]);
    }
}

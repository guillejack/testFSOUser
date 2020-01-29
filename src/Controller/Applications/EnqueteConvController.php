<?php

namespace App\Controller\Applications;

use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EnqueteConvController extends AbstractController
{
    /**
     * @Route("/applications/enquete/conv", name="applications_enquete_conv")
     */
    public function index(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem("Enquete", $this->get("router")->generate("applications_enquete_conv"));
        $breadcrumbs->prependRouteItem("Accueil", "dash_board");
        
        return $this->render('applications/enquete_conv/index.html.twig', [
            'controller_name' => 'enqueteConvController',
        ]);
    }
}
<?php

namespace App\Controller\Applications\Signalement;

use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SignalementAdmController extends AbstractController
{
    /**
     * @Route("/applications/signalement/adm", name="applications_signalement_adm")
     */
    public function index(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem("Signalements", $this->get("router")->generate("applications_signalement_adm"));
        $breadcrumbs->prependRouteItem("Accueil", "dash_board");
    
        return $this->render('applications/signalement_adm/index.html.twig', [
            'controller_name' => 'signalementAdmController',
        ]);
    
    }
}

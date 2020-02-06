<?php

namespace App\Controller\Parametres\UsersGroups;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Repository\Parametres\UsersGroups\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersGroupsController extends AbstractController
{
    /**
     * @Route("/parametres/users_groups", name="parametres_users_groups")
     */
    public function index(Breadcrumbs $breadcrumbs, Request $request , ServicesRepository $repository)    
    {
        if($request->get('selectTab')){

            $selectTabSelection = $request->get('selectTab');
        }
        else {
            $selectTabSelection = "utilisateurs";
        }
        
        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"));
        $breadcrumbs->prependRouteItem("Accueil", "dash_board");
        return $this->render('parametres/users_groups/index.html.twig', [
            'controller_name' => 'UsersGroupsController',
            'selectTab' => $selectTabSelection
        ]);
    }

}

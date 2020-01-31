<?php

namespace App\Controller\Parametres;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Parametres\UsersGroups\Users;
use App\Entity\Parametres\UsersGroups\Groups;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Parametres\UsersGroups\UsersType;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Parametres\UsersGroups\Services;
use App\Form\Parametres\UsersGroups\GroupsType;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Parametres\UsersGroups\ServicesType;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Repository\Parametres\UsersGroups\UsersRepository;
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

     /**
     * @Route("/parametres/users_groups/services/creation", name="parametres_users_groups_creationServices")
     * @Route("/parametres/users_groups/services/modification/{id}", name="parametres_users_groups_modifServices", methods="GET|POST")
     */
    public function modificationServices(Services $services = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "services"]);
        

        if(!$services){
            $breadcrumbs->addItem("Ajout Service");
            $services = new Services();
        }else
        {
            $breadcrumbs->addItem("Modification Service");
        }

        $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        $form = $this->createForm(ServicesType::class,$services);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($services);
            $om->flush();
            $this->addFlash('success', "L'action a été effectué");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "services"]);
        }

        return $this->render('parametres/users_groups/formServices.html.twig',[
            "service" => $services,
            "form" => $form->createView()
        ]);
    }

     /**
     * @Route("/parametres/users_groups/groupes/creation", name="parametres_users_groups_creationGroupes")
     * @Route("/parametres/users_groups/groupes/{id}", name="parametres_users_groups_modifGroupes", methods="GET|POST")
     */
    public function modificationGroupes(Groups $groupes = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "groupes"]);
        

        if(!$groupes){
            $breadcrumbs->addItem("Ajout Groupe");
            $groupes = new Groups();
        }else
        {
            $breadcrumbs->addItem("Modification Groupe");
        }

        $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        $form = $this->createForm(GroupsType::class,$groupes);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($groupes);
            $om->flush();
            $this->addFlash('success', "L'action a été effectué");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "groupes"]);
        }

        return $this->render('parametres/users_groups/formGroupes.html.twig',[
            "groupe" => $groupes,
            "form" => $form->createView()
        ]);
    }

     /**
     * @Route("/parametres/users_groups/users/creation", name="parametres_users_groups_creationUsers")
     * @Route("/parametres/users_groups/users/{id}", name="parametres_users_groups_modifUsers", methods="GET|POST")
     */
    public function modificationUsers(Users $users = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "utilisateurs"]);
        

        if(!$users){
            $breadcrumbs->addItem("Ajout Utilisateur");
            $users = new Users();
        }else
        {
            $breadcrumbs->addItem("Modification Utilisateur");
        }

        $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        $form = $this->createForm(UsersType::class,$users);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($users);
            $om->flush();
            $this->addFlash('success', "L'action a été effectué");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "utilisateurs"]);
        }

        return $this->render('parametres/users_groups/formUsers.html.twig',[
            "groupe" => $users,
            "form" => $form->createView()
        ]);
    }

     /**
     * @Route("/parametres/users_groups/users/table", name="table_users")
     */
    public function usersTable(Request $request, UsersRepository $repository)
    {
        //initialise le tableau a envoyer
        $data = array();

        //recuperation des filtres
        $length = $request->get('length');
        $length = $length && ($length!=-1)?$length:0;
 
        $start = $request->get('start');
        $start = $length?($start && ($start!=-1)?$start:0)/$length:0;
 
        $search = $request->get('search');
        
        $filters = [
            'search' => $search["value"],
        ];
 
        //recherche dans la base les données filtrées
        $enregistrements = $repository->search(
            $filters, $start, $length
        );
 
  var_dump($enregistrements);
        //boucle des enregistrements trouvés
        foreach ($enregistrements as $enregistrement) {
            //initialiasation du sous tableau de données  à envoyer en Json
            $sub_array = array();

            //ajoute au tableau pour chaque ligne
            $sub_array[] = $enregistrement->getId();
            $sub_array[] = $enregistrement->getNom();
            $sub_array[] = $enregistrement->getPrenom();   
            $data[] = $sub_array;
        }

        //parametre datatable
        $output = array(
            "draw"    => intval( $request->get('draw')),
            "recordsTotal"  =>   count($repository->search(array(), 0, false)),
            "recordsFiltered" => count( $repository->search($filters, 0, false)),
            "data"    => $data,
            "erreur"    => 2       
           );
 
        //envoi en json a datatable   
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

     /**
     * @Route("/parametres/users_groups/services/table", name="table_services")
     */
    public function servicesTable(Request $request, ServicesRepository $repository)
    {
        //initialise le tableau a envoyer
        $data = array();

        //recuperation des filtres
        $length = $request->get('length');
        $length = $length && ($length!=-1)?$length:0;
 
        $start = $request->get('start');
        $start = $length?($start && ($start!=-1)?$start:0)/$length:0;
 
        $search = $request->get('search');
        
        $filters = [
            'search' => "",
        ];
 
        //recherche dans la base les données filtrées
        $enregistrements = $repository->search(
            $filters, $start, $length
        );
 var_dump($enregistrements);
         //boucle des enregistrements trouvés
        foreach ($enregistrements as $enregistrement) {
            //initialiasation du sous tableau de données  à envoyer en Json
            $sub_array = array();

            //ajoute au tableau pour chaque ligne
            $sub_array[] = $enregistrement->getId();
            $sub_array[] = $enregistrement->getName();
            $sub_array[] = $enregistrement->getDescription(); 
            $data[] = $sub_array;
        }


        //parametre datatable
         $output = array(
            "draw"    => intval( $request->get('draw')),
            "recordsTotal"  =>   count($repository->search(array(), 0, false)),
            "recordsFiltered" => count( $repository->search($filters, 0, false)),
            "data"    => $data,
            "erreur"    => 2     
           ); 

 
        //envoi en json a datatable   
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }
}

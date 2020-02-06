<?php

namespace App\Controller\Parametres\UsersGroups;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Parametres\UsersGroups\Services;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Parametres\UsersGroups\ServicesType;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Repository\Parametres\UsersGroups\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ServicesManageController extends AbstractController
{

     /**
     * @Route("/parametres/users_groups/services/creation", name="parametres_users_groups_creationServices")
     * @Route("/parametres/users_groups/services/modification/{id}", name="parametres_users_groups_modifServices", methods="GET|POST")
     */
    public function modificationServices(Services $services = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "services"]);
        

        if(!$services){
            $breadcrumbs->addItem("Ajout Service");
            $services = new Services();
            $titre = "Creation d'un service";
        }else
        {
            $breadcrumbs->addItem("Modification Service");
            $titre = "Modification du service";
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
            "form" => $form->createView(),
            "titre" => $titre
        ]);
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
            'search' => $search["value"],
        ];
 
        //recherche dans la base les données filtrées
        $enregistrements = $repository->search(
            $filters, $start, $length
        );

         //boucle des enregistrements trouvés
        foreach ($enregistrements as $enregistrement) {
            //initialiasation du sous tableau de données  à envoyer en Json
            $sub_array = array();

            //ajoute au tableau pour chaque ligne
            $sub_array[] = $enregistrement->getId();
            $sub_array[] = $enregistrement->getName();
            $sub_array[] = $enregistrement->getDescription(); 
            $sub_array[] = '<a class="edit" title="Editer" data-toggle="tooltip" href="'.$this->generateUrl('parametres_users_groups_modifServices', ['id' => $enregistrement->getId()]).'"><i class="far fa-edit"></i></a>
            <a class="delete" title="Supprimer" data-toggle="tooltip" href="'.$this->generateUrl('parametres_users_groups_delServices', ['id' => $enregistrement->getId()]).'" onclick="return confirm(\'Etes vous sûr de supprimer : '. $enregistrement->getName().'?\')" ><i class="far fa-trash-alt"></i></a>';
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
     * 
     * @Route("/parametres/users_groups/services/suppression/{id}", name="parametres_users_groups_delServices")
     */
    public function suppressionService(Services $service, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

        //$group->removeUser($users);
        //$service->removeUser($users);
        $om->remove($service);
        $om->flush();
        $this->addFlash("success","La suppression a été effectuée");
        return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "services"]);
}
}
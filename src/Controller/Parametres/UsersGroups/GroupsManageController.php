<?php

namespace App\Controller\Parametres\UsersGroups;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Parametres\UsersGroups\Groups;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Parametres\UsersGroups\GroupsType;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Repository\Parametres\UsersGroups\GroupsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GroupsManageController extends AbstractController
{
  /**
     * @Route("/parametres/users_groups/groupes/creation", name="parametres_users_groups_creationGroupes")
     * @Route("/parametres/users_groups/groupes/modification/{id}", name="parametres_users_groups_modifGroupes", methods="GET|POST")
     */
    public function modificationGroupes(Groups $groupes = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "groupes"]);
        

        if(!$groupes){
            $breadcrumbs->addItem("Ajout Groupe");
            $groupes = new Groups();
            $titre = "Creation d'un groupe";
        }else
        {
            $breadcrumbs->addItem("Modification Groupe");
            $titre = "Modification du groupe";
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
            "form" => $form->createView(),
            "titre" => $titre
        ]);
    }

    /**
     * @Route("/parametres/users_groups/groupes/table", name="table_groupes")
     */
    public function groupesTable(Request $request, GroupsRepository $repository)
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
            $sub_array[] = $enregistrement->getRole(); 
            $sub_array[] = '<a class="edit" title="Editer" data-toggle="tooltip" href="'.$this->generateUrl('parametres_users_groups_modifGroupes', ['id' => $enregistrement->getId()]).'"><i class="far fa-edit"></i></a>
            <a class="delete" title="Supprimer" data-toggle="tooltip"  href="'.$this->generateUrl('parametres_users_groups_delGroups', ['id' => $enregistrement->getId()]).'" onclick="return confirm(\'Etes vous sûr de supprimer : '. $enregistrement->getName().'?\')" ><i class="far fa-trash-alt"></i></a>';
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
     * @Route("/parametres/users_groups/groupes/suppression/{id}", name="parametres_users_groups_delGroups")
     */
    public function suppressionGroupes(Groups $group, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

        $om->remove($group);
        $om->flush();
        $this->addFlash("success","La suppression a été effectuée");
        return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "groupes"]);
}
 


}
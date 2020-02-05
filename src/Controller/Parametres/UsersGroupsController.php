<?php

namespace App\Controller\Parametres;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Parametres\UsersGroups\Users;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Parametres\UsersGroups\Groups;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Parametres\UsersGroups\UsersType;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Parametres\UsersGroups\Services;
use App\Form\Parametres\UsersGroups\GroupsType;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Parametres\UsersGroups\ServicesType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\Parametres\UsersGroups\UpdateUsersType;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Parametres\UsersGroups\UsersRepository;
use App\Repository\Parametres\UsersGroups\GroupsRepository;
use App\Repository\Parametres\UsersGroups\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/parametres/users_groups/users/creation", name="parametres_users_groups_creationUsers")
     * 
     */
    public function ajoutUsers(Users $users = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "utilisateurs"]);
        

        if(!$users){
            $breadcrumbs->addItem("Ajout Utilisateur");
            $users = new Users();
            $titre = "Création d'un utlisateur";
        }else
        {
            $breadcrumbs->addItem("Modification Utilisateur");
            $titre = "Modification d'un utilisateur";
        }

        $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        $form = $this->createForm(UsersType::class,$users);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $passwordCrypte = $encoder->encodePassword($users, $users->getPassword());
            $users->setPassword($passwordCrypte);
            $om->persist($users);
            $om->flush();
            $this->addFlash('success', "L'action a été effectué");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "utilisateurs"]);
        }

        return $this->render('parametres/users_groups/formUsers.html.twig',[
            "groupe" => $users,
            "form" => $form->createView(),
            "titre" => $titre
        ]);
    }
  /**
     * 
     * @Route("/parametres/users_groups/users/modification/{id}", name="parametres_users_groups_modifUsers", methods="GET|POST")
     */
    public function modificationUsers(Users $users = null, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "utilisateurs"]);
        

        $breadcrumbs->addItem("Modification Utilisateur");
        $titre = "Modification d'un utilisateur";
        $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        $form = $this->createForm(UpdateUsersType::class,$users);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //$passwordCrypte = $encoder->encodePassword($users, $users->getPassword());
            //$users->setPassword($passwordCrypte);
            $om->persist($users);
            $om->flush();
            $this->addFlash('success', "L'action a été effectué");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "utilisateurs"]);
        }

        return $this->render('parametres/users_groups/formUpdateUsers.html.twig',[
            "user" => $users,
            "form" => $form->createView(),
            "titre" => $titre
        ]);
    }

      /**
     * 
     * @Route("/parametres/users_groups/users/suppression/{id}", name="parametres_users_groups_delUsers")
     */
    public function suppressionUsers(Users $users, Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

            $fsObject = new Filesystem();
            $fsObject->remove('img/utilisateurs/'.$users->getPhoto());   
            $om->remove($users);
            $om->flush(); 
            $this->addFlash("success","La suppression a été effectuée");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "utilisateurs"]);
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

        //boucle des enregistrements trouvés
        foreach ($enregistrements as $enregistrement) {
            //initialiasation du sous tableau de données  à envoyer en Json
            $sub_array = array();

            //ajoute au tableau pour chaque ligne
            $sub_array[] = $enregistrement->getId();
            $sub_array[] = $enregistrement->getUsername();
            $sub_array[] = $enregistrement->getNom();
            $sub_array[] = $enregistrement->getPrenom();   
            $sub_array[] = '<a class="edit" title="Editer" data-toggle="tooltip" href="'.$this->generateUrl('parametres_users_groups_modifUsers', ['id' => $enregistrement->getId()]).'"><i class="far fa-edit"></i></a>
            <a class="delete" title="Supprimer" data-toggle="tooltip"  href="'.$this->generateUrl('parametres_users_groups_delUsers', ['id' => $enregistrement->getId()]).'" onclick="return confirm(\'Etes vous sûr de supprimer : '. $enregistrement->getUsername().'?\')" ><i class="far fa-trash-alt"></i></a>';
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

        //$group->removeUser($users);
        //$service->removeUser($users);
        $om->remove($group);
        $om->flush();
        $this->addFlash("success","La suppression a été effectuée");
        return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "groupes"]);
}



    /**
     * @Route("/parametres/users_groups/users/photo", name="photoUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getImage(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            //$user = new Users();
            //$form = $this->createForm(UsersType::class, $user);
            //$form->handleRequest($request);
            // the file
            $file = $_FILES['file'];
            $fileUpload = new UploadedFile($file['tmp_name'], $file['name'], $file['type']);

            //$nomFichierTmp = $file['tmp_name'].$file['name'].$file['type'];
           // $nomFichierTmp = 'test';
            $filename = $this->generateUniqueName() . '.' . $fileUpload->guessExtension();
            $fileUpload->move(
                'img/utilisateurs',
                $filename
            );
            //$user->setPhoto($filename);
            //$em = $this->getDoctrine()->getManager();
            //$em->persist($user);
            //$em->flush();
        }
        return new JsonResponse(array('filename' => $filename));
    }

    private function generateUniqueName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/parametres/users_groups/users/photo/supprimer", name="photoUserDel", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delImage(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $fsObject = new Filesystem();
            $fsObject->remove('img/utilisateurs/'.$request->get('file'));    

            return new JsonResponse(array('reponse' => 'ok'));
        }
        
    }

        /**
     * @Route("/parametres/users_groups/users/photo/supprimerUpdate/", name="photoUserUpdateDel", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delUpdateImage(Request $request,EntityManagerInterface $om, UsersRepository $repository)
    {
        if ($request->isXmlHttpRequest())
        {
            //recupere dans la base l'enregistrement
            $users = $repository->find(
                $request->get('id')
            );
            $fsObject = new Filesystem();
            $fsObject->remove('img/utilisateurs/'.$request->get('file'));    
            $form = $this->createForm(UpdateUsersType::class,$users);
            $users->setPhoto('');
            $om->persist($users);
            $om->flush();
            return new JsonResponse(array('reponse' => 'ok'));
        }
        
    } 
}

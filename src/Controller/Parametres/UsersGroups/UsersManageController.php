<?php

namespace App\Controller\Parametres\UsersGroups;


use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Parametres\UsersGroups\Users;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Parametres\UsersGroups\UsersType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\Parametres\UsersGroups\UpdateUsersType;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Parametres\UsersGroups\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersManageController extends AbstractController
{
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
     * @Route("/parametres/users_groups/users/photo", name="photoUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getImage(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $file = $_FILES['file'];
            $fileUpload = new UploadedFile($file['tmp_name'], $file['name'], $file['type']);

            $filename = $this->generateUniqueName() . '.' . $fileUpload->guessExtension();
            $fileUpload->move(
                'img/utilisateurs',
                $filename
            );
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
     /**
     * @Route("/parametres/users_groups/users/photo/changepwd/", name="changePWD", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request,EntityManagerInterface $om, UsersRepository $repository,UserPasswordEncoderInterface $encoder)
    {
        if ($request->isXmlHttpRequest())
        {
            //recupere dans la base l'enregistrement
            $users = $repository->find(
                $request->get('id')
            );
            $passwordCrypte = $encoder->encodePassword($users, $request->get('pwd'));
            $users->setPassword($passwordCrypte);
            $om->persist($users);
            $om->flush();
            return new JsonResponse(array('reponse' => 'ok'));
        }
        
    }  

}

<?php

namespace App\Controller\Parametres\UsersGroups;

use Symfony\Component\Ldap\Ldap;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Parametres\UsersGroups\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Parametres\UsersGroups\LdapUsersType;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\Ldap\Exception\ConnectionException;
use App\Repository\Parametres\UsersGroups\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class LdapUsersController extends AbstractController
{


 /**
     * @Route("/parametres/users_groups/users/import", name="parametres_users_groups_importAD")
     * 
     */
    public function importUsers(Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "utilisateurs"]);
        
        $breadcrumbs->addItem("Import Utilisateur");
        $titre = "Création d'un utlisateur";

        $breadcrumbs->prependRouteItem("Accueil", "dash_board");

        return $this->render('parametres/users_groups/ldapUsers.html.twig',[

        ]);
    }

    /**
     * @Route("/parametres/users_groups/users/import/ajout", name="parametres_users_groups_ajoutAD")
     * 
     */
    public function ajoutLdapUsers(Request $request, EntityManagerInterface $om, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $encoder){

        $breadcrumbs->addItem("Utilisateurs / Groupes", $this->get("router")->generate("parametres_users_groups"), ['selectTab' => "utilisateurs"]);
        

        $breadcrumbs->addItem("Ajout d'un utilisateur");
        $titre = "Ajout d'un utilisateur Active Directory";
        $breadcrumbs->prependRouteItem("Accueil", "dash_board");

        $users = new Users;
        $users->setNom($request->get('sn'));
        $users->setUsername($request->get('sAMAccountName'));
        $users->setPrenom($request->get('givenName'));
        $users->setEmail($request->get('mail'));


        $form = $this->createForm(LdapUsersType::class,$users);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $passwordCrypte = $encoder->encodePassword($users, random_bytes(10));
            $users->setPassword($passwordCrypte);
            $om->persist($users);
            $om->flush();
            $this->addFlash('success', "L'action a été effectué");
            return $this->redirectToRoute("parametres_users_groups", [ 'selectTab' => "utilisateurs"]);
        }

        return $this->render('parametres/users_groups/formLdapUsers.html.twig',[
            "user" => $users,
            "form" => $form->createView(),
            "titre" => $titre
        ]);
    }

     /**
     * @Route("/parametres/users_groups/users/ldap", name="table_ldap")
     */
    public function ldapTable(Request $request, UsersRepository $repository)
    {
        //initialise le tableau a envoyer
        $data = array();

        //recuperation des filtres
        $length = $request->get('length');
        $length = $length && ($length!=-1)?$length:0;
 
        $start = $request->get('start');
        $start = $length?($start && ($start!=-1)?$start:0)/$length:0;
 
        $search = $request->get('search');
        $recherche = $search["value"];
        
        $domain = '10.228.88.61';
        $username = 'CN=adbrowse,OU=COMPTES DE SERVICES,OU=Utilisateurs,DC=PVBRY,DC=com';
        $password = 'R5jbfsJhQj';
        $dc =  "OU=Utilisateurs,DC=PVBRY,DC=com";
        //$filter  ="(&(objectClass=user)(objectCategory=person))";
        $filter  ="(&(objectClass=user)(objectCategory=person)(|(sn=$recherche*)(givenname=$recherche*)))";

        $attr = array("sAMAccountName", "sn", "givenName", "mail");
        
        $ldap = Ldap::create('ext_ldap', array(
            'host' => $domain,
            'encryption' => 'none',
            'version' => 3,
            'debug' => true,
            'referrals' => false,
        )); 
        
        try {
            $ldap->bind($username, $password);
            $query = $ldap->query( $dc, $filter,[ 'filter' => $attr ]);
            //"attrsOnly", "deref", "filter", "maxItems", "pageSize", "scope", "sizeLimit", "timeout"
        } catch (ConnectionException $e) {
                throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username), 0, $e);
        }
    
        	
        $results = $query->execute()->toArray();

        foreach( $results as $entry ) {
            $sub_array = array();
//<a class="delete" title="Supprimer" data-toggle="tooltip"  href="'.$this->generateUrl('parametres_users_groups_delUsers', ['id' => $enregistrement->getId()]).'" onclick="return confirm(\'Etes vous sûr de supprimer : '. $enregistrement->getUsername().'?\')" ><i class="far fa-trash-alt"></i></a>
            if( isset( $entry->getAttribute('sAMAccountName')[0])  )
                $sAMAccountName = $entry->getAttribute('sAMAccountName')[0];
            else
                $sAMAccountName = "";

            if( isset( $entry->getAttribute('sn')[0])  )
                $sn = $entry->getAttribute('sn')[0];
            else
              $sn= "";

            if( isset( $entry->getAttribute('givenName')[0])  )
                $givenName = $entry->getAttribute('givenName')[0];
            else
              $givenName = "";

            if( isset( $entry->getAttribute('mail')[0])  )
                $mail = $entry->getAttribute('mail')[0];
            else
                $mail = "";

            $href = '<a href="'.$this->generateUrl('parametres_users_groups_ajoutAD', ['sAMAccountName' => $sAMAccountName , 'sn' => $sn , 'givenName' => $givenName , 'mail' => $mail  ]).'">';    
            $sub_array[] = $href.$sAMAccountName;    
            $sub_array[] = $sn;
            $sub_array[] = $givenName;
            $sub_array[] = $mail.'</a>';    
            $data[] = $sub_array;
          }

        //parametre datatable
        $output = array(
            "draw"    => intval( $request->get('draw')),
            "recordsTotal"  =>   0,
            "recordsFiltered" => 0,
            "data"    => $data,
            "erreur"    => 2       
           );
 
        //envoi en json a datatable   
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

}
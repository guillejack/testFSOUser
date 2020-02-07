<?php

namespace App\Controller;

use App\Entity\Parametres\UsersGroups\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="dash_board")
     */
    public function index(Breadcrumbs $breadcrumbs)
    {
       // $breadcrumbs->addItem("Accueil", $this->get("router")->generate("dash_board"));


       $user= $this->get('security.token_storage')->getToken()->getUser();

    // Add "homepage" route link at the start of the breadcrumbs
    $breadcrumbs->prependRouteItem("Accueil", "dash_board");


        return $this->render('dashboard/dashboard/index.html.twig', [
            'controller_name' => 'DashBoardController',
            'user' => $user
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
/*     public function indexAction( Request $request )
    {
        $arrViewData = array('USER_EMAIL' => NULL, 'PASSWORD' => NULL, 'ERROR' => NULL);
 
        return $this->render('securite/login.html.twig', $arrViewData);
    } */

          /**
     * @Route("/login", name="login")
     */
/*     public function login(AuthenticationUtils $util)
    {


        return $this->render('securite/login.html.twig',[

            "lastUserName" => $util->getLastUsername(),
            "error" => $util->getLastAuthenticationError()

        ]);

    } */

     /**
     * @Route("/login/verif", name="verif")
     */
    /* public function verifAction( Request $request , \App\Services\Utils\Ldap $objLdapServ )
    {
        $arrViewData = array('USER_EMAIL' => NULL, 'PASSWORD' => NULL, 'ERROR' => NULL);
 

         // Checks if the login form has been submitted
        if($request->getMethod() == 'POST')
        {
            echo "test";
              // load Ldap service
              ///$objLdapServ = $this->get('ldap');
              //$objLdapServ = $this->container->get('ldap');
   
              // check Ldap login
              $arrLoginResult = $objLdapServ->checkLdapLogin();
   
              // Ldap login result
              $arrViewData = json_decode($arrLoginResult, TRUE);
   
              // check Ldap login result
              if($arrViewData['LOGIN'] == "OK")
              {
                  // user logged ok, then we redirect to the home page
                 // $router = $this->get('router');
                  //$url = $router->generate('home');
   
                  //return $this->redirect($url);
                  return $this->redirectToRoute("app");
                  //return $this->render('login.html.twig', $arrViewData);
              }
        }
        return $this->redirectToRoute("login"); 
        //return $this->render('login.html.twig', $arrViewData);
    } */

     /**
     * @Route("/deconnexion", name="deconnexion")
     */
/*     public function deconnexion(\App\Services\User\UserManager $objUserServ)
    {
        $arrViewData = array('USER_EMAIL' => NULL, 'PASSWORD' => NULL, 'ERROR' => NULL);
        $objUserServ->logOutUser();

        //return $this->render('login/login.html.twig', $arrViewData);
        return $this->redirectToRoute("login");

    } */

     /**
     * @Route("/deconnexion", name="deconnexion")
     */
     public function deconnexion()
    {

        //return $this->render('login/login.html.twig', $arrViewData);
        return $this->redirectToRoute("login");

    } 
}

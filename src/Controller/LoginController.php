<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    
    /**
     * @Route("/login", name="login")
     */
    public function indexAction( Request $request )
    {
        $arrViewData = array('LOGIN' => NULL, 'USERNAME' => NULL, 'ERROR' => NULL);
        return $this->render('securite/login.html.twig', [
            'ERROR' => $arrViewData['ERROR'],
            'USERNAME' => $arrViewData['USERNAME'],
            ]);
    }

     /**
     * @Route("/login/verif", name="verif")
     */
    public function verifAction( Request $request , \App\Services\Utils\Ldap $objLdapServ )
    {
        $arrViewData = array('LOGIN' => NULL, 'USERNAME' => NULL, 'ERROR' => NULL);
         // Checks if the login form has been submitted
        if($request->getMethod() == 'POST')
        { 
              // check Ldap login
              $arrLoginResult = $objLdapServ->checkLdapLogin();
   
              // Ldap login result
              $arrViewData = json_decode($arrLoginResult, TRUE);
   
              // check Ldap login result
              if($arrViewData['LOGIN'] == "OK")
              {
                  return $this->redirectToRoute("dash_board");
              }
        }

       /* return $this->render('securite/login.html.twig', [
            'ERROR' => $arrViewData['ERROR'],
            'USERNAME' => $arrViewData['USERNAME'],
            ]);*/
    }

     /**
     * @Route("/deconnexion", name="deconnexion")
     */
    public function deconnexion(\App\Services\User\UserManager $objUserServ)
    {
        $objUserServ->logOutUser();

        return $this->redirectToRoute("login");

    }
}

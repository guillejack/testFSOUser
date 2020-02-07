<?php

use App\Entity\Parametres\UsersGroups\Users;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
 
      // src/Services/User/UserManager.php
 
      namespace App\Services\User;
 
      use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
      use Symfony\Component\DependencyInjection\ContainerInterface;
      use Symfony\Component\HttpFoundation\Session\SessionInterface;
      use Symfony\Component\HttpFoundation\Request;
      use Symfony\Component\HttpFoundation\Response;
      use Doctrine\ORM\EntityManager;
      use App\Entity\Parametres\UsersGroups\User;
      use Symfony\Component\HttpFoundation\RequestStack;
      use Doctrine\ORM\EntityManagerInterface;
      use Symfony\Component\Routing\RouterInterface;
      use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
      use Exception;
 
      class UserManager
      {
            private $container, $em, $request, $session, $user,$passwordCrypt,$users;
 
            public function __construct(EntityManagerInterface $em, ContainerInterface $container, RequestStack $request , SessionInterface $session, UserPasswordEncoderInterface $passwordCrypt, Users $users )
            {
                  //$entityManager = $this->getDoctrine()->getManager('enquete_conv');
                  $this->em = $em;
                  $this->request = $request;
                  $this->container = $container;
                  $this->session = $session;
                  $this->passwordCrypt = $passwordCrypt;
                  $this->users =$users ;

            }
 
            // checks if user exists when login form has been submitted
/*             public function loginAction($strUsername)
            {
                  if( ! $this->checkUserExists($strUsername) )
                  {
                        // create new user
                        //$this->createUser($strUsername);
                  }
 
                  $this->createLoginSession();
            } */
 
            // get user data from database
            public function getUser($strUsername)
            {
                  return $this->em->getRepository('App:Parametres\UsersGroups\Users')->findOneBy( array('username' => $strUsername) );
            }
 
            // Check if a user exists on database
            public function checkUserExists($strUsername)
            {
                  $this->user = $this->getUser($strUsername);
                  return ( ! empty($this->user)) ? true : false;
            }

            // Check if a user exists on database
            public function checkUserPasswordExists($strUsername, $strPassword)
            {
                  $this->user = $this->getUser($strUsername);
                  if  ( ! empty($this->user)) {
                        if ($this->passwordCrypt->isPasswordValid($this->user, $strPassword ))
                              return true;
                        else
                              return false;      
                  }
                  
                  
                  return ( ! empty($this->user)) ? true : false;
            } 
            // Create new user on database
/*              public function createUser($strUsername)
            {
                  $boolResult = false;
                  $objCurrentDatetime = new \Datetime();
 
                  try
                  {
                        $objUser = new User();
                        $objUser->setUser($strUsername);
                        $objUser->setCreationDate($objCurrentDatetime);
                        $objUser->setLastLoginDate($objCurrentDatetime);
 
                        // save data
                        $this->em->persist($objUser);
                        $this->em->flush();
 
                        // result data
                        $boolResult = true;
 
                        // user obj
                        $this->user = $objUser;
                  }
                  catch(Exception $ex)
                  {
                        echo $ex->getMessage();
                  }
 
                  return $boolResult;
            }  */
  
            // creates login session
            public function createLoginSession()
            {
                  $objToken = new UsernamePasswordToken($this->user->getUsername(), null, 'main',  $this->user->getRoles()) ;//$this->user->getRole() ['ROLE_USER'] 
 
                  // update user last login
                  $this->user->setLastLoginDate( new \Datetime() );
                  $this->em->persist($this->user);
                  $this->em->flush();
 
                  // save token
                  $objTokenStorage = $this->container->get("security.token_storage")->setToken($objToken);
                  $this->session->set('_security_main', serialize($objToken));
            }
 
            // logout a user
            public function logOutUser()
            {
                  $this->container->get('security.token_storage')->setToken(null);
                  $this->session->invalidate();
 
                 // $url = $router->generate('oportunidades');
                 //   return $this->redirect($url);
            }
 
      }
<?php    
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
 
            public function __construct(EntityManagerInterface $em, ContainerInterface $container, RequestStack $request , SessionInterface $session, UserPasswordEncoderInterface $passwordCrypt )
            {
                  $this->em = $em;
                  $this->request = $request;
                  $this->container = $container;
                  $this->session = $session;
                  $this->passwordCrypt = $passwordCrypt;

            }
 

            // get user data from database
            public function getUserDatabase($strUsername)
            {
                  return $this->em->getRepository('App:Parametres\UsersGroups\Users')->findOneBy( array('username' => $strUsername) );
            }
 
            // Check if a user exists on database
            public function checkUserExists($strUsername)
            {
                  $this->user = $this->getUserDatabase($strUsername);
                  return ( ! empty($this->user)) ? true : false;
            }

            // Check if a user exists on database
            public function checkUserPasswordExists($strUsername, $strPassword)
            {
                  $this->user = $this->getUserDatabase($strUsername);
                  if  ( ! empty($this->user)) {
                        if ($this->passwordCrypt->isPasswordValid($this->user, $strPassword ))
                              return true;
                        else
                              return false;      
                  }
                  
                  
                  return false;
            } 

  
            // creates login session
            public function createLoginSession()
            {
                  $objToken = new UsernamePasswordToken($this->user, null, 'db_provider',  $this->user->getRoles()) ;//$this->user->getRole() ['ROLE_USER'] 

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
                  $this->session->clear();
                  $this->session->invalidate();

            }
 
      }
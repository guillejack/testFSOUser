<?php
       
      // src/Services/Utils/Ldap.php
       
      namespace App\Services\Utils;
 
      use Symfony\Component\DependencyInjection\ContainerInterface;
      use Symfony\Component\HttpFoundation\Request;
      use Symfony\Component\HttpFoundation\Response;
      use Symfony\Component\HttpFoundation\RequestStack;
 
      class Ldap
      {
 
          private $em, $request, $container;
          private $strLdapServer, $strLdapDN;
          private $objLdapBind, $strLdapFilter, $strLdapDC, $objLdapConnection;
          private $arrLoginResult, $strUserEmail, $strUserPasswd , $objUserServ;
          protected $requestStack;
 
          public function __construct(ContainerInterface $container, RequestStack $request,\App\Services\User\UserManager $objUserServ)
          {
              $this->request            = $request;
              $this->container    = $container;
              $this->objUserServ = $objUserServ;
 
              // LDAP CONFIG
              $this->strLdapServer     = "10.228.88.61";
              $this->strLdapDN    = "PVBRY";
              $this->strLdapDC    = "OU=Utilisateurs,DC=PVBRY,DC=com";
 
              // init vars
              $this->objLdapBind        = false;
              $this->objLdapConnection = false;
          }
 
          // Load LDAP config
          private function loadLdapConfig()
          {
            $this->strLdapFilter = "(sAMAccountName=" . $this->strUserEmail . ")";
            $this->strLdapServer = "ldap://" . $this->strLdapServer;
            $this->strLdapDN     = $this->strLdapDN . "\\" . $this->strUserEmail;
          }
 
          // Connects to LDAP server
          private function connectToLdapServer()
          {
            $this->objLdapConnection = ldap_connect($this->strLdapServer);
            ldap_set_option($this->objLdapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->objLdapConnection, LDAP_OPT_REFERRALS, 0);
            $this->objLdapBind = @ldap_bind($this->objLdapConnection, $this->strLdapDN, $this->strUserPasswd);
          }
 
          // Get username and password form login form
          private function getLdapUsernameAndPassword()
          {

            $this->strUserEmail  = $this->request->getCurrentRequest()->get('_username');
            $this->strUserPasswd = $this->request->getCurrentRequest()->get('_password');
 
            if( ! empty($this->strUserEmail) && ! empty($this->strUserPasswd))
            {
                  $this->arrLoginResult['USER_EMAIL'] = $this->strUserEmail;
                  $this->arrLoginResult['PASSWORD']      = $this->strUserPasswd;
 
                  // get only username, deleting all data after @
                  if(preg_match('/@/', $this->strUserEmail))
                  {
                        $arrUserData = explode("@", $this->strUserEmail);
                        $this->strUserEmail = $arrUserData[0];
                  }
            }
            else
            {
                  $this->arrLoginResult['ERROR'] = "EMPTY_CREDENTIALS";
            }
          }
 
          // check ldap login with username and password
          public function checkLdapLogin()
          {
            $this->arrLoginResult = array(
                              'LOGIN'      => 'ERROR', 
                              'ERROR'      => 'INIT',
                              'USER_EMAIL' => NULL,
                              'PASSWORD'   => NULL,
                              'USERNAME'   => NULL
                        );
 
            // get username and password
            $this->getLdapUsernameAndPassword();
 
            if( ! empty($this->strUserEmail) && ! empty($this->strUserPasswd) )
            {
                  // load LDAP config
                  $this->loadLdapConfig();
 
                  // connect to server
                  $this->connectToLdapServer();
 
                  // check connection result 
                  if($this->objLdapBind)
                  {
                        if ($this->objUserServ->checkUserExists($this->strUserEmail)){
                              // login ok
                              $this->arrLoginResult['LOGIN'] = "OK";
      
                              // get ldap response
                              $result = ldap_search($this->objLdapConnection, $this->strLdapDC, $this->strLdapFilter);
      
                              // sort ldap results
                              ldap_sort($this->objLdapConnection, $result, "sn");
      
                              // get user info
                              $info = ldap_get_entries($this->objLdapConnection, $result);
      
                              // get user info
                              $this->arrLoginResult['USERNAME'] = $this->strUserEmail;
      
                              // close ldap connection
                              @ldap_close($this->objLdapConnection);
      
                              // login user
                              $this->objUserServ->createLoginSession();
                        } 
                        else {
                              $this->arrLoginResult['USERNAME'] = $this->strUserEmail;      
                              $this->arrLoginResult['ERROR'] = 'INVALID_CREDENTIALS_DATABASE';
                        }

                  }
                  else if ($this->objUserServ->checkUserPasswordExists($this->strUserEmail,$this->strUserPasswd))
                  {
                        $this->objUserServ->createLoginSession();
                        // login ok
                        $this->arrLoginResult['LOGIN'] = "OK";
                  }
                  else {
                        $this->arrLoginResult['USERNAME'] = $this->strUserEmail;
                        $this->arrLoginResult['ERROR'] = 'INVALID_CREDENTIALS_LDAP';
                  }

            }
            return json_encode($this->arrLoginResult);
          }
      }
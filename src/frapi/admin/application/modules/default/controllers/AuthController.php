<?php
class AuthController extends Lupin_Controller_Base
{
    public function init()
    {
        $this->_helper->_acl->allow(null);
        parent::init();
        $this->initView();
    }

    /**
     * Does nothing, intended so the base dispatch does not
     * try to log you out
     */
    public function preDispatch()
    {
    }

    public function indexAction()
    {
        $this->_forward('login');
    }

    public function noaccessAction()
    {
        if ($this->view->user === null) {
            $this->_forward('login');
        }
    }

    public function loginAction()
    {
        // Don't allow logged in people here
        $user = Zend_Auth::getInstance()->getIdentity();
        if ($user !== null) {
            $this->redirect('/');
        }

        $this->view->title = 'Log in';
        if ($this->_request->isPost()) {
            // collect the data from the user
            $f = new Zend_Filter_StripTags();
            $username = $f->filter($this->_request->getPost('handle'));
            $password = $f->filter($this->_request->getPost('password'));

            if (empty($username) || empty($password)) {
                $this->addErrorMessage('Please provide a username and password.');
            } else {
                // do the authentication
                $authAdapter = $this->_getAuthAdapter($username, $password);
                $auth   = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                
                if ($result->isValid()) {
                    $auth->getStorage()->write($authAdapter->getResult());

                    // Receive Zend_Session_Namespace object
                    $session = new Zend_Session_Namespace('Zend_Auth');
                    // Set the time of user logged in
                    $session->setExpirationSeconds(24*3600);

                    // If "remember" was marked
                    if ($this->getRequest()->getParam('rememberme') !== null) {
                        // remember the session for 604800s = 7 days
                        Zend_Session::rememberMe(604800);
                    }

                    $ns = new Zend_Session_Namespace('lastUrl');
                    $lastUrl = $ns->value;
                    if ($lastUrl !== '') {
                        $ns->value = '';
                        $this->_redirect($lastUrl);
                    }

                    $this->_redirect('/');
                } else {
                    // failure: clear database row from session
                    $this->addErrorMessage('Login failed.');
                }
            }
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
        }
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::forgetMe();
        $this->_redirect('/');
    }

    protected function _getAuthAdapter($handle, $password)
    {
        // setup Zend_Auth adapter for a database table
        $authAdapter = new Frapi_Authorization_Adapter_Xml();

        // Set the input credential values to authenticate against
        $authAdapter->setIdentity($handle);
        $authAdapter->setCredential(sha1($password));
        return $authAdapter;
    }
}
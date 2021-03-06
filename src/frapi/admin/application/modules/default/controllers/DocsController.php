<?php
class DocsController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index', 'generate');
        $this->_helper->_acl->allow('admin', $actions);
        
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        
        if (!$contextSwitch->hasContext('text')) {
            $contextSwitch->addContext(
                'text', array(
                    'suffix'  => 'txt', 
                    'headers' => array('Content-Type'=>'text/plain')
                )
            );
            $contextSwitch->addContext(
                'mdown', array(
                    'suffix'  => 'mdown', 
                    'headers' => array('Content-Type'=>'text/plain')
                )
            );
            $contextSwitch->addContext(
                'html', array(
                    'suffix'  => 'html', 
                    'headers' => array('Content-Type'=>'text/html')
                )
            );
            
            $contextSwitch->addContext(
                'pdf', array(
                    'suffix'  => 'pdf', 
                    'headers' => array(
                        'Content-Type'=>'application/pdf',
                        'Content-Disposition' => 'Attachment; filename="api_docs-'.@date('Y-m-d').'.pdf"',
                    )
                )
            );
        }
        
        $contextSwitch->addActionContext('generate', 'text')->initContext();
        $contextSwitch->addActionContext('generate', 'html')->initContext();
        $contextSwitch->addActionContext('generate', 'pdf')->initContext();
        $contextSwitch->addActionContext('generate', 'mdown')->initContext();
        
        parent::init();
    }
    
    /**
     * Index, used to display format options and
     * basic outline.
     *
     * @return void
     **/
    public function indexAction()
    {
        $this->_forward('generate');
    }

    /**
     * Generate documentation, in available formats.
     *
     * @return void
     **/
    public function generateAction()
    {
        $doc_data = array();
        $emod  = new Default_Model_Error;
        $amod  = new Default_Model_Action;
        $omod  = new Default_Model_Output;
        
        $doc_data['actions']      = $amod->getAll();
        $doc_data['output-types'] = $omod->getAll();
        $doc_data['errors']       = $emod->getAll();

        $this->view->doc_data     = $doc_data;
    }
}
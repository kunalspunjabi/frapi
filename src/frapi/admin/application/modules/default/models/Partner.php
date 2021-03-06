<?php

class Default_Model_Partner extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('partners');
    }
    
    public function add(array $data)
    {
        // Might put back the while to insure the singularity of this key but
        // for now I'll trust odds that I can't control... hrm.
        $apiKey = $this->generateAPIKey();

        $whitelist = array('firstname', 'lastname', 'email', 'company');
        $this->whiteList($whitelist, $data);


        $values = array(
            'firstname' => $data['firstname'], 
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],     
            'company'   => $data['company'],
            'api_key'    => $apiKey
        );

        try {
            $res = $this->config->add('partner', $values);
        } catch (Exception $e) { 

        }
        
        var_dump($res); die();
        $this->refreshAPCCache();
        return true;
    }

    public function edit(array $data, $id)
    {
        $whitelist = array('firstname', 'lastname', 'email', 'company');
        $this->whiteList($whitelist, $data);

        $values = array(
            'email'     => $data['email'],   
            'company'   => $data['company'],  
            'lastname'  => $data['lastname'],
            'firstname' => $data['firstname'], 
        );

        try {
            $this->config->update('partner', 'hash', $id, $values);
        } catch (Exception $e) { }
        
        $this->refreshAPCCache();
        return true;
    }

    public function updateAPIKey($id)
    {
        $apiKey = $this->generateAPIKey();

        try {
            $this->config->update('partner', 'hash', $id, array('api_key' => $apiKey));
        } catch (Exception $e) { }
        $this->refreshAPCCache();
    }

    public function delete($id)
    {
        $this->config->deleteByField('partner', 'hash', $id);
        $this->refreshAPCCache();
    }

    public function get($id)
    {
        $partner = $this->config->getByField('partner', 'hash', $id);
        return isset($partner) ? $partner : false;
    }

    public function generateAPIKey()
    {
        return hash('sha1', uniqid(mt_rand(1, 1000000000), true));
    }

    public function getAll()
    {
        $partners = $this->config->getAll('partner');
        return $partners;
    }
    
    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     **/
    public function refreshAPCCache()
    {
        apc_delete('Partners.emails-keys');
    }
}
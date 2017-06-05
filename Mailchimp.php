<?php

/**
 * Mail Chimp API v3
 * Author: Anil Prajapati <anilprz3@gmail.com>
 * version: 1.0
**/

namespace Anlprz\MailChimp;

Class Mailchimp { 
    
    // Mailchimp API Key
    // Locate API key: User menu > Account > Extras > API Key > Generate or copy API key
    private $apikey = null;

    // List ID to add Subscriber
    // Locate List ID: Lists menu > Choose desired List > Settings > List name and defaults
    private $listId = null;

    // Server ID
    // Locate Server ID: first string of the url before DOT > https://us19.admin.mailchimp.com/lists/
    private $serverId = null;
    
    // MERGE FIELDS
    // Locate Merge Feilds: Lists menu > Choose desired List > Settings > List fields and *|MERGE|* tags
    // In your List of Fields Table: This "Put this tag in your content:" Column will have Field key names 
    private $mergeFields = array();
    
    /**
     * New instance of Mailchimp with Api key, Server Id (optional) and List Id (optional)
     * @param string $apiKey
     * @param string $serverId
     * @param string $listId
     * @throws Exception
     * @return void
     */
    public function __construct( $apiKey, $serverId, $listId )
    {
        if( empty( $apiKey ) )
        {
            throw new Exception( 'Mailchimp API module cannot find your API key by itself, please provide' );
        }
        $this->apikey = $apiKey;

        if( !empty( $serverId ) ) $this->serverId = $serverId;

        if( !empty( $listId ) ) $this->listId = $listId;
    }
    
    /**
     * Get API key
     * @return string|null
     */
    public function getApikey()
    {
        return $this->apikey;
    }
    
    /**
     * Set List Id
     * @param string $listId
     */
    public function setListId ( $listId )
    {
        $this->listId = $listId;
    }
    
    /**
     * Get List Id
     * @return string|null
     */
    public function getListId ()
    {
        return $this->listId;
    }
    
    /**
     * Set Server Id
     * @param string $serverId
     */
    public function setServerId( $serverId )
    {
        $this->serverId = $serverId;
    }
    
    /**
     * Get Server Id
     * @return string|null
     */
    public function getServerId ()
    {
        return $this->serverId;
    }
    
    /**
     * Set Extra fields of Mailchimp variables i.e. Merge Fields
     * Usage 1: setMergeFields( array( 'FNAME' => 'Megan', 'AGE' => '16' ) );
     * Usage 2: setMergeFields( 'FNAME', 'Megan' );
     * Usage 3: (chain method) setMergeFields( 'FNAME', 'Megan' )->setMergeFields( 'LNAME', 'Fox' );
     * @param array $field
     * @throws Exception
     * @return object
     */
    public function setMergeFields ( $field, $value = false )
    {
        if ( empty( $field ) ) throw new Exception( 'Please set value to begin.' );
        
        if ( is_array( $field ) )
        {
            foreach ( $field as $k => $v) {
                $this->mergeFields[ $k ] = $v;
            }
        } 
        elseif ( is_string( $field ) && !empty( $value ) )
        {
            $this->mergeFields[ $field ] = $value;
        } else 
        {
            throw new Exception( 'Syntax Error while saving your Merge Fields.' );
        }
        
        return $this;
    }
    
    public function subscribe( $email )
    {
        if ( empty( $this->mergeFields['NAME'] ) ) throw new Exception( 'Merge Field NAME is required by default.' );
        
        if( empty( $this->listId ) ) throw new Exception( 'Please define List ID before subscribing.' );

        if( empty( $this->serverId ) ) throw new Exception( 'Please defeine Server ID before subscribing.' );
        
        $data = array(
                'apikey'        => $this->getApikey(),
                'email_address' => $email,
                'status'        => 'subscribed',
                'merge_fields'  => $this->mergeFields,
            );
        
        $json_data = json_encode($data);

        return $this->request( 'https://'.$this->getServerId() .'.api.mailchimp.com/3.0/lists/'.$this->getListId().'/members/', $json_data, 10, false  );
    }
    
    public function request( $url = '', $postFields = array(), $timeout = 10, $ssl = true )
    {
        $auth = base64_encode( 'user:'.$this->getApikey() );
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://'.$this->getServerId() .'.api.mailchimp.com/3.0/lists/'.$this->getListId().'/members/');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Authorization: Basic '.$auth));
            curl_setopt($ch, CURLOPT_USERAGENT, 'MCHIMP-ANLPRZ/3.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            return curl_exec($ch);
        } catch ( Exception $e ) {
            return $e->getMessage();
        }
    }

}

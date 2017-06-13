<?php

namespace App\Http\Controllers;


use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;



class GuzzleHttpController extends Controller
 {
    

	public function index()
    {
        $client = new Client(); //GuzzleHttp\Client 
       
        $response = $client->request('POST', 'http://203.64.37.41/API/TeamService.ashx?ask=postMessage', [
            'form_params' => [
                'account' => 'va_1b279737c4be426f82',
                'api_key' => '8565dd4d-a82d-4b8d-baa4-024a4c4997c3',
                'team_sn' => '1007',
                'content_type' => 1,
                'text_content' => '攝氏攝氏攝氏攝氏'
            ]
        ]);
        $body = $response->getBody();
        dd(json_decode($body)->IsSuccess);
        
    }

    
   
   
    
	
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

	protected $name;

	public function __construct($name = null){

		$this->name = $name;

	}

    public static function eventsSource()
    {
    	$json = file_get_contents(getcwd().'\\js\\clientFiles\\eventSources.json');
    	return $json;
    }

    public function getEvents()
    {
    	if($json = file_get_contents(getcwd().'\\js\\clientFiles\\'.strtolower($this->name).'.json'))
        {
    		return $json;
    	}

    	return false;
    }

    public function putEvents($data)
    {
    	if($json = file_put_contents(getcwd().'\\js\\clientFiles\\'.strtolower($this->name).'.json', $data))
        {
            return $json;
        }

        return false;
    }

    public function store()
    {
    	$this->name = request('name');
    	fopen(getcwd().'\\js/clientFiles\\'.strtolower($this->name).'.json', 'w');

    	if(file_exists(getcwd().'\\js\\clientFiles\\'.strtolower($this->name).'.json')){

    		$json = json_decode($this->eventsSource(), true);
	    	$count = count($json['eventSources']);
	    	$json['eventSources'][$count] = [
	    		'clientName' => Request('name'),
	    		'address' => request('address'),
	    		'url' => 'js/clientFiles/'.strtolower($this->name).'.json',
	    		'color' => request('backgroundcolor'),
	    		'textColor' => request('textcolor')
	    	];

	    	file_put_contents(getcwd().'\\js\\clientFiles\\eventSources.json', json_encode($json, JSON_PRETTY_PRINT));
    	}
    	
    }


}

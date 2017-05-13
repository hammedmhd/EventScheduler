<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;

class ClientController extends Controller
{
    public function index()
    {
    	return view('index');
    }

    public function eventsRender(Client $client)
    {
        //function works as static or public as its only called upon once on index load for rendering events thru ajax.
    	$json = $client->eventsSource();
    	return $json;
    }
    

    public function store(Client $client)
    {
    	$this->validate(request(), [

    		'name' => 'required|min:3'

    	]);
    	
        $client->name = request('name');
        $json = json_decode($client->eventsSource());

        foreach($json->eventSources as $event){
            if(strtoupper($event->clientName) == strtoupper($client->name)){
                return back()->withErrors([
                    'message' => 'Sorry, looks like the client is active already.'
                ]);
            }
        }

    	$client->store();
        session()->flash('message', 'Client added successfully!');

        return redirect('/');
    }

    public function delete(Client $client)
    {

        $client->name = request('name');
        $json = json_decode($client->eventsSource(), true);

        if(file_exists(getcwd().'\\js\\clientFiles\\'.strtolower($client->name).'.json')){
            if(unlink(getcwd().'\\js\\clientFiles\\'.strtolower($client->name).'.json')){

                foreach($json['eventSources'] as $id => $clients){
                    if(strtoupper($clients['clientName']) == strtoupper($client->name)){
                        unset($json['eventSources'][$id]);
                    }
                }

                $json['eventSources'] = array_values($json['eventSources']);
                file_put_contents(getcwd().'\\js\\clientFiles\\eventSources.json', json_encode($json, JSON_PRETTY_PRINT));
                session()->flash('message', 'Client Removed Successfully');

            }else{
                session()->flash('message', 'Woops.. something didnt work, please try again');
                return redirect('/');
            }
        }else{
            session()->flash('message', 'Woops.. something didnt work, please try again');
            return redirect('/');
        }

        return redirect('/');
    }

    public function update(Client $client)
    {
        //return request('obj');
        $client->name = request('obj')['title'];
        $json = json_decode($client->eventsSource(), true);

        foreach($json['eventSources'] as $id => $person)
        {
            if($person['clientName'] == $client->name)
            {
                if(isset(request('obj')['backgroundColor']))
                {
                    $json['eventSources'][$id]['color'] = request('obj')['backgroundColor'];
                }
                if(isset(request('obj')['textColor']))
                {
                    $json['eventSources'][$id]['textColor'] = request('obj')['textColor'];
                }
                //add address if needed.
            }
        }

      if(file_put_contents(getcwd().'\\js\\clientFiles\\eventSources.json', json_encode($json, JSON_PRETTY_PRINT))) return 'Client Updated';
    }
}

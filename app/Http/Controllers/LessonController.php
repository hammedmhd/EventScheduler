<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Lesson;

class LessonController extends Controller
{

	public function updatePayment()
	{
		$obj = request('obj');
		$client = new Client($obj['title']);
		$json = json_decode($client->getEvents(), true);
		foreach($json as $id => $event)
		{
			if($event['start'] == $obj['start'])
			{
				//return ["old" => $event['paid'], "new" => $obj['paid']];
				$json[$id]['paid'] = $obj['paid'];
			}
		}
		$json = json_encode($json, JSON_PRETTY_PRINT);
		$client->putEvents($json);

	}
	
	public function store(Lesson $lesson)
	{
		if(!$lesson->hasClash(request('start'), request('end'))){
			$obj = request()->all();
			$client = new Client($obj['title']);
			$file = $client->getEvents();
			$json = json_decode($file, true);
			$count = count($json);

			$json[$count] = $obj;
			$json = json_encode($json, JSON_PRETTY_PRINT);
			if($client->putEvents($json)){
				//session()->flash('message','Lesson added.');
				return 'Lesson Added';
			}
		//return 'Unable to process client, please check client name and retry';

		}else{
			return 'Looks like this slot is occupied, try another!';
		}
	}

	public function update(Lesson $lesson)
	{
		if(!request('resize')){
			$obj = request('new');
			$oldobj = request('old');
			$client = new Client($obj['title']);
			$file = $client->getEvents();
			$json = json_decode($file, true);

			if(!$lesson->hasClash($obj['start'], $obj['end'])){
				if($json > 0){
					foreach($json as $id => $event){
						if($event['start'] == $oldobj['start']){
							$json[$id] = $obj;
							$client->putEvents(json_encode($json, JSON_PRETTY_PRINT));
							return 'Lesson date updated!';
						}
					}
				}
			}else{
				return 'Looks like this slot is occupied, try another!';
			}

			return 'Failed to update lesson, if event is an all day event, you can only move it within the all day section.';
		}else{
			$obj = request('resize');
			$client = new Client($obj['title']);
			$json = json_decode($client->getEvents(), true);

			foreach($json as $id => $event){
				if($event['start'] == $obj['start']){
					$json[$id] = $obj;
					$client->putEvents(json_encode($json, JSON_PRETTY_PRINT));
					return 'Lesson timing updated!'; 
				}
			}
		}
	}

	public function delete()
	{// seems like protected properties can be manipulated from model and controller, more than other areas. unless new Model();....
		$obj = request()->all();
		$client = new Client(strtolower($obj['title']));
		$json = json_decode($client->getEvents(), true);

		foreach($json as $id => $event){
			if($event['start'] == $obj['start'] && $event['end'] == $obj['end']){
				unset($json[$id]);
				$json = array_values($json);
				$client->putEvents(json_encode($json, JSON_PRETTY_PRINT));
				return 'Lesson Removed Successfully!';
			}
		}

		return "Unable to remove Lesson, check client's name and retry!";
	}

}

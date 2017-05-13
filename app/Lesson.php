<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    public function hasClash($start, $end = null)
	{
		$client = new Client();
		$json = json_decode($client->eventsSource(), true);
		$names = [];
		$file = [];
		if($json['eventSources'] > 0){ // this if condition can actually b removed as a tmep file should always b in place for app.
			foreach($json['eventSources'] as $events){
				array_push($names, strtolower($events['clientName']));
			}
		}

		foreach($names as $id => $name){
			$client = new Client($name);
			$file[$id] = json_decode($client->getEvents(), true);
		}

		if($file > 0){
			foreach($file as $events){
				if($events > 0){
					foreach($events as $list){
						if($list['start'] == $start){// || (($list['end'] !== null) && ($list['end'] == $end))){
							return true;
						}
					}
				}
			}
		}

		return false;
	}
}

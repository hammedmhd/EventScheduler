<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expenses;
use App\Client;

class ExpensesController extends Controller
{

    public function viewExpenses(Expenses $expenses)
    {
    	$finaljson = [];
		$dateRange = $expenses->dateRange(request('obj'));
		$clients = json_decode(Client::eventsSource(), true);

		if($clients['eventSources'] > 0)
		{
			foreach($clients['eventSources'] as $client)
			{
				$clientModel = new Client($client['clientName']);
				$json = json_decode($clientModel->getEvents(), true);
				if($json > 0)
				{
					foreach($json as $event)
					{
						foreach($dateRange as $date)
						{
							if(preg_match('['.$date.']', $event['start']))
							{
								array_push($finaljson, $event);
							}
						}
					}
				}else{ continue; }
			}
		}else{  }

		$expenses = $expenses->getAllExpenses($finaljson);
		return ["expenses" => $expenses, "clients" => $finaljson];
    }
    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    //function to getExpenses($currentViewDate);
	protected $price = ['1hr' => 200, '30min' => 200/2];
	protected $petrol;
	protected $dateRange = ["01" => "31", "02" => "28", "03" => "31", "04" => "30", "05" => "31", "06" => "30", "07" => "31", "08" => "31", "09" => "30", "10" => "31", "11" => "30", "12" => "31"];

	//this function does not recognise the view range of which to calculate for, add to it or via controller to be handled
	public function getAllExpenses($json)
	{
		$total = $paid = $unpaid = 0;
		if($json > 0)
		{
			foreach($json as $event)
			{
				$hours = $this->getHours($event['start'], $event['end']);
				if($hours == 'All day') continue;

				$price = $this->getQuotation($hours);
				if($event['paid'] == 'true') $paid += $price;
				$total += $price;
			}
		}
		
		$unpaid = $total - $paid;
		$obj = [
			"total" => $total,
			"paid" => $paid,
			"unpaid" => $unpaid
		];
		return $obj;
	}

	public function dateRange($obj)
	{//returns array of current view range 
		$start = explode('-', $obj['start']);
		$end = explode('-', $obj['end']);
		$end[2] = $end[2] - 1;
		//$view = $end[1] - $start[1];
		$finalobj = [
			"yearStart" => $start[0],
			"yearEnd" => $end[0],
			"monthStart" => $start[1],
			"monthEnd" => $end[1],
			"viewStart" => '',
			"viewEnd" => '',
			"iteration" => ''
		];

		if($start[1] == $end[1]){
			switch($obj['name']){
				case 'agendaWeek':
				case 'listWeek':
					$finalobj['viewStart'] = $viewStart = $start[2];
					$viewEnd = $viewStart + 6;
					$finalobj['viewEnd'] = (string) $viewEnd;
					$finalobj['iteration'] = 7;
					break;

				case 'agendaDay':
					$finalobj['viewStart'] = $start[2];
					$finalobj['viewEnd'] = $start[2];
					$finalobj['iteration'] = 1;
					break;
			}
		}else{
			switch($obj['name']){
				case 'month':
					$finalobj['viewStart'] = $viewStart = $start[2];
					$finalobj['monthDays'] = $monthEndDate = $this->dateRange[$start[1]];
					$finalobj['iteration'] = 1 + ($monthEndDate - $viewStart);
					$finalobj['viewEnd'] = (string) $viewEnd = ((int) $viewStart + (int) $monthEndDate) - 1;
					break;

				case 'agendaWeek':
				case 'listWeek':
					$finalobj['monthDays'] = $monthEndDate = $this->dateRange[$finalobj['monthStart']];
					$finalobj['viewStart'] = $viewStart = $start[2];
					$finalobj['viewEnd'] = (string) $end[2];
					$finalobj['remaining'] = $remaining = 6 - ((int) $monthEndDate - (int) $viewStart);
					$finalobj['currentIter'] = 1 + ((int) $monthEndDate - (int) $viewStart); //+1 for current day;
					$finalobj['iteration'] = 7;
					break;

				case 'agendaDay':
					$finalobj['viewStart'] = $start[2];
					$finalobj['viewEnd'] = $start[2];
					$finalobj['iteration'] = 1;
					break;
			}
		}

		switch($finalobj['iteration']){
			case 1:
				$result = implode('-', [$finalobj['yearStart'], $finalobj['monthStart'], $finalobj['viewStart']]);
				return [$result];
				break;

			case 7:
				$currentResult = [];
				if(isset($finalobj['remaining'])){
					$yearStart = $finalobj['yearStart']; $yearEnd = $finalobj['yearEnd'];
					$monthStart = $finalobj['monthStart']; $monthEnd = $finalobj['remaining'] > 0 ? $finalobj['monthEnd'] : $finalobj['monthStart'];
					$integerViewStart = (int) $finalobj['viewStart'];
					$limit = (int) $finalobj['monthDays'];
					
					for($integerViewStart; $integerViewStart <= $limit; $integerViewStart++)
					{
						if(strlen($integerViewStart) < 2) $viewStart = '0'.(string) $integerViewStart; 
						else $viewStart = (string) $integerViewStart;
						
						$date = implode('-', [$finalobj['yearStart'], $monthStart, $viewStart]);
						array_push($currentResult, $date);
					}
					if($finalobj['remaining'] > 0)
					{
						$remainingResult = [];
						for($i = 1; $i <= $finalobj['remaining']; $i++)
						{
							$viewStart = '0'. (string) $i;
							$date = implode('-', [$finalobj['yearEnd'], $monthEnd, $viewStart]);
							array_push($remainingResult, $date);
						}
						$currentResult = array_merge($currentResult, $remainingResult);
					}
				}else{
					$integerViewStart = (int) $finalobj['viewStart'];$integerViewEnd = (int) $finalobj['viewEnd'];
					for($integerViewStart; $integerViewStart <= $integerViewEnd; $integerViewStart++)
					{
						if(strlen($integerViewStart) < 2) $viewStart = '0'.(string) $integerViewStart;
						else $viewStart = (string) $integerViewStart;

						$date = implode('-', [$finalobj['yearStart'], $finalobj['monthStart'], $viewStart]);
						array_push($currentResult, $date);
					}
				}
				return $currentResult;
				break;

			default: 
				$result = [];
				$integerViewStart = (int) $finalobj['viewStart'];$integerViewEnd = (int) $finalobj['viewEnd'];
				for($integerViewStart; $integerViewStart <= $integerViewEnd; $integerViewStart++)
				{
					if(strlen($integerViewStart) < 2) $viewStart = '0'.(string) $integerViewStart;
					else $viewStart = (string) $integerViewStart;

					$date = implode('-', [$finalobj['yearStart'], $finalobj['monthStart'], $viewStart]);
					array_push($result, $date);
				}
				return $result;
				break;
		}
	}


    public function getQuotation($obj)
    {

    	$realmin = $obj['min'] == 30 ? 1 : 0;
    	$hr = $obj['hr'] * $this->price['1hr'];
    	$min = $realmin * $this->price['30min'];
    	$quotation = $hr + $min;

    	return $quotation;
    }

	public function getDays($start, $end = null)
	{
		if(!strpos($start, 'T')) return 'All day';

		$startdate = explode('T', $start)[0];
		$enddate = $end == null ? 'same day' : explode('T', $end)[0];
		if($enddate == $startdate || $enddate == 'same day') return 0;

		$sdate = explode('-', $startdate);
		$edate = explode('-', $enddate);
		$yr = $edate[0] - $sdate[0];
		$m = $edate[1] - $sdate[1];
		$d = $edate[2] - $sdate[2];
		$fobj = [
			'yr' => $yr,
			'm' => $m,
			'd' => $d
		];

		return $fobj;
	}

    public function getHours($start, $end = null)
    {
    	$date = $this->getDays($start, $end);
    	if(strval($date) == 'All day') return 'All day';

    	$starttime = explode('T', $start)[1];
    	$endtime = $end == null ? 1 : explode('T', $end)[1];

    	if($endtime == 1) return ['hr' => 1, 'min' => 0];

    	$stime = explode(':', $starttime);
    	$etime = explode(':', $endtime);

    	$hr = (int) $etime[0] - (int) $stime[0];
    	$min = (int) $etime[1] - (int) $stime[1];

    	if(preg_match("/^[-]/", $min) == true){ $min = abs($min); $hr = $hr - 1;}

    	$fobj = [
    		'hr' => $hr,
    		'min' => $min
    	];

    	if($date['d'] > 0){
    		$extradays = $date['d'] * 24;
    		$fobj['hr'] += $extradays;
    	}

    	return $fobj;
    }
}

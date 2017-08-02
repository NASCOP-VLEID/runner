<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    //

    public function getTotalHolidaysinMonth($month)
	{
		switch ($month) {
			case 0:
				$totalholidays=10;
				break;
			case 1:
				$totalholidays=1;
				break;
			case 4:
				$totalholidays=2;
				break;
			case 5:
				$totalholidays=1;
				break;
			case 6:
				$totalholidays=1;
				break;
			case 8:
				$totalholidays=1;
				break;
			case 10:
				$totalholidays=1;
				break;
			case 12:
				$totalholidays=3;
				break;
			default:
				$totalholidays=0;
				break;
		}
		return $totalholidays;

	}

	public function getWorkingDays($startDate,$endDate){


	    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
	    //We add one to inlude both dates in the interval.
	    $days = (strtotime($endDate) - strtotime($startDate)) / 86400 + 1;

	    $no_full_weeks = floor($days / 7);

	    $no_remaining_days = fmod($days, 7);

	    //It will return 1 if it's Monday,.. ,7 for Sunday
	    $the_first_day_of_week = date("N",strtotime($startDate));

	    $the_last_day_of_week = date("N",strtotime($endDate));
	    // echo              $the_last_day_of_week;
	    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
	    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
	    if ($the_first_day_of_week <= $the_last_day_of_week){
	        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
	        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
	    }

	    else{
	        if ($the_first_day_of_week <= 6) {
	        //In the case when the interval falls in two weeks, there will be a Sunday for sure
	            $no_remaining_days--;
	        }
	    }

	    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
		//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
	   $workingDays = $no_full_weeks * 5;
	    if ($no_remaining_days > 0 )
	    {
	      $workingDays += $no_remaining_days;
	    }

	    //We subtract the holidays
		/*    foreach($holidays as $holiday){
	        $time_stamp=strtotime($holiday);
	        //If the holiday doesn't fall in weekend
	        if (strtotime($startDate) <= $time_stamp && $time_stamp <= strtotime($endDate) && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
	            $workingDays--;
	    }*/

	    return $workingDays;
	} 

	public function age_range($age){
		switch ($age) {
			case 1:
				$age_b = array(0.0001, 2);
				break;
			case 2:
				$age_b = array(2.0001, 18);
				break;
			case 3:
				$age_b = array(0.0001, 0.5);
				break;
			case 4:
				$age_b = array(1, 1.5);
				break;
			case 5:
				$age_b = array(24, 1200);
				break;
			default:
				break;
		}
		return $age_b;
	}

	public function age_band($age){
		switch ($age) {
			case 1:
				$age_b = array(0.0001, 2);
				break;
			case 2:
				$age_b = array(2.0001, 8);
				break;
			case 3:
				$age_b = array(8.0001, 12);
				break;
			case 4:
				$age_b = array(12.0001, 18);
				break;
			case 5:
				$age_b = array(18.0001, 1200);
				break;
			case 6:
				$age_b = array(0, 0);
				break;
			case 9:
				$age_b = array(0.0001, 0.5);
				break;
			case 10:
				$age_b = array(0.50001, 1.5);
				break;
			case 11:
				$age_b = array(1.5001, 2);
				break;
			case 12:
				$age_b = array(2.0001, 6);
				break;
			case 13:
				$age_b = array(6.0001, 9);
				break;
			case 14:
				$age_b = array(9.0001, 12);
				break;
			default:
				break;
		}
		return $age_b;
	}

	



}

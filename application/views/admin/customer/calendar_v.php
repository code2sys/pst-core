<?php
	/* draw table */
	$calendar = "<table width='100%'><tr><td><span class='prev' data-month='".$month."' data-year='".$year."'><i class='fa fa-caret-left' style='width:10px;' aria-hidden='true'></i></span>";
	$calendar .= "<span class='next' data-month='".$month."' data-year='".$year."'><i class='fa fa-caret-right' style='width:10px;' aria-hidden='true'></i></span>".date("F", mktime(0, 0, 0, $month, 10)).' '.$year."</td></tr><tr><td>";
	$calendar .= '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$d = strlen($list_day) == 1 ? '0'.$list_day : $list_day;
		$dt = $year.'-'.$month.'-'.$d;
		$cd = date('Y-m-d');
		$crntdt = $cd == $dt ? 'crntdt' : '';
		$calendar.= '<td class="calendar-day '.$crntdt.'" data-dt="'.$dt.'">';
			/* add in the day number */
			$calendar.= '<div class="day-number" data-dt="'.$dt.'">'.$list_day.'</div>';

			/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
			if( array_key_exists($dt, $eventData)) {
				foreach( $eventData[$dt] as $reminder) {
					$dot = strlen($reminder['subject']) > 13 ? '...' : '';
					$cmplt = $reminder['is_completed'] == '1' ? 'cmplt' : 'in-cmplt'; 
					$calendar .= "<p class='day-rem ".$cmplt."' data-id='".$reminder['id']."' data-dt='".$dt."'>".substr($reminder['subject'],0,12).$dot."</p>";
				}
			}
			//$calendar.= str_repeat('<p> </p>',2);
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table></td></tr></table>';
	
	/* all done, return result */
	echo $calendar;
?>
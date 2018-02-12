<?php
$contact_info = json_decode($credit['contact_info']);
$physical_address = json_decode($credit['physical_address']);
$housing_info = json_decode($credit['housing_info']);
$banking_info = json_decode($credit['banking_info']);
$previous_add = json_decode($credit['previous_add']);
$employer_info = json_decode($credit['employer_info']);
$prior_employer_info = json_decode($credit['prior_employer_info']);
$reference = json_decode($credit['reference']);

if ($credit['joint'] > 0) {
    $co_contact_info = json_decode($credit['co_contact_info']);
    $co_physical_address = json_decode($credit['co_physical_address']);
    $co_housing_info = json_decode($credit['co_housing_info']);
    $co_banking_info = json_decode($credit['co_banking_info']);
    $co_previous_add = json_decode($credit['co_previous_add']);
    $co_employer_info = json_decode($credit['co_employer_info']);
    $co_prior_employer_info = json_decode($credit['co_prior_employer_info']);

}


	SetLeftMargin(10);
	SetXY(10,10);
	
	SetFont('Times','B',14);

Cell(90,6, 'Status :-',0,0,'L',0);
Cell(90,6, $credit['application_status'],0,1,'L',0);
Cell(90,6, 'Submitted :-',0,0,'L',0);
Cell(90,6, date("m/d/Y g:i a t", strtotime($credit['application_date'])),0,1,'L',0);
Cell(90,6, 'Application Type :-',0,0,'L',0);
Cell(90,6, $credit['joint'] > 0 ? "Joint" : "Individual",0,1,'L',0);


    SetTextColor(255,255 ,255 );
	Cell(190,6, 'Vehicle Information',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(90,6, 'Type :-',0,0,'L',0);
	Cell(90,6, $credit['type'],0,1,'L',0);
	Cell(90,6, 'Condition :-',0,0,'L',0);
	Cell(90,6, $credit['condition'],0,1,'L',0);
	Cell(90,6, 'Year :-',0,0,'L',0);
	Cell(90,6, $credit['year'],0,1,'L',0);
	Cell(90,6, 'Make :-',0,0,'L',0);
	Cell(90,6, $credit['make'],0,1,'L',0);
	Cell(90,6, 'Model :-',0,0,'L',0);
	Cell(90,6, $credit['model'],0,1,'L',0);
	Cell(90,6, 'Down Payment :-',0,0,'L',0);
	Cell(90,6, $credit['down_payment'],0,1,'L',0);
	Cell(90,6, '',0,1,'L',0);
	
	SetFont('Times','B',14);
	SetTextColor(255,255 ,255 );
	Cell(190,6, 'Applicant Contact Information',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(90,6, 'First Name :-',0,0,'L',0);
	Cell(90,6, $credit['first_name'],0,1,'L',0);
	Cell(90,6, 'Middle Name :-',0,0,'L',0);
	Cell(90,6, $contact_info->mname,0,1,'L',0);
	Cell(90,6, 'Last Name :-',0,0,'L',0);
	Cell(90,6, $credit['last_name'],0,1,'L',0);
	Cell(90,6, "Driver's License :-",0,0,'L',0);
	Cell(90,6, $credit['driver_licence'],0,1,'L',0);
	Cell(90,6, 'Work Phone :-',0,0,'L',0);
	Cell(90,6, $contact_info->wphone,0,1,'L',0);
	Cell(90,6, 'Residence Phone :-',0,0,'L',0);
	Cell(90,6, $contact_info->rphone,0,1,'L',0);
	Cell(90,6, 'E-mail :-',0,0,'L',0);
	Cell(90,6, $credit['email'],0,1,'L',0);
	Cell(90,6, 'Social Security Number :-',0,0,'L',0);
	Cell(90,6, $contact_info->ssno,0,1,'L',0);
	Cell(90,6, 'Marital Status :-',0,0,'L',0);
	Cell(90,6, $contact_info->marital_status,0,1,'L',0);
	Cell(90,6, 'Male/Female :-',0,0,'L',0);
	Cell(90,6, $contact_info->gender,0,1,'L',0);
	Cell(90,6, 'Date of Birth :-',0,0,'L',0);
	Cell(90,6, $contact_info->dob,0,1,'L',0);
	Cell(90,6, '',0,1,'L',0);

	if ($credit["joint"] > 0) {
        SetFont('Times', 'B', 14);
        SetTextColor(255, 255, 255);
        Cell(190, 6, 'Co-Applicant Contact Information', 0, 1, 'L', 1);
        SetFont('Times', '', 12);
        SetTextColor(0, 0, 0);
        Cell(90, 6, '', 0, 1, 'L', 0);
        Cell(90, 6, 'First Name :-', 0, 0, 'L', 0);
        Cell(90, 6, $credit['co_first_name'], 0, 1, 'L', 0);
        Cell(90, 6, 'Middle Name :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->mname, 0, 1, 'L', 0);
        Cell(90, 6, 'Last Name :-', 0, 0, 'L', 0);
        Cell(90, 6, $credit['co_last_name'], 0, 1, 'L', 0);
        Cell(90, 6, "Driver's License :-", 0, 0, 'L', 0);
        Cell(90, 6, $credit['co_driver_licence'], 0, 1, 'L', 0);
        Cell(90, 6, 'Work Phone :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->wphone, 0, 1, 'L', 0);
        Cell(90, 6, 'Residence Phone :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->rphone, 0, 1, 'L', 0);
        Cell(90, 6, 'E-mail :-', 0, 0, 'L', 0);
        Cell(90, 6, $credit['co_email'], 0, 1, 'L', 0);
        Cell(90, 6, 'Social Security Number :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->ssno, 0, 1, 'L', 0);
        Cell(90, 6, 'Marital Status :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->marital_status, 0, 1, 'L', 0);
        Cell(90, 6, 'Male/Female :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->gender, 0, 1, 'L', 0);
        Cell(90, 6, 'Date of Birth :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_contact_info->dob, 0, 1, 'L', 0);
        Cell(90, 6, '', 0, 1, 'L', 0);
    }

	SetFont('Times','B',14);
	SetTextColor(255,255 ,255 );
	Cell(190,6, 'Applicant Physical Address Information',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(90,6, 'Physical Address :-',0,0,'L',0);
	Cell(90,6, $physical_address->paddress,0,1,'L',0);
	Cell(90,6, 'City :-',0,0,'L',0);
	Cell(90,6, $physical_address->city,0,1,'L',0);
	Cell(90,6, 'State :-',0,0,'L',0);
	Cell(90,6, $physical_address->state,0,1,'L',0);
	Cell(90,6, 'Zip :-',0,0,'L',0);
	Cell(90,6, $physical_address->zip,0,1,'L',0);
	Cell(90,6, 'Country :-',0,0,'L',0);
	Cell(90,6, $physical_address->country,0,1,'L',0);
	Cell(90,6, '',0,1,'L',0);

if ($credit["joint"] > 0) {
    SetFont('Times', 'B', 14);
    SetTextColor(255, 255, 255);
    Cell(190, 6, 'Co-Applicant Physical Address Information', 0, 1, 'L', 1);
    SetFont('Times', '', 12);
    SetTextColor(0, 0, 0);
    Cell(90, 6, '', 0, 1, 'L', 0);
    Cell(90, 6, 'Physical Address :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_physical_address->paddress, 0, 1, 'L', 0);
    Cell(90, 6, 'City :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_physical_address->city, 0, 1, 'L', 0);
    Cell(90, 6, 'State :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_physical_address->state, 0, 1, 'L', 0);
    Cell(90, 6, 'Zip :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_physical_address->zip, 0, 1, 'L', 0);
    Cell(90, 6, 'Country :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_physical_address->country, 0, 1, 'L', 0);
    Cell(90, 6, '', 0, 1, 'L', 0);
}

	SetFont('Times','B',14);
	SetTextColor(255,255 ,255 );
	Cell(190,6, 'Applicant Housing Information',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(90,6, 'Do you rent or own your home, or other ? :-',0,0,'L',0);
	Cell(90,6, $housing_info->owns,0,1,'L',0);
	Cell(90,6, 'Landlord / Mortgage :-',0,0,'L',0);
	Cell(90,6, $housing_info->landlord,0,1,'L',0);
	Cell(90,6, 'Rent / Mortgage Monthly Amount :-',0,0,'L',0);
	Cell(90,6, $housing_info->rent,0,1,'L',0);
	Cell(90,6, 'Mortgage Balance :-',0,0,'L',0);
	Cell(90,6, $housing_info->mort_balance,0,1,'L',0);
	Cell(90,6, 'Time at Current Residence :-',0,0,'L',0);
	Cell(90,6, $housing_info->years.' Years, '.$housing_info->months.' Months',0,1,'L',0);
	Cell(90,6, '',0,1,'L',0);

	if ($housing_info->years < 2) {
        SetFont('Times', 'B', 14);
        SetTextColor(255, 255, 255);
        Cell(190, 6, 'Applicant Previous Residence', 0, 1, 'L', 1);
        SetFont('Times', '', 12);
        SetTextColor(0, 0, 0);
        Cell(90, 6, '', 0, 1, 'L', 0);
        Cell(90, 6, 'Address :-', 0, 0, 'L', 0);
        Cell(90, 6, $previous_add->address, 0, 1, 'L', 0);
        Cell(90, 6, 'City :-', 0, 0, 'L', 0);
        Cell(90, 6, $previous_add->city, 0, 1, 'L', 0);
        Cell(90, 6, 'State :-', 0, 0, 'L', 0);
        Cell(90, 6, $previous_add->state, 0, 1, 'L', 0);
        Cell(90, 6, 'Zip :-', 0, 0, 'L', 0);
        Cell(90, 6, $previous_add->zip, 0, 1, 'L', 0);
        Cell(90, 6, 'How long at previous address ? :-', 0, 0, 'L', 0);
        Cell(90, 6, $previous_add->years . ' Years, ' . $previous_add->months . ' Months', 0, 1, 'L', 0);
        Cell(90, 6, '', 0, 1, 'L', 0);
    }

if ($credit["joint"] > 0) {
    SetFont('Times', 'B', 14);
    SetTextColor(255, 255, 255);
    Cell(190, 6, 'Co-Applicant Housing Information', 0, 1, 'L', 1);
    SetFont('Times', '', 12);
    SetTextColor(0, 0, 0);
    Cell(90, 6, '', 0, 1, 'L', 0);
    Cell(90, 6, 'Do you rent or own your home, or other ? :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_housing_info->owns, 0, 1, 'L', 0);
    Cell(90, 6, 'Landlord / Mortgage :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_housing_info->landlord, 0, 1, 'L', 0);
    Cell(90, 6, 'Rent / Mortgage Monthly Amount :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_housing_info->rent, 0, 1, 'L', 0);
    Cell(90, 6, 'Mortgage Balance :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_housing_info->mort_balance, 0, 1, 'L', 0);
    Cell(90, 6, 'Time at Current Residence :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_housing_info->years . ' Years, ' . $co_housing_info->months . ' Months', 0, 1, 'L', 0);
    Cell(90, 6, '', 0, 1, 'L', 0);

    if ($co_housing_info->years < 2) {
        SetFont('Times', 'B', 14);
        SetTextColor(255, 255, 255);
        Cell(190, 6, 'Co-Applicant Previous Residence (If less then 5 years at current address..)', 0, 1, 'L', 1);
        SetFont('Times', '', 12);
        SetTextColor(0, 0, 0);
        Cell(90, 6, '', 0, 1, 'L', 0);
        Cell(90, 6, 'Address :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_previous_add->address, 0, 1, 'L', 0);
        Cell(90, 6, 'City :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_previous_add->city, 0, 1, 'L', 0);
        Cell(90, 6, 'State :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_previous_add->state, 0, 1, 'L', 0);
        Cell(90, 6, 'Zip :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_previous_add->zip, 0, 1, 'L', 0);
        Cell(90, 6, 'How long at previous address ? :-', 0, 0, 'L', 0);
        Cell(90, 6, $co_previous_add->years . ' Years, ' . $co_previous_add->months . ' Months', 0, 1, 'L', 0);
        Cell(90, 6, '', 0, 1, 'L', 0);
    }
}
	
	SetFont('Times','B',14);
	SetTextColor(255,255 ,255 );
	Cell(190,6, 'Applicant Banking Information',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(90,6, 'Name of Bank :-',0,0,'L',0);
	Cell(90,6, $banking_info->bank_name,0,1,'L',0);
	Cell(90,6, 'Account Types :-',0,0,'L',0);
	Cell(90,6, $banking_info->ac_type,0,1,'L',0);
	Cell(90,6, 'Name of Bank :-',0,0,'L',0);
	Cell(90,6, $banking_info->bank_name1,0,1,'L',0);
	Cell(90,6, 'Account Types :-',0,0,'L',0);
	Cell(90,6, $banking_info->ac_type1,0,1,'L',0);
	Cell(90,6, '',0,1,'L',0);

if ($credit["joint"] > 0) {
    SetFont('Times', 'B', 14);
    SetTextColor(255, 255, 255);
    Cell(190, 6, 'Co-Applicant Banking Information', 0, 1, 'L', 1);
    SetFont('Times', '', 12);
    SetTextColor(0, 0, 0);
    Cell(90, 6, '', 0, 1, 'L', 0);
    Cell(90, 6, 'Name of Bank :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_banking_info->bank_name, 0, 1, 'L', 0);
    Cell(90, 6, 'Account Types :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_banking_info->ac_type, 0, 1, 'L', 0);
    Cell(90, 6, 'Name of Bank :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_banking_info->bank_name1, 0, 1, 'L', 0);
    Cell(90, 6, 'Account Types :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_banking_info->ac_type1, 0, 1, 'L', 0);
    Cell(90, 6, '', 0, 1, 'L', 0);
}


	SetFont('Times','B',14);
	SetTextColor(255,255 ,255 );
	Cell(190,6, 'Applicant Employer Information',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(90,6, 'Occupation :-',0,0,'L',0);
	Cell(90,6, $employer_info->occupation,0,1,'L',0);
	Cell(90,6, 'Employer Name :-',0,0,'L',0);
	Cell(90,6, $employer_info->emp_name,0,1,'L',0);
	Cell(90,6, 'Employer Address :-',0,0,'L',0);
	Cell(90,6, $employer_info->emp_addr,0,1,'L',0);
	Cell(90,6, 'Employer City :-',0,0,'L',0);
	Cell(90,6, $employer_info->emp_city,0,1,'L',0);
	Cell(90,6, 'Employer State :-',0,0,'L',0);
	Cell(90,6, $employer_info->state,0,1,'L',0);
	Cell(90,6, 'Employer Zip :-',0,0,'L',0);
	Cell(90,6, $employer_info->emp_zip,0,1,'L',0);
	Cell(90,6, 'Employer Phone :-',0,0,'L',0);
	Cell(90,6, $employer_info->emp_phone,0,1,'L',0);
	Cell(90,6, 'Salary(Annually Gross) :-',0,0,'L',0);
	Cell(90,6, $employer_info->salary,0,1,'L',0);
	Cell(90,6, 'Time at Employer :-',0,0,'L',0);
	Cell(90,6, $employer_info->year.' Years, '.$employer_info->month.' Months',0,1,'L',0);
	//Cell(90,6, 'Type of Employment :-',0,0,'L',0);
	//Cell(90,6, $employer_info->address,0,1,'L',0);
	Cell(90,6, 'Other Income :-',0,0,'L',0);
	Cell(90,6, $employer_info->other_income,0,1,'L',0);
	Cell(90,6, 'Other Income Frequency :-',0,0,'L',0);
	Cell(90,6, $employer_info->income_frequency,0,1,'L',0);
	Cell(90,6, 'Additional Comments  :-',0,0,'L',0);
	Cell(90,6, $employer_info->comments,0,1,'L',0);
	Cell(90,6, '',0,1,'L',0);

	if (isset($prior_employer_info->occupation)) {
        SetFont('Times','B',14);
        SetTextColor(255,255 ,255 );
        Cell(190,6, 'Applicant Previous Employer Information',0,1,'L',1);
        SetFont('Times','',12);
        SetTextColor(0,0,0 );
        Cell(90,6, '',0,1,'L',0);
        Cell(90,6, 'Occupation :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->occupation,0,1,'L',0);
        Cell(90,6, 'Employer Name :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->emp_name,0,1,'L',0);
        Cell(90,6, 'Employer Address :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->emp_addr,0,1,'L',0);
        Cell(90,6, 'Employer City :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->emp_city,0,1,'L',0);
        Cell(90,6, 'Employer State :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->state,0,1,'L',0);
        Cell(90,6, 'Employer Zip :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->emp_zip,0,1,'L',0);
        Cell(90,6, 'Employer Phone :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->emp_phone,0,1,'L',0);
        Cell(90,6, 'Salary(Annually Gross) :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->salary,0,1,'L',0);
        Cell(90,6, 'Time at Employer :-',0,0,'L',0);
        Cell(90,6, $prior_employer_info->year.' Years, '.$prior_employer_info->month.' Months',0,1,'L',0);
        //Cell(90,6, 'Type of Employment :-',0,0,'L',0);
        //Cell(90,6, $prior_employer_info->address,0,1,'L',0);
    }


if ($credit["joint"] > 0) {
    SetFont('Times', 'B', 14);
    SetTextColor(255, 255, 255);
    Cell(190, 6, 'Co-Applicant Employer Information', 0, 1, 'L', 1);
    SetFont('Times', '', 12);
    SetTextColor(0, 0, 0);
    Cell(90, 6, '', 0, 1, 'L', 0);
    Cell(90, 6, 'Occupation :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->occupation, 0, 1, 'L', 0);
    Cell(90, 6, 'Employer Name :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->emp_name, 0, 1, 'L', 0);
    Cell(90, 6, 'Employer Address :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->emp_addr, 0, 1, 'L', 0);
    Cell(90, 6, 'Employer City :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->emp_city, 0, 1, 'L', 0);
    Cell(90, 6, 'Employer State :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->state, 0, 1, 'L', 0);
    Cell(90, 6, 'Employer Zip :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->emp_zip, 0, 1, 'L', 0);
    Cell(90, 6, 'Employer Phone :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->emp_phone, 0, 1, 'L', 0);
    Cell(90, 6, 'Salary(Annually Gross) :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->salary, 0, 1, 'L', 0);
    Cell(90, 6, 'Time at Employer :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->year . ' Years, ' . $co_employer_info->month . ' Months', 0, 1, 'L', 0);
    //Cell(90,6, 'Type of Employment :-',0,0,'L',0);
    //Cell(90,6, $employer_info->address,0,1,'L',0);
    Cell(90, 6, 'Other Income :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->other_income, 0, 1, 'L', 0);
    Cell(90, 6, 'Other Income Frequency :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->income_frequency, 0, 1, 'L', 0);
    Cell(90, 6, 'Additional Comments  :-', 0, 0, 'L', 0);
    Cell(90, 6, $co_employer_info->comments, 0, 1, 'L', 0);
    Cell(90, 6, '', 0, 1, 'L', 0);


    if (isset($co_prior_employer_info->occupation)) {
        SetFont('Times','B',14);
        SetTextColor(255,255 ,255 );
        Cell(190,6, 'Co-Applicant Previous Employer Information',0,1,'L',1);
        SetFont('Times','',12);
        SetTextColor(0,0,0 );
        Cell(90,6, '',0,1,'L',0);
        Cell(90,6, 'Occupation :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->occupation,0,1,'L',0);
        Cell(90,6, 'Employer Name :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->emp_name,0,1,'L',0);
        Cell(90,6, 'Employer Address :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->emp_addr,0,1,'L',0);
        Cell(90,6, 'Employer City :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->emp_city,0,1,'L',0);
        Cell(90,6, 'Employer State :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->state,0,1,'L',0);
        Cell(90,6, 'Employer Zip :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->emp_zip,0,1,'L',0);
        Cell(90,6, 'Employer Phone :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->emp_phone,0,1,'L',0);
        Cell(90,6, 'Salary(Annually Gross) :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->salary,0,1,'L',0);
        Cell(90,6, 'Time at Employer :-',0,0,'L',0);
        Cell(90,6, $co_prior_employer_info->year.' Years ,'.$co_prior_employer_info->month.' Months',0,1,'L',0);
        //Cell(90,6, 'Type of Employment :-',0,0,'L',0);
    }
}

	SetFont('Times','B',14);
	SetTextColor(255,255 ,255 );
	Cell(190,6, 'References',0,1,'L',1);
	SetFont('Times','',12);
	SetTextColor(0,0,0 );
	Cell(90,6, '',0,1,'L',0);
	Cell(24,6, 'Name :-',0,0,'L',0);
	Cell(24,6, $reference->name1,0,0,'L',0);
	Cell(24,6, 'Phone :-',0,0,'L',0);
	Cell(24,6, $reference->phone1,0,0,'L',0);
	Cell(24,6, 'City :-',0,0,'L',0);
	Cell(24,6, $reference->city1,0,0,'L',0);
	Cell(24,6, 'State :-',0,0,'L',0);
	Cell(24,6, $reference->state1,0,0,'L',0);
	Cell(90,6, '',0,1,'L',0);
	Cell(24,6, 'Name :-',0,0,'L',0);
	Cell(24,6, $reference->name2,0,0,'L',0);
	Cell(24,6, 'Phone :-',0,0,'L',0);
	Cell(24,6, $reference->phone2,0,0,'L',0);
	Cell(24,6, 'City :-',0,0,'L',0);
	Cell(24,6, $reference->city2,0,0,'L',0);
	Cell(24,6, 'State :-',0,0,'L',0);
	Cell(24,6, $reference->state2,0,0,'L',0);
	Cell(90,6, '',0,1,'L',0);
	Cell(24,6, 'Name :-',0,0,'L',0);
	Cell(24,6, $reference->name3,0,0,'L',0);
	Cell(24,6, 'Phone :-',0,0,'L',0);
	Cell(24,6, $reference->phone3,0,0,'L',0);
	Cell(24,6, 'City :-',0,0,'L',0);
	Cell(24,6, $reference->city3,0,0,'L',0);
	Cell(24,6, 'State :-',0,0,'L',0);
	Cell(24,6, $reference->state3,0,0,'L',0);
	Cell(90,6, '',0,1,'L',0);
	Cell(24,6, 'Name :-',0,0,'L',0);
	Cell(24,6, $reference->name4,0,0,'L',0);
	Cell(24,6, 'Phone :-',0,0,'L',0);
	Cell(24,6, $reference->phone4,0,0,'L',0);
	Cell(24,6, 'City :-',0,0,'L',0);
	Cell(24,6, $reference->city4,0,0,'L',0);
	Cell(24,6, 'State :-',0,0,'L',0);
	Cell(24,6, $reference->state4,0,0,'L',0);
	Cell(90,6, '',0,1,'L',0);

?>
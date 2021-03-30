<?php

if(config('client_connected') && (Config::get("client_data")->domain_name=="healtcaremydoctor" || Config::get("client_data")->domain_name=="default")){
	return [
	    'Filters' => 'Gender & Expertise',
	    'Vendors' => 'Doctors',
	    'Vendor' => 'Doctor',
	    'Users' => 'Patients',
	    'User' => 'Patient',
	    'Additional Fields' => 'Education & Others',
	    'Additional Field' => 'Education & Others',
	    'Cat. Service Type' => 'Mode of Intetraction',
	    'Master Preferences' => 'Master Preferences',
	    'Master Preference' => 'Master Preference',
	    'Symptoms' => 'Symptoms',
	    'Symptom' => 'Symptom',
	    'Medical Report' => 'Medical Record',
	];
}else if(config('client_connected') && (Config::get("client_data")->domain_name=="intely")){
	return [
	    'Filters' => 'Filters',
	    'Vendors' => 'Nurses',
	    'Vendor' => 'Nurse',
	    'User' => 'User',
	    'Users' => 'Users',
	    'Additional Fields' => 'Additional Fields',
	    'Additional Field' => 'Additional Field',
	    'Cat. Service Type' => 'Cat. Service Type',
	    'Master Preferences' => 'Covid/Preference',
	    'Master Preference' => 'Covid/Preference',
	    'Custom Master Preferences' => 'Duties',
	    'Custom Master Preference' => 'Duties',
	    'Symptoms' => 'Symptoms',
	    'Symptom' => 'Symptom',
	];
}else if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r")){
	return [
	    'Filters' => 'Filters',
	    'Vendors' => 'Professionals',
	    'Vendor' => 'Professional',
	    'User' => 'User',
	    'Users' => 'Users',
	    'Additional Fields' => 'Additional Fields',
	    'Additional Field' => 'Additional Field',
	    'Cat. Service Type' => 'Cat. Service Type',
	    'Master Preferences' => 'Master Preferences',
	    'Master Preference' => 'Master Preference',
	    'Symptoms' => 'Symptoms',
	    'Symptom' => 'Symptom',
	];
}else if(config('client_connected') && (Config::get("client_data")->domain_name=="physiotherapist")){
	return [
	    'Filters' => 'Filters',
	    'Vendors' => 'Physiotherapists',
	    'Vendor' => 'Physiotherapist',
	    'User' => 'Patient',
	    'Users' => 'Patients',
	    'Additional Fields' => 'Additional Fields',
	    'Additional Field' => 'Additional Field',
	    'Cat. Service Type' => 'Cat. Service Type',
	    'Master Preferences' => 'Covid/Preference',
	    'Master Preference' => 'Covid/Preference',
	    'Symptoms' => 'Symptoms',
	    'Symptom' => 'Symptom',
	    'Sub Category' => 'Session at Centre',
	];
}else{
	return [
	    'Filters' => 'Filters',
	    'Vendors' => 'Consultants',
	    'Vendor' => 'Consultant',
	    'User' => 'User',
	    'Users' => 'Users',
	    'Additional Fields' => 'Additional Fields',
	    'Additional Field' => 'Additional Field',
	    'Cat. Service Type' => 'Cat. Service Type',
	    'Master Preferences' => 'Master Preferences',
	    'Master Preference' => 'Master Preference',
	    'Master PreferenceLifestyle' => 'Lifestyles',
	    'Master PreferenceMedicalHistory' => 'Medical History',
	    'Medical Report' => 'Medical Record',
	    'Symptoms' => 'Symptoms',
	    'Symptom' => 'Symptom',
	];
}
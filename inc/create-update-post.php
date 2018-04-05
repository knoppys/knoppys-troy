<?php
function troy_categories() {
	
	//Get the XML file
	$file = file_get_contents(get_site_url().'/wp-content/plugins/knoppys-troy/uploads/PublishJobCategories.xml');
	
	//Create a new object
	$categoriesXML = new SimpleXMLElement($file);

	//Foreach of the categories in the XML file 
	foreach ($categoriesXML->category as $category) {

			//Get the correct WP term name which has to be the actual name
			if ($category['description'] == 'Type Of Job') {
				$taxonomyName = 'job_type';
			} elseif ($category['description'] == 'Hours') {
				$taxonomyName = 'no_of_hours';
			} elseif ($category['description'] == 'Sector') {
				$taxonomyName = 'sector';
			}

			/*************
			* Check to see if the <job_category> exists. 
			* Update or create it
			*************/		
			foreach ($category->job_category as $job_category) {					
				if(term_exists($job_category['code'],$taxonomyName)){						
					$categoryName = get_term($job_category[0], $taxonomyName);
					$args = array('slug'=>$job_category[0],'description'=>$job_category[0]);					
					wp_update_term( $categoryName->ID, $taxonomyName, $args);
					update_term_meta($categoryName->ID,'code', $job_category['code']);					
				} else {					
					$args = array('slug'=>$job_category[0],'description'=>$job_category[0]);								
					wp_insert_term( $job_category['code'], $taxonomyName, $args);	
					$term = get_term($job_category[0]);
					update_term_meta($term->ID,'code', $job_category['code']);					
				}
				
			}

		}	
}

function troy_vacancies() {
	
	//Live URL
	$files = glob('/home/hamblinco/public_html/wp-content/plugins/knoppys-troy/uploads/*.xml');	
	
	//DEV URL
	//$files = glob('/var/www/hamblin/wp-content/plugins/knoppys-troy/uploads/*.xml');

	foreach ($files as $file) {	

		//Live URL
		if ($file !== '/home/hamblinco/public_html/wp-content/plugins/knoppys-troy/uploads/PublishJobCategories.xml') {	

		//Dev URL
		//if ($file !== '/var/www/hamblin/wp-content/plugins/knoppys-troy/uploads/PublishJobCategories.xml') {	

			//Create the XML Object			
			$jobFile = file_get_contents($file);					
			$vacancyXML = new SimpleXMLElement($jobFile);	

			//Get the xml method
			$method = $vacancyXML->attributes()->method;		

			//Get the function name and Add or Delete the vacancy
			if ($method == 'Add' || $method == 'Update') {	
				
				//Check to see if the post already exists
				$args = array(
					'post_type' => 'vacancy',
					'meta_key' => 'ref_no',
					'meta_value' => (string)$vacancyXML->ref_no,
				);
				$vacancy = get_posts($args);	

				if (!$vacancy) {
					vacancies_add($vacancyXML);
				}	

			} elseif ($method == 'Delete') {	

				vacancies_delete((string)$vacancyXML->ref_no);

			} elseif ($method == '') {

				wp_mail(get_option( 'admin_email' ), 'XML Error', 'There has been an error updating '.(string)$vacancyXML->ref_no);
				
			}	

			//Once actions are complete, archive the file
			vacancies_archive($file);	

						
		}
			
	}
}



function vacancies_add($vacancyXML){

	//Insert the post basics
	$basics = array(
	'post_title' => (string)$vacancyXML->position,
	'post_content' => (string)$vacancyXML->long_description,
	'post_date' => (string)$vacancyXML->date_published['date'],
	'post_type' => 'vacancy',
	'post_status' => 'publish'
	);	
	$new_post = wp_insert_post($basics, true);										
	
	//Update the post meta
	$meta = array(
	'job_id' => (string)$vacancyXML->attributes()->id,
	'ref_no' => (string)$vacancyXML->ref_no,
	'division' => (string)$vacancyXML->division,
	'start_date' => (string)$vacancyXML->start_date,
	'location' => (string)$vacancyXML->location,
	'salary' => (string)$vacancyXML->salary,
	'consultant_email' => (string)utf8_encode($vacancyXML->consultant_email),
	'consultant_phone' => (string)$vacancyXML->consultant_phone,
	'consultant_fax' => (string)$vacancyXML->consultant_fax,
	'consultant_name' => (string)$vacancyXML->consultant_name,
	'consultant_code' => (string)$vacancyXML->consultant_code,
	'job_type' => (string)$vacancyXML->job_type,					
	);
	foreach ($meta as $key => $value) {
		$update = update_post_meta($new_post, $key, $value);						
	}

	//String for job_type  taxonomy				
	$job_type_code = $vacancyXML->category[0]->job_category['group'] . $vacancyXML->category[0]->job_category['code'];
	wp_set_object_terms($new_post, str_replace(' ', '', $job_type_code), 'job_type');
					
	//String for no_of_hours  taxonomy				
	$job_type_code = $vacancyXML->category[1]->job_category['group'] . $vacancyXML->category[1]->job_category['code'];
	wp_set_object_terms($new_post, str_replace(' ', '', $job_type_code), 'no_of_hours');
						//String for sector  taxonomy				
	$job_type_code = $vacancyXML->category[2]->job_category['group'] . $vacancyXML->category[2]->job_category['code'];
	wp_set_object_terms($new_post, str_replace(' ', '', $job_type_code), 'sector');	

	//Once actions are complete, archive the file
	vacancies_archive($file);	
	
}

function vacancies_delete($job_ref){
	$args = array(
		'post_type' => 'vacancy',
		'meta_key' => 'job_id',
		'meta_value' => $job_ref
	);
	$vacancy = get_posts($args);
	foreach ($vacancy as $vacancy) {
		$deleted = wp_delete_post($vacancy->ID, true);
		/*
		if ($deleted) {
			echo $jobID.' has been deleted.<br><br>';
		}
		*/		
	}	
}

function vacancies_archive($file){

	//Live URL
	rename($file, '/home/hamblinco/public_html/wp-content/plugins/knoppys-troy/uploads/archive/'.basename($file));

	//Dev URL
	//rename($file, '/var/www/hamblin/wp-content/plugins/knoppys-troy/uploads/archive/'.basename($file));

}
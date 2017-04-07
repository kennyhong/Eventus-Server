<?php
/*****************************************
This script populates the servers database with useful and authentic looking mock data.
The script assumes that the server is empty before it runs
*****************************************/


//creates a service with given variables
function makeService($name, $cost){
	$url = 'http://eventus.us-west-2.elasticbeanstalk.com/api/services/';
	$data = array('name' => $name,'cost' => $cost);
	$options = array(
	'http' => array(
    'method'  => 'POST',
    'content' => json_encode( $data ),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
	$context  = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	$response = json_decode( $result );//not really using this after it returns but whatever
}
//create a service tag with given name
function makeTag($name){
	$url = 'http://eventus.us-west-2.elasticbeanstalk.com/api/service_tags/';
	$data = array('name' => $name);
	$options = array(
	'http' => array(
    'method'  => 'POST',
    'content' => json_encode( $data ),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
	$context  = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	$response = json_decode( $result );//not really using this after it returns but whatever
}
//link a service tag to a service
function makeLink($sid, $tid){
	$url = 'http://eventus.us-west-2.elasticbeanstalk.com/api/services/'.$sid.'/service_tags/'.$tid;
	$options = array(
	'http' => array(
    'method'  => 'POST',
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
	$context  = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	$response = json_decode( $result );//not really using this after it returns but whatever
}

function makeLinks($sid, $tidArray){
	foreach($tidArray as &$tid){
		makeLink($sid,$tid);
	}
}

//set up tags
makeTag('Catering');//1
makeTag('Wedding');//2
makeTag('Stadium');//3
makeTag('Entertainment');//4
makeTag('Music');//5
makeTag('Child Friendly');//6
makeTag('Transportation');//7
makeTag('Florist');//8
makeTag('Minister');//9
makeTag('Venue');//10
makeTag('Photographer');//11
makeTag('Bakery');//12
makeTag('Bar');//13

//make services and link to tags
makeService('Buccacino\'s Fresca Italiana',56.49);//1
makeLinks(1,array(1,13));

makeService('Academy Bowling Lanes',15.99);//2
makeLinks(2,array(4,6));

makeService('Investers Group Field',799.99);
makeLinks(3,array(3,4,9));

makeService('Lakewood Florist & Gifts Ltd',87.99);
makeLinks(4,array(2,8));

makeService('Empire Photography',249.99);
makeLinks(5,array(2,10));

makeService('Yoga Pilates and More',99.99);
makeLinks(6,array(9));

makeService('Supreme Entertainer',125.00);
makeLinks(7,array(4,5));

makeService('Moxie\'s Bar and Grill',30.99);
makeLinks(8,array(1,9,12,13));

makeService('Original Joe\'s Restaurant and Bar',59.99);
makeLinks(9,array(1,9,12,13));

makeService('McGavin\'s Bread Baskets',19.99);
makeLinks(10,array(1,11,13));

makeService('Pita Pit',12.99);
makeLinks(11,array(1,6,13));

makeService('Stella\'s Catering and Commissary',59.99);
makeLinks(12,array(1,13));

makeService('Eiffel Tower Pastry Shop and Catering',89.99);
makeLinks(13,array(1,2,13));

makeService('Ronnette\'s Catering Services',77.99);
makeLinks(14,array(1));

makeService('Pony Corral Restaurant and Bar',55.99);
makeLinks(15,array(1,9,12,13));

makeService('Rae and Jerry\'s Steak House',88.99);
makeLinks(16,array(1,12,13));

makeService('Bellamy\'s Restaurant and Bar',76.99);
makeLinks(17,array(12,13));

makeService('FortWhyte Alive',142.99);
makeLinks(18,array(2,9));

makeService('Patricia\'s Ballroom',420.99);
makeLinks(19,array(2,9));

makeService('The Fort Garry Hotel',527.99);
makeLinks(20,array(2,9));

makeService('The Pavilion Event Center',742.99);
makeLinks(21,array(2,9));

makeService('MTS Center',1899.99);
makeLinks(22,array(3,4,9));

makeService('SilverVity St. Vital Cinemas',12.50);
makeLinks(23,array(4));

makeService('Celebrations Dinner Theatre',78.99);
makeLinks(24,array(4,9,13));

makeService('Lyric Theatre',99.99);
makeLinks(25,array(4,9));

makeService('Roadshow Sound and Lite',147.99);
makeLinks(26,array(4,5,6));

makeService('Crystal Sound',80.99);
makeLinks(27,array(4,5));

makeService('Gudlite Entertainment',120.99);
makeLinks(28,array(4,5));

makeService('Executive Limousine Service',99.99);
makeLinks(29,array(7));

makeService('Five Start SUV Limousine',119.99);
makeLinks(30,array(7));

makeService('A Royal Limousine Service',109.99);
makeLinks(31,array(7));

makeService('Unicity Taxy Ltd',49.99);
makeLinks(32,array(7));

makeService('Spring Taxi',55.99);
makeLinks(33,array(7));

makeService('Duffy\'s Taxi',59.99);
makeLinks(34,array(7));

makeService('In Full Bloom Florists',99.99);
makeLinks(35,array(2,8));

makeService('Myra Rose Florist',99.99);
makeLinks(36,array(2,8));

makeService('The Floral Fixx',99.99);
makeLinks(37,array(2,8));

makeService('Joel Ross Photography',229.99);
makeLinks(38,array(2,10));

makeService('Empire Photography',349.99);
makeLinks(39,array(2,10));

makeService('Rygiel Photography and Video',338.99);
makeLinks(40,array(1,20));

makeService('Weston Bakeries Ltd',63.99);
makeLinks(41,array(11));

makeService('Roll Cake Bakery and Dessert Inc',34.99);
makeLinks(42,array(11));

makeService('Q Karaoke Restaurant and Bar',33.99);
makeLinks(43,array(12,13));

makeService('Barley Brothers',55.99);
makeLinks(44,array(12,13));

makeService('Tavern United',44.99);
makeLinks(45,array(12,13));

?>
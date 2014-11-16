<?php
//BABEL CONFIGURATION FILE/////////////////////////////////////////
//Doublecheck to change .htaccess file on root folder in order for the rewrite rules to work properly
//Language trenslations ar set in an appropriate .php file eg: el.php for Greek or en.php for English 
///////////////////////////////////////////////////////////////////
//here are the variables to change the logo at the right corner
//you may provide your project's home page and logo or whatever you like
$default_language="el";//nothing happens for now
$project_name="Greek Wordnet";
$project_homepage="http://libver.math.auth.gr";
$logo="../semimages/okfn.svg";
//////////////////////////////////////////////////////////////////
//this is the most important section of the configuration file
// if you get nothing than errors check these option first

//here you sould provide your sparql endpoint. be sure to include the port if it's not in default:80
$sparql_endpoint="http://localhost:8890/sparql";
//here goes the deafault graph you should check at. Currently it just changes the default graph displayed in the interface
$default_graph="";
//base uri.the part of the iri of your entities that is same for every iri.
$base="http://libver.math.auth.gr/resource/"; 
$base_prefix="libver";
//default iri to display if nothing provided in adress bar
$default_uri="";
//here you should fill in your server. Fill in http://localhost if you just check the browser localy
$host="http://localhost";
///////////////////////////////////////////////////////////////////
//here goes the desired properties for showing in the top of the page. feel free tou change the desired properties you eould like to show up

$type_prop="rdf:type";
$label_prop="rdfs:label";
$comment_prop="";
//image is not yet implemented
$depiction_prop="";

//if you want to hide propertie's urls with simple prefixes, you should add the appropriate namespace prefix in the file prefix.txt
//example:
//		 
//		 rdf=http://www.w3.org/1999/02/22-rdf-syntax-ns# 

///////////////////////////////////////////////////////////////////
//here are some options that change the behaviour of the browser
//value 0 means this option is disabled where 1 means is enable

$opt_label=1;//change to 1 if you want to disblay the entities $label_prop instead of the last part of the IRI
$bidirection=1;//defaults to 1, in order to display both rel and rev links. Change to 0 if you want only rel links
$local=0;//CAUTION!!!!!!THIS OPTION IS EXPERIMENTAL!!!!!. I suggest not to turn this on coz ugly bugs will fly on your screen. Idealy you could publish data stored in a different server than yours. Interesting right?? 



?>
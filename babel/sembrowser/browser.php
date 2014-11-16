<?php 
//	include 'dereference.php';
	//initials
	
	include 'initialization/el.php';
	include 'initialization/config.php';
	$uri=$_GET["uri"];
	//decoding the uri
	$uri=urldecode($uri);
	//find the last part of the uri. this is helpful for later stages
	$posu=strlen($base);
	$propu=substr($uri,$posu);
	//creating header to be posted
	header('Link: <'.$host.'/data/'.$propu.'.rdf>; rel="alternate"; type="application/rdf+xml"; title="Structured Descriptor Document (RDF/XML format)",
	<'.$host.'/data/'.$propu.'.n3>; rel="alternate"; type="text/n3"; title="Structured Descriptor Document (N3/Turtle format)",
	<'.$host.'/data/'.$propu.'.json>; rel="alternate"; type="application/json"; title="Structured Descriptor Document (RDF/JSON format)",
	<'.$host.'/data/'.$propu.'.atom>; rel="alternate"; type="application/atom+xml"; title="OData (Atom+Feed format)",
	<'.$host.'/data/'.$propu.'.csv>; rel="alternate"; type="text/csv"; title="Structured Descriptor Document (CSV format)",
	<'.$host.'/data/'.$propu.'.ntriples>; rel="alternate"; type="text/plain"; title="Structured Descriptor Document (N-Triples format)",
	<'.$host.'/resource/'.$propu.'>; rel="http://xmlns.com/foaf/0.1/primaryTopic",
	<'.$host.'/resource/'.$propu.'>; rev="describedBy"');
	//end of creating header
	
	//setting default uri if no uri posted
	if(strcmp($uri,"")==0){
		$uri=$default_uri;
		}
	//reading of prefixes that show up in property column and http:// literals
	$prefix1=file_get_contents('initialization/prefix.txt');
	$prefix=str_getcsv($prefix1,"\n");
	$count2=0;
	foreach ($prefix as &$ns){
		$ns=str_getcsv($ns,"=");
		$nspace_c1[$count2]=$ns[0];
		$nspace_c2[$count2]=$ns[1];
		$count2++;
		}
	//getting data to be printed on the header such as title, short description, resource type, and picture
	
	$query="$sparql_endpoint?default-graph-uri=&query=define+input:inference+'gnd'+select+%3Ftype+%3Flabel+%3Fcomment+%3Fdepiction+%3Fgraph+where+{GRAPH+%3Fgraph+{OPTIONAL{%3C$uri%3E+$type_prop+%3Ftype}.+OPTIONAL{%3C$uri%3E+$label_prop+%3Flabel}.+OPTIONAL+{%3C$uri%3E+$comment_prop+%3Fcomment}.+OPTIONAL+{%3C$uri%3E+$depiction_prop+%3Fdepiction}}}&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
	$csv = file_get_contents($query);
	$Data = str_getcsv($csv,"\n");
	foreach($Data as &$Row1) {
		$Row1 = str_getcsv($Row1, ","); //parse the items in rows 
	}
	//the query generates a two lined csv file, the first being the titles of the columns, so we get just the seconf line containing the required data
	$type=$Row1[0];
	$label=$Row1[1];
	$comment=$Row1[2];
	$depiction=$Row1[3];
	$graph=$Row1[4];
	
	//define some variables
	$class1="even";//even class row,required for zebra style table presentation
	$class2="odd";//odd class for the same reason. the two classes change each other as <tr> classes to produce tble style
	$temp="";//variable to change the row <tr> class
	$count=0;//simple check in what row we are currently
	$prop="";
	//the query to be executed is a select query that selects only -> relations in csv format
	//the variable $sparql_endpoint is defined in initialization/config.php
	$sparql_query="select ?property ?value where {<$uri> ?property ?value}";
	$query2="$sparql_endpoint?default-graph-uri=".urlencode($default_graph)."&query=".urlencode($sparql_query)."&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";

	//$query2="$sparql_endpoint?default-graph-uri=&query=select+?property+?value+where+{%3C$uri%3E+%3Fproperty+%3Fvalue%7D&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
	//getting data
	//get the file
	$csv2 = file_get_contents($query2);
	//split it in seperate strings and put in an array $Data2,seperator is the \n character, the change of row
	$Data2 = str_getcsv($csv2, "\n"); //parse the rows 
	
	/////////////////////////////////////////DOCTYPE////////////////////////////////////////////////
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML+RDFa 1.0//EN\" \"http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\"\n\txmlns:foaf=\"http://xmlns.com/foaf/0.1/\"\n\txmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n\tversion=\"XHTML+RDFa 1.0\" xml:lang=\"el\">\n";
	/////////////////////////////////////////HEAD///////////////////////////////////////////////////////////////////
	echo "<head profile=\"http://www.w3.org/1999/xhtml/vocab\">\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"../semstyle/mystyle.css\" />\n";
	echo "\t<title>$about:$propu</title>\n";//title
	echo "\t<meta charset=\"utf-8\">\n";//charset
	//links. The variable $host and $propu
	echo "\t<link rel=\"alternate\" type=\"application/rdf+xml\"  href=\"$host/data/$propu.rdf\" title=\"Structured Descriptor Document (RDF/XML format)\" />\n";
	echo "\t<link rel=\"alternate\" type=\"text/rdf+n3\"          href=\"$host/data/$propu.n3\" title=\"Structured Descriptor Document (N3/Turtle format)\" />\n";
    echo "\t<link rel=\"alternate\" type=\"application/json\"     href=\"$host/data/$propu.json\" title=\"Structured Descriptor Document (RDF/JSON format)\" />\n";
    echo "\t<link rel=\"alternate\" type=\"application/atom+xml\" href=\"$host/data/$propu.atom\" title=\"OData (Atom+Feed format)\" />\n";
    echo "\t<link rel=\"alternate\" type=\"text/plain\"           href=\"$host/data/$propu.ntriples\" title=\"Structured Descriptor Document (N-Triples format)\" />\n";
    echo "\t<link rel=\"alternate\" type=\"text/csv\"             href=\"$host/data/$propu.csv\" title=\"Structured Descriptor Document (CSV format)\" />\n";
    echo "\t<link rel=\"foaf:primarytopic\" href=\"$uri\"/>\n";
	echo "\t<link rev=\"describedby\" href=\"$uri\"/>\n";
	echo "</head>\n";

	//////////////////////////////////////BODY/////////////////////////////////////////////////////
	echo "<body about=\"$uri\">\n";
	/////////////////////////////////////GOOGLE///////////////////////////////////////////////////
	include_once("initialization/googletrack.php");
	//////////////////////////////////////HEADER//////////////////////////////////////////////////
	echo "<div id=\"header\">\n";
	echo "<div id=\"hd_l\">\n";//hd_l contains the basic information presented in the left top of the page
	//About: page $about is defined in the selected language document
	echo "<h1 id=\"title\">$about:";
	echo "<a href=\"$uri\">";
	//controls what should be printed after About:.Possible options is the last part of the uri or the $label obtained from the first query. The switch is on the config.php
	if($opt_label==1){
		echo $label;
		}
	else{
		echo $propu;
		}
	echo "</a></h1>\n";
	//this div contains some basic information about the type of the entity and the dataset
	echo "<div class=\"page-resource-uri\">\n";
	$pos5=strrpos($type,'#');
	if($pos5===FALSE){
		$pos5=strrpos($type,'/');
	}
	$prop5=substr($type,$pos5+1);
	$len5=strlen($prop5);
	$pre5=substr($type,0,-$len5);
	echo "$entity : <a href=\"$type\">$prop5</a>\n";
	echo "$named_graph : <a href=\"$graph\">$graph</a>\n";
	echo "$data_space : <a href=\"$host\">$host</a>\n";
	echo "</div>\n";
	echo "</div>\n";
	//div hd_r contains the project_logo
	echo "<div id=\"hd_r\"><a href=\"$project_homepage\" title=\"$about $project_name\"><img src=\"$logo\" height=\"64\" alt=\"$about $project_name\"></a>";
	echo "</div>\n";
	
	
	echo "</div>\n";
	//here starts the real deal...
	echo "<div id=\"content\">\n";
	//short description, could be a rdfs:comment on in the anything you think is useful. What $comment present is configurable via initialization/config.php
	echo "<p>$comment</p>\n";
	//Properties table
	echo "<table class=\"description\">\n";
	// write column titles
	echo "<tr><th id=\"property\">$property</th><th id=\"value\">$value</th></tr>\n";
	foreach($Data2 as &$Row) {
		$Row = str_getcsv($Row, ","); //parse the items in rows 
			if ($count==0){//if it is the first line it contains only headers, which we don't want them so end the proccess and go for the next line
			$count++;
			continue;
			}
			//if the property is diferrent from the previous, start a new line
			if($Row[0]!=$temp){
				$temp=$Row[0];
				//change the classes
				list($class1,$class2) = array($class2,$class1);
				if($count!=1){
					echo "</ul></td></tr>\n";
				}
				//search for property namespasce containing the # character
				$pos1=strrpos($Row[0],'#');
				
				//parsing properties
				if ($pos1===FALSE){
					//in case of slash ending of the property
					$pos2=strrpos($Row[0],'/');
					//prop is the property
					$prop=substr($Row[0],$pos2+1);
					$len1=strlen($prop);
					//pre is the namespace
					$pre=substr($Row[0],0,-$len1);
					$count2=0;
					//find an appropriate prefix defined in initialization/prefixes.txt
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre)==0){
							$uri3=$pre;
							$pre=$nspace_c1[$count2];
							break;
						}
					$count2++;
					}
				}
				else{
					//same as above this time for hash ending predicates
					$pos2=strrpos($Row[0],'#');
					$prop=substr($Row[0],$pos2+1);
					$len1=strlen($prop);
					$pre=substr($Row[0],0,-$len1);
					$count2=0;
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre)==0){
							$uri3=$pre;
							$pre=$nspace_c1[$count2];
							break;
						}
						$count2++;
					}
				}			
				//find if the value of this property is a URI
				$pos2=strrpos($Row[1],"http://");
				//in case of failure print the column as simple literal
				if ($pos2===FALSE){
					$value1="<span class=\"literal\"><span property=\"$pre:$prop\" xmlns:$pre=\"$uri3\">$Row[1]</span>\n";
				}
				//in case of success print it as a URI
				else {
					//split the value in namespace and last part
					$pos2=strrpos($Row[1],'#');
					if($pos2===FALSE){
						$pos2=strrpos($Row[1],'/');
					}
					//prop2 is last part
					$prop2=substr($Row[1],$pos2+1);
					$len1=strlen($prop2);
					//pre2 is the namespace
					$pre2=substr($Row[1],0,-$len1);
					$temp1=$pre2;
					$count2=0;
					$uri4="";
					//search for an appropriate namespace defined in initialization.txt
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre2)==0){
							$uri4=$pre2;
							$pre2=$nspace_c1[$count2];
							break;
						}
					$count2++;
					}
					//in case of failure finding a prefix just print the whole URI
					if (strcmp($uri4,"")==0){
						$uri4=$pre2;
					}
					//experimental...become a local interface of an external sparql endpoint or vice verca
					if(($local==1) && (strrpos($temp1,$base)!==FALSE)){
						
						$uri4=$host."/sembrowser/browser.php?uri=$temp1";
					}
					//encode the uri
					if(strrpos($prop2,'%')===FALSE){
						$fix_uri=urlencode($prop2);
					}
					else{
						$fix_uri=$prop2;
						$prop2=urldecode($prop2);
					}
					
					//print labels
					$sparql_query2="define input:inference \"gnd\" select ?label_moufa where {<$Row[1]> rdfs:label ?label_moufa}";
					$query4="$sparql_endpoint?default-graph-uri=".urlencode($default_graph)."&query=".urlencode($sparql_query2)."&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
					$csv4 = file_get_contents($query4);
					$Data3 = str_getcsv($csv4,"\n");
					foreach($Data3 as &$Row4) {
						$Row4 = str_getcsv($Row4, "\n"); //parse the items in rows 
					}
					$label2=$Row4[0];
					if($label2!="label_moufa"){
						$prop2=$label2;
					}
					//echo "edw eimai $label2";
					//make the content of the value column
					$value1="<span class=\"literal\"><a class=\"uri\" rel=\"$pre:$prop\" xmlns:$pre=\"$uri3\" href=\"$uri4$fix_uri\"><small>$pre2:</small>$prop2</a></span>\n";
				}//end parsing properties
				//print the row without ending it with </tr>, nor the list and cell. That's beacause we may have a value with the same property so we have to put it in the same row by expanding the row and the list 
				///////FURTHER DEVELOPMENT
				//we could change a little the content of $value1 so we could use the browser as an intefrace to a sparql endpoint hosted in another server 
				///////FURTHER DEVELOPMENT
				echo "<tr class=\"$class1\"><td class=\"property\"><a class=\"uri\" href=\"$Row[0]\"><small>$pre:</small>$prop</a></td><td><ul><li>$value1</li>\n";
			}
		else{
			$pos2=strrpos($Row[1],"http://");
			if ($pos2===FALSE){
				$value1="<span class=\"literal\"><span property=\"$pre:$prop\" xmlns:$pre=\"$uri3\">$Row[1]</span>\n";
				}
			else {
				$pos2=strrpos($Row[1],'/');
				$prop2=substr($Row[1],$pos2+1);
				$len1=strlen($prop2);
				$pre2=substr($Row[1],0,-$len1);
				$pre2=$temp1;
				$count2=0;
				$uri4="";
				foreach($nspace_c2 as $ns1){
					if(strcmp($ns1,$pre2)==0){
						$uri4=$pre2;
						$pre2=$nspace_c1[$count2];
						break;
						}
					$count2++;
					}
				if (strcmp($uri4,"")==0){
					$uri4=$pre2;
					}
				//experimental...become a local interface of an external sparql endpoint or vice verca
				if(($local==1) && (strrpos($temp1,$base)!==FALSE)){
					$uri4=$host."/sembrowser/browser.php?uri=$temp1";
				}
				if(strrpos($prop2,'%')===FALSE){
					$fix_uri=urlencode($prop2);
				}
				else{
					$fix_uri=$prop2;
					$prop2=urldecode($prop2);
					}
				//print labels
				$sparql_query2="define input:inference \"gnd\" select ?label_moufa where {<$Row[1]> rdfs:label ?label_moufa}";
				$query4="$sparql_endpoint?default-graph-uri=".urlencode($default_graph)."&query=".urlencode($sparql_query2)."&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
				$csv4 = file_get_contents($query4);
				$Data3 = str_getcsv($csv4,"\n");
				foreach($Data3 as &$Row4) {
					$Row4 = str_getcsv($Row4, "\n"); //parse the items in rows 
				}
				$label2=$Row4[0];
				if($label2!="label_moufa"){
					$prop2=$label2;
				}
				$value1="<span class=\"literal\"><a class=\"uri\" rel=\"$pre:$prop\" xmlns:$pre=\"$uri3\" href=\"$uri4$fix_uri\"><small>$pre2:</small>$prop2</a></span>\n";
				}
			echo "<li>$value1</li>\n";
			}
		$count++;
		
	}
	//the same proccess as above this time for rev links 
	if($bidirection==1){
		//the select query this time using the $uri as an object and not subject
		$query2="$sparql_endpoint?default-graph-uri=&query=select+?property+?value+where+{%3Fvalue+%3Fproperty+%3C$uri%3E%7D&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
		//getting data
		$temp="";
		$count=0;
		$prop="";
		$csv2 = file_get_contents($query2);
		$Data2 = str_getcsv($csv2, "\n"); //parse the rows 
		foreach($Data2 as &$Row) {
			$Row = str_getcsv($Row, ","); //parse the items in rows 
			if ($count==0){
				$count++;
				continue;
			}
			if($Row[0]!=$temp){
				$temp=$Row[0];
				$pos1=strrpos($Row[0],'#');
				list($class1,$class2) = array($class2,$class1);
				if($count!=1){
					echo "</ul></td></tr>";
				}			
				//parsing properties
				if ($pos1===FALSE){
					$pos2=strrpos($Row[0],'/');
					$prop=substr($Row[0],$pos2+1);
					$len1=strlen($prop);
					$pre=substr($Row[0],0,-$len1);
					$count2=0;
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre)==0){
							$uri3=$pre;
							$pre=$nspace_c1[$count2];
							break;
						}
					$count2++;
					}
				}
				else{
					$pos2=strrpos($Row[0],'#');
					$prop=substr($Row[0],$pos2+1);
					$len1=strlen($prop);
					$pre=substr($Row[0],0,-$len1);
					$count2=0;
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre)==0){
							$uri3=$pre;
							$pre=$nspace_c1[$count2];
							break;
						}
					$count2++;
					}
				}			
				$pos2=strrpos($Row[1],"http://");
				if ($pos2===FALSE){
					$value1="<span class=\"literal\"><span property=\"$pre:$prop\" xmlns:$pre=\"$uri3\">$Row[1]</span>\n";
				}
				else {
					$pos2=strrpos($Row[1],'/');
					$prop2=substr($Row[1],$pos2+1);
					$len1=strlen($prop2);
					$pre2=substr($Row[1],0,-$len1);
					$temp1=$pre2;
					$count2=0;
					$uri4="";
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre2)==0){
							$uri4=$pre2;
							$pre2=$nspace_c1[$count2];
							break;
						}
						$count2++;
					}
					if (strcmp($uri4,"")==0){
						$uri4=$pre2;
					}
				//////////////////////////////////////////////////////////////////////////////////////////////////////
				//experimental...become a local interface of an external sparql endpoint or vice verca
					if(($local==1) && (strrpos($temp1,$base)!==FALSE)){
						$uri4=$host."/sembrowser/browser.php?uri=$temp1";
					}
				//the problem is that on a rdfa distiller this will produce wrong data but the data provided via files are correct
				//////////////////////////////////////////////////////////////////////////////////////////////////////
					if(strrpos($prop2,'%')===FALSE){
						$fix_uri=urlencode($prop2);
					}
					else{
						$fix_uri=$prop2;
						$prop2=urldecode($prop2);
					}
					//print labels
					$sparql_query2="define input:inference \"gnd\" select ?label_moufa where {<$Row[1]> rdfs:label ?label_moufa}";
					$query4="$sparql_endpoint?default-graph-uri=".urlencode($default_graph)."&query=".urlencode($sparql_query2)."&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
					$csv4 = file_get_contents($query4);
					$Data3 = str_getcsv($csv4,"\n");
					foreach($Data3 as &$Row4) {
						$Row4 = str_getcsv($Row4, "\n"); //parse the items in rows 
					}
					$label2=$Row4[0];
					if($label2!="label_moufa"){
						$prop2=$label2;
					}
					$value1="<span class=\"literal\"><a class=\"uri\" rev=\"$pre:$prop\" xmlns:$pre=\"$uri3\" href=\"$uri4$fix_uri\"><small>$pre2:</small>$prop2</a></span>\n";
				}//end parsing properties
				echo "<tr class=\"$class1\"><td class=\"property\">$is <a class=\"uri\" href=\"$Row[0]\"><small>$pre:</small>$prop</a> $of</td><td><ul><li>$value1</li>\n";
			}
			else{
				$pos2=strrpos($Row[1],"http://");
				if ($pos2===FALSE){
					$value1="<span class=\"literal\"><span property=\"$pre:$prop\" xmlns:$pre=\"$uri3\">$Row[1]</span>\n";
				}
				else {
					$pos2=strrpos($Row[1],'/');
					$prop2=substr($Row[1],$pos2+1);
					$len1=strlen($prop2);
					$pre2=substr($Row[1],0,-$len1);
					$temp1=$pre2;
					$count2=0;
					$uri4="";
					foreach($nspace_c2 as $ns1){
						if(strcmp($ns1,$pre2)==0){
							$uri4=$pre2;
							$pre2=$nspace_c1[$count2];
							break;
						}
						$count2++;
					}
					if (strcmp($uri4,"")==0){
						$uri4=$pre2;
					}
					//experimental...become a local interface of an external sparql endpoint or vice verca
					if(($local==1) && (strrpos($temp1,$base)!==FALSE)){
						$uri4=$host."/sembrowser/browser.php?uri=$temp1";
					}
					if(strrpos($prop2,'%')===FALSE){
						$fix_uri=urlencode($prop2);
					}
					else{
						$fix_uri=$prop2;
						$prop2=urldecode($prop2);
					}
					//print labels
					$sparql_query2="define input:inference \"gnd\" select ?label_moufa where {<$Row[1]> rdfs:label ?label_moufa}";
					$query4="$sparql_endpoint?default-graph-uri=".urlencode($default_graph)."&query=".urlencode($sparql_query2)."&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
					$csv4 = file_get_contents($query4);
					$Data3 = str_getcsv($csv4,"\n");
					foreach($Data3 as &$Row4) {
						$Row4 = str_getcsv($Row4, "\n"); //parse the items in rows 
					}
					$label2=$Row4[0];
					if($label2!="label_moufa"){
						$prop2=$label2;
					}
					$value1="<span class=\"literal\"><a class=\"uri\" rel=\"$pre:$prop\" xmlns:$pre=\"$uri3\" href=\"$uri4$fix_uri\"><small>$pre2:</small>$prop2</a></span>\n";
				}
				echo "<li>$value1</li>\n";
			}
			$count++;
		}
	}
	echo "</ul></td></tr></table>\n";//end of table creation
	echo "</div>\n";//end of content
	
	
	//////////////////////////////////////////FOOTER///////////////////////////////////////////////////////////////////////////////////
	echo "<div id=\"footer\">\n";
	//links to data dumps about this uri
	echo "<div id=\"ft_t\">\n";
	//raw_data can be defined in language document 
	echo "$raw_data:\n";
	echo "<a href=\"$host/data/$propu.csv\">CSV</a> |RDF (\n";
	echo "<a href=\"$host/data/$propu.ntriples\">N-Triples</a>\n";
	echo "<a href=\"$host/data/$propu.n3\">N3/Turtle</a>\n";
	echo "<a href=\"$host/data/$propu.json\">JSON</a>\n";
	echo "<a href=\"$host/data/$propu.rdf\">XML</a>)\n";
	echo "</div>\n";
	//logo links have to be added
	echo "<div id=\"ft_b\">\n";
	echo "<a href=\"http://virtuoso.openlinksw.com\" title=\"OpenLink Virtuoso\"><img class=\"powered_by\" src=\"../semimages/virt_power_no_border.png\" alt=\"Powered by OpenLink Virtuoso\" /></a>\n";
	echo "<a href=\"http://linkeddata.org/\" title=\"This material is Open Knowledge\"><img alt=\"This material is Open Knowledge\" src=\"../semimages/LoDLogo.gif\"/></a>\n";
	echo "<a href=\"$sparql_endpoint\" title=\"Project SPARQL Endpoint\"><img alt=\"W3C Semantic Web Technology\" src=\"../semimages/sw-sparql-blue.png\" /></a>\n";
	echo "<a href=\"http://www.opendefinition.org\" title=\"This material is Open Knowledge\"><img alt=\"This material is Open Knowledge\" src=\"../semimages/od_80x15_red_green.png\" /></a>\n";
	echo "<span about=\"\" resource=\"http://www.w3.org/TR/rdfa-syntax\" rel=\"dc:conformsTo\" xmlns:dc=\"http://purl.org/dc/terms/\">\n";
	echo "<a href=\"http://validator.w3.org/check?uri=referer\"><img src=\"../semimages/valid-xhtml-rdfa.png\" alt=\"Valid XHTML + RDFa\" height=\"20\"/></a>\n</span>\n";
	echo "</div>";
	////////////////////////////////////////////////////////////////////Can't touch this na na na na, na...na//////////////////////////////////////////////
	echo "<div id=\"ft_ccbysa\">\n";
	echo "<a href=\"http://gr.okfn.org\" title=\"OKFN Local Branch Greece\"><img alt=\"OKFN Local Branch Greece\" src=\"../semimages/okfn.svg\" height=\"64\" /></a>\n";
	echo "<a href=\"http://www.math.auth.gr/\" title=\"Aristotle University of Thessaloniki Math Dept.\"><img alt=\"Aristotle University of Thessaloniki Math Dept.\" src=\"../semimages/math.png\" height=\"60\" /></a>\n";
	////////////////////////////////////////////////////////////////////Can't touch this na na na na, na...na//////////////////////////////////////////////
	///////////////////////////////////////PROJECT INFO HERE//////////////////////////////////////////////////////////////////////////////////////////
	echo "<div>";
	include_once("initialization/footer_content.php");
	echo "</div>";
	echo "</div>\n";
	echo "</div>\n";//end of footer
	////////////////////////////////////////////////////////////////////Can't touch this na na na na, na...na//////////////////////////////////////////////
	echo "<div id=\"developer\">";
	echo "Interface provided via <strong>Babel:the i18n LD Browser version:0.1beta</strong><br />";
	echo "developed by <a href=\"mailto:s.karampatakis@gmail.com\">Sotiris Karampatakis</a>";
	echo "</div>";
	////////////////////////////////////////////////////////////////////Can't touch this na na na na, na...na//////////////////////////////////////////////
	echo "</body>\n";
	echo "</html>\n";
	//that's all folks!!!
?>

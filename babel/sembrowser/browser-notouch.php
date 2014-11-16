<?php 
	//initials
	include 'initialization/el.php';
	include 'initialization/config.php';
	$uri=$_GET["uri"];
	$uri=urldecode($uri);
	$posu=strrpos($uri,'/');
	$propu=substr($uri,$posu+1);
	header('Link: <'.$host.'/data/'.$propu.'.rdf>; rel="alternate"; type="application/rdf+xml"; title="Structured Descriptor Document (RDF/XML format)",
	<'.$host.'/data/'.$propu.'.n3>; rel="alternate"; type="text/n3"; title="Structured Descriptor Document (N3/Turtle format)",
	<'.$host.'/data/'.$propu.'.json>; rel="alternate"; type="application/json"; title="Structured Descriptor Document (RDF/JSON format)",
	<'.$host.'/data/'.$propu.'.atom>; rel="alternate"; type="application/atom+xml"; title="OData (Atom+Feed format)",
	<'.$host.'/data/'.$propu.'.csv>; rel="alternate"; type="text/csv"; title="Structured Descriptor Document (CSV format)",
	<'.$host.'/data/'.$propu.'.ntriples>; rel="alternate"; type="text/plain"; title="Structured Descriptor Document (N-Triples format)",
	<'.$host.'/resource/'.$propu.'>; rel="http://xmlns.com/foaf/0.1/primaryTopic",
	<'.$host.'/resource/'.$propu.'>; rev="describedBy"');
	if(strcmp($uri,"")==0){
		$uri=$default_uri;
		}
	$prefix1=file_get_contents('initialization/prefix.txt');
	$prefix=str_getcsv($prefix1,"\n");
	$count2=0;
	foreach ($prefix as &$ns){
		$ns=str_getcsv($ns,"=");
		$nspace_c1[$count2]=$ns[0];
		$nspace_c2[$count2]=$ns[1];
		$count2++;
		}
	$query="$sparql_endpoint?default-graph-uri=&query=select+?type+?label+?comment?depiction+where+{OPTIONAL{%3C$uri%3E+$type_prop+%3Ftype}.+OPTIONAL{%3C$uri%3E+$label_prop+%3Flabel}.+OPTIONAL+{%3C$uri%3E+$comment_prop+%3Fcomment}.+OPTIONAL+{%3C$uri%3E+$depiction_prop+%3Fdepiction}}&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
	$csv = file_get_contents($query);
	$Data = str_getcsv($csv,"\n");
	foreach($Data as &$Row1) {
		$Row1 = str_getcsv($Row1, ","); //parse the items in rows 
	}
	$type=$Row1[0];
	$label=$Row1[1];
	$comment=$Row1[2];
	$depiction=$Row1[3];
	$class1="even";
	$class2="odd";
	$temp="";
	$count=0;
	$prop="";
	$query2="$sparql_endpoint?default-graph-uri=&query=select+?property+?value+where+{%3C$uri%3E+%3Fproperty+%3Fvalue%7D&should-sponge=&format=text%2Fcsv&timeout=0&debug=on";
	//getting data
	$csv2 = file_get_contents($query2);
	$Data2 = str_getcsv($csv2, "\n"); //parse the rows 
	
	/////////////////////////////////////////////////////////////////////////////////////////
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML+RDFa 1.0//EN\"
    \"http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"
    xmlns:foaf=\"http://xmlns.com/foaf/0.1/\"
    xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
    version=\"XHTML+RDFa 1.0\" xml:lang=\"el\">\n";
	echo "<head profile=\"http://www.w3.org/1999/xhtml/vocab\">\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"../semstyle/mystyle.css\" />\n";
	echo "\t<title>$about:$propu</title>\n";
	echo "\t<meta charset=\"utf-8\">\n";
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
	//////////////////////////////////////HEADER//////////////////////////////////////////////////
	echo "<div id=\"header\">\n";
	echo "<div id=\"hd_l\">\n";
	//About: page
	echo "<h1 id=\"title\">$about:";
	echo "<a href=\"$uri\">";
	if($opt_label==1){
		echo $label;
		}
	else{
		echo $propu;
		}
	echo "</a></h1>\n";
	
	echo "<div class=\"page-resource-uri\">\n";
	$pos5=strrpos($type,'/');
	$prop5=substr($type,$pos5+1);
	$len5=strlen($prop5);
	$pre5=substr($type,0,-$len5);
	echo "$entity : <a href=\"$type\">$prop5</a>\n";
	echo "$named_graph : <a href=\"$default_graph\">$default_graph</a>\n";
	echo "$data_space : <a href=\"$host\">$host</a>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "<div id=\"hd_r\"><a href=\"$project_homepage\" title=\"$about $project_name\"><img src=\"$logo\" height=\"64\" alt=\"$about $project_name\"></a>";
	echo "</div>\n";
	
	
	echo "</div>\n";
	echo "<div id=\"content\">\n";
	echo "<p>$comment</p>\n";
	//Properties table
	echo "<table class=\"description\">\n";
	echo "<th id=\"property\">$property</th><th id=\"value\">$value</th>\n";
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
			echo "</ul></td></tr>";
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
				$fix_uri=urlencode($prop2);
				$value1="<span class=\"literal\"><a class=\"uri\" rel=\"$pre:$prop\" xmlns:$pre=\"$uri3\" href=\"$uri4$fix_uri\"><small>$pre2:</small>$prop2</a></span>\n";
				}
			
			
			//end parsing properties
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
				$fix_uri=urlencode($prop2);
				$value1="<span class=\"literal\"><a class=\"uri\" rel=\"$pre:$prop\" xmlns:$pre=\"$uri3\" href=\"$uri4$fix_uri\"><small>$pre2:</small>$prop2</a></span>\n";
				}
			echo "<li>$value1</li>\n";
			}
		$count++;
		
	}
	echo "</ul></td></tr></table>\n";
	
	
	//end of table
	echo "</div>\n";//end of content
	//////////////////////////////////////////FOOTER///////////////////////////////////////////////////////////////////////////////////
	echo "<div id=\"footer\">\n";
	echo "<div id=\"ft_t\">\n";
	echo "$raw_data:\n";
	echo "<a href=\"$host/data/$propu.csv\">CSV</a> |RDF (\n";
	echo "<a href=\"$host/data/$propu.ntriples\">N-Triples</a>\n";
	echo "<a href=\"$host/data/$propu.n3\">N3/Turtle</a>\n";
	echo "<a href=\"$host/data/$propu.json\">JSON</a>\n";
	echo "<a href=\"$host/data/$propu.rdf\">XML</a>)\n";
	echo "</div>\n";
		
	echo "</div>\n";//end of footer
	echo "</body>\n";
	echo "</html>\n";
?>
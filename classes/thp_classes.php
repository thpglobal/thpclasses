<?php
// thp_classes contains four basic object classes for formatting pages using the PureCSS library
// Page -- Sends the headers, starts the body, sends the navbar, main title and control icons
// Filters -- Sets up dropdowns that feed into the $_SESSION object
// Table -- Sets up and outputs a 2d table - also backing it up into $_SESSION["contents"];
// Form -- Sets up an editing form with validation
require(__DIR__."/../../includes/thpsecurity.php"); // this version sets up up PDO object and global permission variables
// START CLASS PAGE
class Page {
	public $datatable = FALSE;
	public $addStickyHeader = TRUE;
	public $css=array("/static/pure.thp.css","/static/thp.form.css"); // defaults used by thpmne
	public $time_start; // used to measure length for process
	public $links=array("print"=>"'javascript:window.print();'");
	public $hints=array("print"=>"Print this page");
	public $appendTitle='';
	public function debug($message,$values) {
		echo("<p>$message".":"); print_r($values); echo("</p>\n");
	}

	public function datatable(){
		$this->datatable=TRUE;
	}
	public function disableStickyHeader(){
		$this->addStickyHeader=FALSE;
	}

/* dynamic property setter/getter for this class */
	public function get($prop){
		if(isset($this->$prop)){
			return $this->$prop;
		}
		return NULL;
	}
	public function set($prop, $value){
		if(isset($this->$prop)){
			$this->$prop = $value;
		}
	}
	
	public function menu(){
		$menu=$_SESSION["menu"];
		if(isset($_SESSION["menu"]) and sizeof($menu)>0) { 
			echo("<div class='pure-menu pure-menu-horizontal hidden-print'>\n\t<ul class='pure-menu-list'>\n");
			foreach($menu as $key=>$links){
				if(is_array($links)) {
					echo("\t\t<li class='pure-menu-item pure-menu-has-children pure-menu-allow-hover'>\n");
					echo("\t\t\t<a href='#' class='pure-menu-link'>$key</a>\n\t\t\t<ul class='pure-menu-children'>\n");
					foreach($links as $tag=>$link){
						echo("\t\t\t<li class='pure-menu-item'><a class='pure-menu-link' href='$link'>$tag</a></li>\n");
					}
					echo("\t\t</ul>\n\t</li>\n");
				}else{
					echo("\t\t<li class='pure-menu-item'><a class='pure-menu-link' href='$links'>$key</a></li>\n");
				}
			}
			echo("\t</ul>\n</div>\n");
		}
	}

	public function start($title="THP",$lang="en"){
		$_SESSION["datatable"]=$this->datatable; // save for access by Table class
		foreach($_GET as $key=>$value) $_SESSION[$key]=$value;
		$this->time_start=microtime(true);
		echo("<!DOCTYPE html>\n<html lang=$lang>\n<head>\n<title>$title</title>\n");
		echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/pure/1.0.0/pure-min.css'>\n");
		echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/pure/1.0.0/grids-responsive.css'>\n");
		echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>\n");
		foreach($this->css as $css) echo("<link rel='stylesheet' href='$css'>\n");
		if($this->addStickyHeader){echo("<script src='/static/irStickyHeader.js'></script>\n");}
		if($this->datatable=="1"){ // Additional setup for using DataTables
?>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/jquery.dataTables.min.css'>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#datatable tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" size=4 placeholder="'+title+'" />' );
    } );
 
    // DataTable
    var table = $('#datatable').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
} );
</script>
<?php
        }
        echo("<meta charset='utf-8'>\n");
        echo("</head>\n<body>\n");
		$this->menu();
        echo("<div class=container>\n");
        echo("<h1>$title ");
        foreach($this->links as $key=>$link) {
            $hint=$this->hints[$key];
            echo("<a href=$link class='fa fa-$key' title='$hint'></a>\n");
        }
        echo($this->appendTitle."</h1>\n");
		$reply=$_SESSION["reply"];
		if($reply>''){
			unset($_SESSION["reply"]); 
				$color="green";
				if(substr($reply,0,5)=="Error") $color="red";
			echo("<p style='text-align:center;color:white;background-color:".$color."'>$reply</p>\n");
		}
	}
	public function icon($type="edit",$link="/edit",$hint="Edit this record"){
		$this->links[$type]=$link;
		$this->hints[$type]=$hint;
	}
	public function toggle($name,$on_msg='On',$off_msg='Off'){
		$now=$_SESSION[$name];
		if($now<>'off') $now='on'; // default is ON
		$then=($now=='on' ? 'off' : 'on');
		$this->appendTitle.="<a class='fa fa-toggle-$now' href='?$name=$then'></a> ";
		$this->appendTitle .= ($now=='on' ? $on_msg : $off_msg) ;
	}
	
	## If addStickyHeader variable is set to true, fire up the sitcky event. Else don't do anything. 
	## Pages which don't need this sticky header can set this variable to false to avoid this option. 
	public function fireStickyHeader(){
		if( $this->addStickyHeader ) {
			echo "<script>
			// self executing function
			(function() {
				var tables = document.getElementsByTagName('table');
				//fire for all tables in the page
				for(i = 0;i < tables.length; i++){
					lrStickyHeader(tables[i]);
				}
			})();
			</script>\n";
		}
	}
	public function end(){
		$time=microtime(true)-($this->time_start);
		echo("<p><i>Run time: $time</i></p>\n");
		echo("</div>\n");
		$this->fireStickyHeader();
        echo("</body></html>\n");
    }
}
// END CLASS PAGE
// CLASS FILTER - dropdowns that - on change - restart the page and set $_SESSION["name"];
class Filter {
	protected $db;
	public $width=4; // Filter denomiator, eg normally 1/4 of screen
	public $showOffLabel=false;
	
	public function get($prop){
    	if(isset($this->$prop)){
	    	return $this->$prop;
	    }
        return NULL;
    }
	public function set($prop, $value){
    	if(isset($this->$prop)){
	       $this->$prop = $value;
    	}
    }
    public function start($db=NULL){
        echo("<div class=pure-g>\n");
		$this->db=$db;
    }
    public function end(){
        echo("</div>\n");
    }
	public function range($name,$n1=1, $n2=4){
		if(!( ($_SESSION[$name]>=$n1) and ($_SESSION[$name]<=$n2) )) $_SESSION[$name]=$n2;
		for($i=$n1;$i<=$n2;$i++) $array[$i]=$i;
		return $this->pairs($name,$array);
	}
	public function toggle($name,$on_msg='On',$off_msg='Off'){
		$now=$_SESSION[$name];
		if($now<>'off') $now='on';
		$then=($now=='on' ? 'off' : 'on');
		echo("<div class='pure-u-1 pure-u-md-1-4'>$name: <a class='fa fa-3x fa-toggle-$now' href='?$name=$then'></a>");
		echo( ($now=='on' ? $on_msg : $off_msg)."</div>");
		return $now;
	}
	/* switch version of the toggle, shows both on/off labels */
	public function switchToggle($name,$on_msg='On',$off_msg='Off'){
		$now=$_SESSION[$name];
		if($now<>'off') $now='on';
		$then=($now=='on' ? 'off' : 'on');
		echo("<div class='pure-u-1 pure-u-md-1-4'>$name: ". ($this->showOffLabel ? $off_msg : '') . 
			"<a class='fa fa-3x fa-toggle-$now' href='?$name=$then'></a>");
		echo($on_msg."</div>");
		return $now;
	}
	public function warn($msg='Error.'){
		echo("<div class='pure-u-1 pure-u-md-1-4' style='background-color:red !important; color:white !important;'>$msg</div>\n");
	}
	public function query($name,$query){
		if($this->db==NULL) Die("You forgot to pass $db in the start method.");
		return $this->pairs($name, $this->db->query($query)->fetchAll(PDO::FETCH_KEY_PAIR) );
	}
	public function table($name,$where=''){
		$where_clause=($where=='' ? "" : "where $where");
		return $this->query($name,"select id,name from $name $where_clause order by 2");
	}
	public function pairs($name,$array,$all='(All)'){
		if (!isset($_SESSION[$name])) $_SESSION[$name] = "0";
		echo "<form class='pure-form pure-u-1 pure-u-md-1-".$this->width."'>" .
			"<div class='form-group'><label for='$name'>".ucfirst($name).":&nbsp;</label>" .
			"<select id='$name' name=$name onchange=this.form.submit(); >\n";
		if($all>'') echo("<option value=0>$all\n");
		foreach($array as $key=>$value) { // default to first if required
			if(($all=='') and ($_SESSION[$name]==0)) $_SESSION[$name]=$key;
			echo("<option value=$key");
			if($key==$_SESSION[$name]) echo(" SELECTED");
			echo(">$value\n");
		}
		echo("</select></div></form>\n");
		return $_SESSION[$name];
	}	
}
// END CLASS FILTER
// CLASS FORM - EDIT A RECORD
class Form {
	protected $db;
	private $div1="<div class='pure-control-group'>\n<label for=";
	public $data=array();
	public $hidden=array("id");
	public $ignore=array();
	private function debug($name,$item){
		if($_SESSION["debug"]) {
			echo("<p>$name: "); print_r($item); echo("</p>\n");
		}
	}
	
	public function start($db=NULL,$action="/update"){
		$this->db=$db; // reference database connection
		echo("<form class='pure-form pure-form-aligned' method='post'");
		if($action>'') echo (" action='$action'");
		echo(">\n<Fieldset>\n");
	}
	public function end($submit="Save Data"){
		echo("\n\n<div class='pure-controls'>".
		'<button type="submit" class="pure-button pure-button-primary">'.$submit.'</button>'.
		"</div>\n</fieldset>\n</form>\n");
	}
	public function hidden($array) {
		$this->hidden=$array;
	}
	public function ignore($array){
		$this->ignore=$array;
	}
	public function data($array) { // these are the existing values
		$this->data=$array;
	}
	public function toggle($name) {
		echo($this->div1."'$name'>$name:</label>");
		echo('<input type=hidden name='.$name.' value=0>');
		echo('<label class=switch><input type=checkbox name='.$name);
		if($this->data[$name]>0) echo(" checked");
		echo ("><span class=slider></span></label></div>\n");
	}
		
	public function num($name,$min=NULL,$max=NULL){
		$value=$this->data[$name];
		if($value=='') $value=0;
		$label=ucwords($name);
		if($min<>NULL) $label .= "$min to $max";
		echo($this->div1."'$name'>".ucwords($name).":</label>");
		echo("<input type=number name='$name' value='$value'");
		if($min<>NULL) echo(" min='$min'");
		if($max<>NULL) echo(" max='$max'");
		if($min<>NULL) echo("><span class=status></span");
		echo("></div>\n");
	}
	public function text($name,$rename='',$minlength=0){
		$label=($rename>'' ? $rename : $name);
		echo($this->div1."'$name'>".ucwords($label).":</label>");
		echo("<input type=text name='$name' value='".$this->data[$name]."'");
		if($minlength>0) echo(' required><span class=status></span');
		echo("></div>\n");
	}
	public function date($name,$required=0){ // This restricts daterange to mindate/maxdate if set
		if(!isset($_SeSSION["lastdate"])) $_SESSION["lastdate"]=date("Y-m-d");
		if(!isset($this->data[$name])) $this->data[$name]=$_SESSION["lastdate"];
		echo($this->div1."'$name'>".ucwords($name).":</label>");
		echo("<input type=date name='$name' value='".$this->data[$name]."'");
		if(isset($_SESSION["mindate"])) echo(" min='".$_SESSION["mindate"]."'");
		if(isset($_SESSION["maxdate"])) echo(" max='".$_SESSION["maxdate"]."'");		
		if($required) echo (' required');
		echo("><span class=status></span></div>\n");
	}
	public function textarea($name,$rename='',$required=0){
		$label=($rename>'' ? $rename : $name);
		echo($this->div1."'$name'>".ucwords($label).":</label>");
		echo("<textarea name=$name rows=5 cols=60");
		if($required) echo(" REQUIRED");
		echo(">".$this->data[$name]."</textarea>\n");
		if($required) echo("<span class=status></span>");
		echo("</div>\n");
	}
	public function hide($name,$value){
		echo("<input type=hidden name='$name' value='$value'>\n");
	}
	public function pairs($name,$array,$required=0){
	if($_SESSION["debug"]) echo("<p>$name=>".$this->data["name"]."</p>\n");
        $requiredAttr=($required) ? ' required ' : '';
        //HtML5 requires required value to be empty (not zero) for validation
        $requiredVal=($required) ? '' : 0;
        echo($this->div1."'$name'>".ucwords($name).":</label>");
        echo("<select name='$name' $requiredAttr>\n<option value='$requiredVal'>(Select)\n");
        foreach($array as $key=>$value){
            echo("<option value='$key'");
            if($key==$this->data[$name]) echo(" selected");
            echo(">$value\n");
        }
        echo("</select>");
        if($required){echo "<span class=status></span>";}
        echo("</div>\n");
    }
	public function query($name,$query,$required=0){
		$pdo_stmt=$this->db->query($query);
		if(!is_object($pdo_stmt)) Die("Fatal Error - bad query - $query \n");
		$this->pairs($name,$pdo_stmt->fetchAll(PDO::FETCH_KEY_PAIR),$required);
	}
	public function record($table,$id){ // goes inside start and end
		// Also can create a new record if id==0
		// First pull in the list of field meta data
		if($id=='') $id=0;
		$this->hide("table",$table);
		$pdo_stmt=$this->db->query("select * from $table where id='$id'");
		if(!is_object($pdo_stmt)) Die("Fatal Error - bad query - $query \n");
		$this->data = $pdo_stmt->fetch(PDO::FETCH_ASSOC);
		
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index)
		{ $meta[$column_index] = $pdo_stmt->getColumnMeta($column_index);}
		
		$this->debug("Meta",$meta);
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index) {
			$name=$meta[$column_index]["name"];
			$type=$meta[$column_index]["native_type"];
			$value=$this->data[$name];
			if($name=="id"){
				$this->hide($name,$id);
			}elseif(isset($this->hidden[$name])){
				$this->hide($name,$this->hidden[$name]);
			}elseif($name=='User_Email'){
				$this->hide($name,strtolower($_SERVER["USER_EMAIL"]));
			}elseif(substr($name,-3)=="_ID"){
				$subtable=strtolower(substr($name,0,-3));
				$this->query($name,"select id,name from $subtable order by 2");
			}elseif($type=="TINY") {
				$this->toggle($name);
			}elseif($type=="LONG") {
				$this->num($name);
			}elseif($type=="BLOB") {
				$this->textarea($name);
			}elseif($type=='DATE'){
				$this->date($name);		
			}elseif(!in_array($name,$this->ignore)) {
				$this->text($name);
			}
		}
	}
} // END OF CLASS FORM
// START CLASS TABLE
//
class Table { // These are public for now but may eventually be private with setters
	protected $db; // database connection
	public $contents=array(array()); // main 2d array
	public $rowspan=0; // If>0, then start rowspan with column this many columns
	public $backmap=array(); // Create backpointers to the array after pivot
	public $extra=array(); // extra headers
	public $ntext=1; // number of columns to not be formatted
	public $groups=array(); // headers
	public $showGroupID=TRUE;
	public $extraheader=""; // Optional Extra headers string
	public $infocol=array(); // Definitions of column headers
	public $inforow=array(); // Definitions of rows
	public $classes=array(); // Used for specially coloring of rows
	public $href="";
	public $dpoints=0; // Decimal points
	public function start($db){
		$this->db=$db;
	}
	
	/* dynamic property setter/getter for this class */
	public function get($prop){
    	if(isset($this->$prop)){
	    	return $this->$prop;
	    }
        return NULL;
        }
	public function set($prop, $value){
    	if(isset($this->$prop)){
	       $this->$prop = $value;
    	}
        }
    
    public function info($definition){ // return a string function with info symbol and title
	    return "<span title='$definition' class='fa fa-info-circle'></span>";
    }
	public function rowspan($n=2){ // set number of columns to include in rowspan
		$this->rowspan=$n;
	}
	public function sumcols($col1=2,$row1=1){ // sum the last columns starting with $n to a new Total column, startin with row 1
		$nrows=sizeof($this->contents);
		$ncols=sizeof($this->contents[0]);
		$this->contents[0][$ncols]="Total";
		for($i=$row1;$i<$nrows;$i++){
			$this->contents[$i][$ncols]=0;
			for($j=$col1;$j<$ncols;$j++) $this->contents[$i][$ncols] += $this->contents[$i][$j];
		}
	}
	
	/* It just returns the result of a query,
	 * Donesn't updates contents variable 
	 * Helpful to get the result of any queries
	 */
	public function fetchRecords($query){
		if(empty($query)){return false;}
		$pdo_stmt=$this->db->query($query);
		if(!is_object($pdo_stmt)) echo("<div> Error: bad query: $query.</div>");
		$data=$pdo_stmt->fetch(PDO::FETCH_ASSOC);
		return $data;
    }
		
	public function query($query){ // load results of a query into the grid
		$pdo_stmt=$this->db->query($query);
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index)
			{
				$meta = $pdo_stmt->getColumnMeta($column_index);
				$this->contents[0][$column_index]=$meta["name"];
			}
		while($row = $pdo_stmt->fetch(PDO::FETCH_NUM)) $this->contents[]=$row;
	}
	public function column($id_col,$dest_col,$array){
		if($_SESSION["debug"]) {echo("<p>Column:"); print_r($array); echo("</p>\n");}
		if(sizeof($this->backmap)==0) $this->backmap($id_col);
		foreach($array as $key=>$value){
			$j=substr($key,-1); // Get the digit after the_
			$id=substr($key,0,-2); // Get the indicator name before the _
			if(key_exists($id,$this->backmap)) $this->contents[$this->backmap[$id]+$j-1][$dest_col]=$value;
		}
	}
	public function map($id_col,$dest_col,$array){
		// maps with no reference to rowspan
		$nrows=sizeof($this->contents);
		for($i=1;$i<$nrows;$i++) {
			$id=$this->contents[$i][$id_col]; 
			$map=str_replace(".","_",$id);
//			echo(" $i $id $map;");
			if(!empty($array) and key_exists($map,$array)) $this->contents[$i][$dest_col]=$array[$map];
		}
	}
	public function map_query($id_col,$dest_col,$query) {
		// This will map multiple columns directly from the array
		if(sizeof($this->backmap)==0) $this->backmap($id_col);
		if($_SESSION["debug"]) echo("<p>Backmap".print_r($this->backmap,TRUE)."</p>\n");
		if($_SESSION["debug"]) echo("<p>Map Query $query</p>\n");
		$pdo_stmt=$this->db->query($query);
		$nrows=sizeof($this->contents);
		while($row = $pdo_stmt->fetch(PDO::FETCH_NUM)) {
			$n=sizeof($row);
			$i=$this->backmap[$row[0]]; // into which row do we plant this?
			if($_SESSION["debug"]) echo("<p>Map n $n i $i ".print_r($row,TRUE)."</p>\n");
			if($i>0 and $i<$nrows) {
				for($j=1;$j<$n;$j++) $this->contents[$i][$j+$dest_col-1]=$row[$j];
			}else{$this->errormsg.="<br>Map_query error:".print_r($row,TRUE);}
		}
	}	
				
	public function loadrows($result) { // load from the output of a pdo query
		while($row=$result->fetch(PDO::FETCH_NUM)) $this->row($row);
	}
	public function dump() {
		print_r($this->contents);
	}
	public function record($table,$id){ // display one record horizontally
		$this->contents[0]=array("Field","Value");
		$pdo_stmt=$this->db->query("select * from $table where id='$id'");
		if(!is_object($pdo_stmt)) Die("</div>Fatal Error: bad query in Table: $query.");
		$data=$pdo_stmt->fetch(PDO::FETCH_ASSOC);
		foreach($data as $key=>$value) {
			$row[0]=$key; $row[1]=$value;
			$this->contents[]=$row;
		}
	}
    public function header($row) {
        $this->contents[0]=$row;
    }
	public function backmap($id_col) {
		//Identify the first row of a rowspan set of rows
		$nrows=sizeof($this->contents); // how many rows total?
		$last="";
		for($i=1;$i<$nrows;$i++) {
			$id=$this->contents[$i][$id_col];
			if($id<>$last) {
				$last=$id;
				$this->backmap[$id]=$i;
			}
		}
		if($_SESSION["debug"]) {echo("<p>Backmap:");print_r($this->backmap);echo("</p>\n");}
	}
    public function row($row){
        $this->contents[]=$row;
    }
	public function ntext($n=1){ // set the number of text columns
		$this->ntext=$n;
	}
    public function groups($row,$showGroupID=TRUE) {
        $this->groups=$row;
        $this->showGroupID=$showGroupID;
    }
    public function inforow($array) {
        $this->inforow=$array;
    }
    public function infocol($array) {
        $this->infocol=$array;
    }
    
// SUM UP THE $contents from column $col1 onwards (counting from zero)
	public function totals($col1=2,$row1=1){ // basically an alias
		$this->sumrows($col1);
	}
	public function sumrows($col1=2,$row1=1){
		$nrows=sizeof($this->contents);
		$ncols=sizeof($this->contents[0]);
		for($j=0;$j<$col1;$j++) $sums[$j]='';
		$sums[$col1-1]="TOTALS:";
		for($j=$col1;$j<$ncols;$j++){
			for($i=$row1;$i<$nrows;$i++) $sums[$j] += $this->contents[$i][$j];
		}
	    $this->contents[]=$sums;
    }
    
    ### replace totals with dash for non-numeric columnns
    ### call this method after calling totals or sumrows
	public function replaceNonNumericSums($ntextN,$replaceStr=' — '){
		$lastLine=array_pop($this->contents);
		$lastLine[1]=count($this->contents)-1;
		for($i=2;$i<$ntextN;$i++){
   			$lastLine[$i]=$replaceStr;
		}
		$this->contents[]=$lastLine;
	}
    
	// Link any foreign keys to their dependent table name field
	public function smartquery($table,$where="",$yearfilter=""){ // option to limit a date to a year
		$yearclause='';
		$whereclause='';
		$from=" from $table a";
		$alias=97; // ascii for lowercase a
		$pdo_stmt=$this->db->query("select * from $table limit 0"); // we need the names of the fields
		$query="select ";
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index) {
			$name=$pdo_stmt->getColumnMeta($column_index)["name"];
			if($yearfilter>"" and substr($name,-5)=="_Date") $yearclause=" year($name)='$yearfilter' ";
			if(substr($name,-3)=="_ID") {
				$alias++; // go to the next lowercase letter
				$from .=" left outer join ".strtolower(substr($name,0,-3))." ".chr($alias)." on a.$name=".chr($alias).".id ";
				$query .= chr($alias).".name as ".substr($name,0,-3).", ";
			}else{
				$query .= "a.$name, ";
			}
		}
		if($where>"" or $yearclause>"") $whereclause=" where ";
		if($where>"") $whereclause.=$where;
		if($where>"" and $yearclause>"") $yearclause=" and $yearclause";
		$query=substr($query,0,-2).$from.$whereclause.$yearclause." order by 1 desc limit 1500";
		if($_SESSION["debug"]) echo("<p>Debug Smart $query</p>\n");
		$this->query($query);		
	}
	
	### Add extra joins with non-foreign key tables to achieve complex queries
    ### Pass extra joins, selects, group bys with the $extraJoin parameter
 	### Link any foreign keys to their dependent table name field
	public function smartquery2($table,$where="",$extraJoin=array()){
		$from=" from $table a";
		$alias=97; // ascii for lowercase a
		$pdo_stmt=$this->db->query("select * from $table limit 0"); // we need the names of the fields
		$query="select ";
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index) {
			$name=$pdo_stmt->getColumnMeta($column_index)["name"];
			if(substr($name,-3)=="_ID") {
				$alias++; // go to the next lowercase letter
				$from .=" left outer join ".strtolower(substr($name,0,-3))." ".chr($alias)." on a.$name=".chr($alias).".id ";
				$query .= chr($alias).".name as ".substr($name,0,-3).", ";
			}else{
				$query .= "a.$name, ";
			}
		}
		### Add extra joins if applicable
		if( !empty($extraJoin) ) {
            $query = str_replace($extraJoin['after'],$extraJoin['after'].$extraJoin['select'],$query);
            $from .= $extraJoin['joins'];
            $query = substr($query,0,-2).$from.$where. $extraJoin['groupby'] . " order by 1 desc limit 1500";
		} else {
             $query=substr($query,0,-2).$from.$where." order by 1 desc limit 1500";
        }
		if($_SESSION["debug"]) echo("<p>Debug Smart $query</p>\n");
		$this->query($query);
	}
	
	// Pivot data into the table after column j=$ni, for k=$nc groups of m=$nd columns
	public function pivot($query,$ni,$nc,$nd,$nsums=0){
		// $ni indicates the position of the rowspan field, typically column 3 counting from zero
		// $nsums adds fields beyond rowspan before the pivot used for summing (eg, 3)
		// the math here is really complicated as we parse one single row into rowspan rows,
		// and then go back and sum it up
		// the "pointer" to a disaggregate is $ni+$nsums+($nd*$j)+$i
		// it now also fills the backmap array
		if(!($result=$this->db->query($query))) Die($query);
		while($row=$result->fetch(PDO::FETCH_NUM)){ // fold each row into rowspan rows
//			if($_SESSION["debug"]) {echo("<p>Debug pivot $ni");print_r($row);echo("</p>\n");}
			$n=$row[$ni]; // rowspan
			for($i=1;$i<=$n;$i++){
				$line=array(); // first, empty it
				for($j=0;$j<$ni;$j++) $line[$j]=$row[$j]; // set up the pre-fold columns
				for($j=0;$j<$nc;$j++) $line[$ni+$j]=$row[$ni+$nsums+$i+($nd*$j)]; // number of disaggregated columns
				$this->row($line);
			}
			$sump=$row[$ni+1]; // Do we sum anything for this indicator?
			if($_SESSION["debug"]) echo("<p>Above loop $nsums $sump ".$row[$ni+3]."</p>\n");
			if($nsums and (($sump>0) or ($sump<0))){ // do we add summing rows?
				$label=$row[$ni+3]; // L8 is the label for the sum
				if($label=="") $label="Total # participants";
				for($j=0;$j<$ni;$j++) $line[$j]=$row[$j];
				$line[$ni]=$label;
				for($j=1;$j<$nc;$j++) { // loop through columns to be summed
					$participants=0;
					if($sump>0) { // normal sum
						for($i=1;$i<=$sump;$i++) $participants += $row[$ni+$nsums+($nd*$j)+$i];
					}elseif($sump==-2) {
						$participants=$row[$ni+($nd*$j)+4]-$row[$ni+($nd*$j)+5];
					}
					$line[]=$participants; // append to line
				}
				if($_SESSION["debug"]) print_r($line);
				$this->row($line); // append to grid
				$sumw=$row[$ni+2];
				if($sumw>0) { // sum up number of workshop for SumW>0
					for($j=0;$j<$ni;$j++) $line[$j]=$row[$j];
					$line[$ni]="Total # workshops";
					for($j=1;$j<$nc;$j++) { // loop through columns to be summed							
						$workshops=0;
						for($i=$sumw;$i<=$n;$i++) $workshops += $row[$ni+$nsums+($nd*$j)+$i];
						$line[]=$workshops;
					}
					$this->row($line); // append to grid
				}
			}
		}
		$this->rowspan($ni-1); 
	}
	// Turn columns into input fields in a range of columns
	public function edit_cols($id_col,$v_col,$n_col){
		$lastid=""; // last id rowspan
		for($i=1;$i<sizeof($this->contents);$i++){
			$id=$this->contents[$i][$id_col];
			if($id<>$lastid){
				$j=1;
				$lastid=$id;
			}
			for($k=0;$k<$n_col;$k++){
				$v=$this->contents[$i][$v_col+$k];
				$field="V{$j}-{$k}-".str_replace(".","_",$id);
				$this->contents[$i][$v_col+$k]="<input name='$field' size=5 value='$v'>";
			}
			$j++;
		}
	}
	// SHOW THE TABLE WITH NO HEADER
	// Needed when you output two tables and only one has a sticky header
	public function show_noheader($href='') {
		echo("<table class='pure-table $striped pure-table-bordered'>\n");
		foreach($this->contents as $row) {
			echo("<tr>");
			$n=sizeof($row);
			if($href>'') echo("<td><a href='$href'>".$row[0]."</a></td>\n");
			if($href=='') echo("<td>".$row[0]."</td>");
			for($i=1;$i<$n;$i++) echo("<td>".$row[$i]."</td>");
			echo("</tr>\n");
		}
		echo("</table>\n");
	}
	// SHOW THE TABLE - Including the id column on hrefs, but do skip the groups column
	public function show($href=''){
        // Set parameters appropriate to various options
	    $ngroups=sizeof($this->groups); // Option to group rows with subheaders
	    $ninforow=sizeof($this->inforow); // Option to show info symbols at start of row
		$nclasses=sizeof($this->classes); // Are there special row colors?
	    $nstart=($ngroups>0 ? 1 : 0); // If groups, then don't display col 0
	    $group=-99;
		$nrows=sizeof($this->contents);
	    $ncols=sizeof($this->contents[0]);
		$nrowspan=$this->rowspan;
		// If we're doing rowspan, set up the array
		if($nrowspan) {
			$first="";
			$r=1; // keep your finger on first row in group
			for($i=1;$i<$nrows;$i++){
				if($this->contents[$i][$nstart]==$first){
					$rowspan[$r]++; $rowspan[$i]=0;
				}else{
					$r=$i; $first=$this->contents[$r][$nstart]; $rowspan[$r]=1;
				}
			}
		}
		// Start outputing the table
		$striped=($nclasses>0 ? "" : "pure-table-striped");
		$tid=($_SESSION["datatable"] ? "id='datatable'" : "");
		echo("<table $tid class='pure-table $striped pure-table-bordered'>\n<thead>\n");
		if(strlen($this->extraheader)>0) echo($this->extraheader);
		foreach($this->contents as $i=>$row) {
			if($i==0){ // column headers - replace underscores with blanks to look nicer
		        for($j=$nstart;$j<$ncols;$j++){
		        	if( isset($this->infocol[$row[$j]]) ){ $infoc=$this->info($this->infocol[$row[$j]]);}else{$infoc='';}
		        	echo("<th>".str_replace("_"," ",$row[$j])."$infoc</th>");
		        }
		        echo("</tr>\n</thead>\n<tbody>\n");
		    }else{ // regular rows (perhaps preceded by a full-width bar?
				if($ngroups>0) { // output a bar based on column zero if requested
		            $g=$row[0];
		            if($g>$group) {
		                $group=$g;
		                echo("<tr><th colspan=".($ncols-1).">". (($this->showGroupID) ? "{$group}. " : '') .$this->groups[$group]."</th></tr>\n");
		            }
		        }
				$tag=$row[$nstart]; // if there is an id here, this is it
				$class=$this->classes[$tag]; // is there a special class definition for this row?
				if($class>'') $class=" class=$class";			
			    echo("<tr$class>"); // Start outputing rows
				// Here is where all the variability comes in
				// if there are rowspans we send out the that many columns only at start of a rowspan group
				if( ($nrowspan==0) or ($rowspan[$i]>0)){ // do we output the first bits of this row or not?
					$rs=($rowspan[$i]>1 ? " rowspan=".$rowspan[$i] : ""); // is there a rowspan clause in the TDs?
					if($ninforow>0) $info=$this->info($this->inforow[$row[$nstart]]); // Does the row include an info icon?
					if($href>'') {
						echo("<td$rs><a href='".$href.$row[$nstart]."'>".$info.$row[$nstart]."</a></td>"); // a link?
					}else{ echo("<td$rs>".$info.$row[$nstart]."</td>");} // or no link
					// are there more columns within the rowspan?
					if($nrowspan>1) for($j=$nstart+1;$j<($nstart+$nrowspan);$j++) echo("<td$rs>$row[$j]</td>");
				}
				$nstart2=($rowspan>1 ? $nstart+$nrowspan : $nstart+1);
		        for($j=$nstart2;$j<$ncols;$j++) {
					$v=$row[$j];
					if ( is_numeric($v) and ($j>=($this->ntext)) ) $v=number_format($v,$this->dpoints);
					echo("<td>$v</td>");
				}
                echo("</tr>\n");
		    }
		}
		echo("</tbody>\n");
		// for datatables, add a footer
		if($_SESSION["datatable"]) {
			echo("<tfoot><tr>");
			for($j=$nstart; $j<$ncols; $j++) echo("<th>".$this->contents[0][$j]."</th>");
			echo("</tr></tfoot>\n");
		}
		echo("</table>\n");
		$_SESSION["contents"]=$this->contents;
	}
}
class Chart{
	public $ncharts=0; // count
	public function show($title="Sample",$type="Radar",$data=array("A"=>1,"B"=>2,"C"=>3)) {
		if($this->ncharts==0) echo ("<script src=https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.1.1/Chart.min.js></script>\n");
		$this->ncharts++;
		$n=$this->ncharts; // handy shorthand
		echo("<div style='width:420; float:left;'><h3>$title</h3><canvas id=chart$n width=400 height=300></canvas></div>\n");
		echo("<script>\n");
		echo("var data$n = {\n");
		foreach($data as $key=>$value) {
			$labels[]=$key;
			$y[]=$value;
		}
		echo("labels : ".json_encode($labels).",\n");
		echo("datasets : [\n{\n");
		echo("label : ".json_encode($title).",\n");
		echo("fillColor : 'rgba(0,255,0,0.5)',\n");
		echo("strokeColor : '#ACC26D',\n");
		echo("pointColor : '#fff',\n");
		echo("pointStrokeColor : '#9DB86D',\n");
		echo("data : ".json_encode($y)."\n}]}\n");
		echo("var c$n = document.getElementById('chart$n').getContext('2d');\n");
		echo("new Chart(c$n).$type(data$n);\n</script>\n");
	}
}
?>

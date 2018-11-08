<?php
// thp_classes contains four basic object classes for formatting pages using the PureCSS library
// Page -- Sends the headers, starts the body, sends the navbar, main title and control icons
// Filters -- Sets up dropdowns that feed into the $_SESSION object
// Table -- Sets up and outputs a 2d table - also backing it up into $_SESSION["contents"];
// Form -- Sets up an editing form with validation
require(__DIR__."/../../includes/thpsecurity.php"); // this version sets up up PDO object and global permission variables
require(__DIR__."/chart.php");
// START CLASS PAGE
class Page {
	public $datatable = FALSE;
	public $addStickyHeader = TRUE;
	public $css=array("/static/pure.thp.css","/static/thp.form.css"); // defaults used by thpmne
	public $preh1=""; // used for dashboard colorbar etc
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
//		if($this->addStickyHeader){echo("<script src='/static/irStickyHeader.js'></script>\n");} // JC REMOVED
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
		echo($this->preh1); //used for dashboard colorbar or whatever
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
	public function fireStickyHeader(){ /* // JC COMMENTED OUT
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
		*/
	}
	public function end(){
		$time=microtime(true)-($this->time_start);
		echo("<p><i>Run time: $time</i></p>\n");
		echo("</div>\n");
//		$this->fireStickyHeader(); // JC COMMENTED OUT
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

?>

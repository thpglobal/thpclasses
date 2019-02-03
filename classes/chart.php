<?php

class Chart{
	public $ncharts=0; // count
	public $color='white'; // default text color, defines regular page from dashboard dark page
	public $background='black';
	public $fill="rgba(0,255,0,0.5)";
	public $width=3; // put 3 across unless changed
	public $db=NULL;
	public $options="scales:{xAxes:[{gridLines:{color:'yellow'}}],
	yAxes:[{ticks:{beginAtZero:true},gridLines:{color:'yellow'}}]}\n";
	public function start($db=NULL, $color='white'){ // color not yet implmented
		$this->db=$db;
		$this->color=$color;
		$this->background=($color=='white' ? 'black' : 'white');
		if($color=='black') $this->options=str_replace('yellow','blue',$this->options);
		echo("<script src=https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js></script>\n");
		echo("<script>\n");
		echo("Chart.defaults.global.responsive = true;\n");
		echo("Chart.defaults.global.defaultColor = '{$this->color}';\n");
		echo("Chart.defaults.global.defaultFontColor = '{$this->color}';\n");
		echo("var ChartOptions = {".$this->options."};\n");
		echo("var RadarOptions = {scales: { ticks: { beginAtZero: true }}};\n");
		echo("</script>\n");
		echo("<div class=pure-g>\n");
	}
	public function end() { echo("</div>\n"); }
	public function query($n,$title,$query) {
		if($this->db==NULL) Die("You forgot the Chart::start($db) method.");
		$pdo_stmt=$this->db->query($query);
		while($line=$pdo_stmt->fetch(PDO::FETCH_NUM)){ $x[]=$line[0];$y[]=$line[1];}	
		$this->make($n,$title,'bar',$x,$y);
	}
	public function make($n,$ctitle,$ctype,$x,$y){
		$width=$this->width;
		$title=str_replace("'","&apos;",$ctitle);
		echo("<div class='pure-u-1-1 pure-u-md-1-$width'><h3>$title</h3><canvas id=chart$n width=500 height=350></canvas></div>\n");
		echo("<script>\n");
		echo("var data$n = { \n");
		echo("  labels : ".json_encode($x).",\n");
		echo("  datasets : [{\n"); 
		echo("	label: '$title',\n");
		if( 'line' == $ctype ){
			echo("	fill: false,\n");
    	}else{
    		echo("	fill: true,\n");
    	}
		echo("	backgroundColor: '".$this->fill."',\n");
		echo("	data: ".json_encode($y)."\n	} \n], \n}; \n");
		echo("var c$n = document.getElementById('chart".$n."').getContext('2d');\n");
 		echo("var cc$n = new Chart(c$n,{ type: '$ctype', data: data$n, options: ChartOptions } );\n");
 		echo("</script>\n");
	}
	public function show($title="Sample",$type="radar",$data=array("A"=>1,"B"=>2,"C"=>3)) {
		$this->ncharts++;
		$n=$this->ncharts; // handy shorthand
		$width=$this->width;
		echo("<div class='pure-u-1-1 pure-u-md-1-$width'><h3>$title</h3><canvas id=chart$n width=500 height=350></canvas></div>\n");
		echo("<script>\n");
		echo("var data$n = {\n");
		foreach($data as $key=>$value) {
			$labels[]=$key;
			$y[]=$value;
		}
		echo("labels : ".json_encode($labels).",\n");
		echo("datasets : [\n{\n");
		echo("label : ".json_encode($title).",\n");
		echo("	backgroundColor: '".$this->fill."',\n");
		echo("data : ".json_encode($y)."\n}]}\n");
		echo("var c$n = document.getElementById('chart$n').getContext('2d');\n");
 		echo("var cc$n = new Chart(c$n,{ type: '$type', data: data$n");
		if($type<>'radar') echo(", options: ChartOptions");
		if($type=='radar') echo(", options: RadarOptions");
		echo("} );\n");
		echo("</script>\n");
	}
}
?>

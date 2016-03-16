<?php
	require_once "/home3/deamon/public_html/lib.php";
	page();
?>
<h2 id=game>
	
		<label for='gmode'>Select Mode:</label>
		<select id="gmode" oninput='setmode(this)'>
		  <option value=quiz selected>Did i say that?</option>
		  <option value=who>Who said that?</option>
		</select>
		<span onclick='tchooser()'> Select Politician: <input type=text id=selectedpolly disabled></span>
	<div id='chooser'>
		<div class='polly'>Loading</div>
	</div>
</h2>

<div id='player'>
	<div id='question' class='whitebox'></div>
	<div id='score' class='whitebox'>Score<hr>58</div>
	<div class='bubble mainbubble'>
		<div class='says' onclick='check(this)'></div>
		<div class='says' onclick='check(this)'></div>
		<div class='says' onclick='check(this)'></div>
	</div>
	<div class='polly mainpolly'></div>
	<div id='share' class='whitebox'>Share: <input id='shareinput' type=text disabled><button id=sharebutton data-clipboard-target="#shareinput">Copy</button></div>
</div>
<script src="https://zenorocha.github.io/clipboard.js/dist/clipboard.min.js"></script>
<script>
var player = $('#player');
var pollys = $('.polly');
var bubble = $('.bubble');
var sharebox = $('#share');
var question = $('#question');
var scorebox = $('#score');
var chooser = $('#chooser');
var picker = $('#picker');

var mydata = Array();
var current = {id:0,question:'', type:'multi',a:'',b:'',c:'',ans:'a'};
var pollys = Array();
var elected = 'trump';
var share = location.pathname+"?polly="+elected+"&q"+current['id'];
var score = 0;
var answered = false;

function $get(what){
	var expr = RegExp(what+'\=([^&#]+)')
	try{
	return window.location.search.slice(1).match(expr)[1]
	}catch (e){
		return "";
	}
}

function getquestions(who){//http get data in future
	//mydata = Array({id:1, question:'which of the following did trump say?', type:'multi',a:'asd oaisdoiasj oda sodjasldjl as djas dlasj d',b:'asfgs asdgdfsggsdf sdf gsdf gs dfg sdf gsd fg  dfgsdg',c:'asda sdasd hgkjhgjh ghfhgjfghj hjgfkff jhgjhgjkh jkgh',ans:'a'},{id:2, question:'which of the following did trump say?11', type:'multi',a:'asd',b:'asfgsdfgsdg',c:'asdasdasd',ans:'b'},{id:23, question:'which o22f the following did trump say?', type:'multi',a:'asd',b:'asfgsdfgsdg',c:'asdasdasd',ans:'a'},{id:253, question:'which of the follow44ing did trump say?', type:'multi',a:'asd',b:'asfgsdfgsdg',c:'asdasdasd',ans:'c'},{id:53, question:'which of the fol55lowing did trump say?', type:'multi',a:'asd',b:'asfgsdfgsdg',c:'asdasdasd',ans:'c'});
	$.getJSON( "/api/games/pollisay", { polly: who },function(data){mydata=data;nextquestion();} ).fail(function(){alert('Error Retrieving Politican data.')});
	//nextquestion();
}

function nextquestion(){
	current = mydata.pop();
	if(current === undefined){
		question[0].innerHTML = "Sorry, I've got nothing else on this politican. Try selecting another Politican.";
	}else{
		answered = false;
		
		for(i = 0; i < bubble.children().length; i++){
			bubble.children()[i].className = "says";
		}
		
		question[0].innerHTML = current['question'];
		//sharebox.empty();
		share = location.origin + location.pathname+"?polly="+elected+"&q"+current['id'];
		sharebox.children()[0].value = share;
		switch(current['type']){
			case 'comp':
				break;
			default:
				i = Math.random().toString();
				if(i[2] < 3){
					bubble.children()[0].innerHTML = current['a'];
					if(i[3] < 5){
						bubble.children()[1].innerHTML = current['b'];
						bubble.children()[2].innerHTML = current['c'];
					}else{
						bubble.children()[1].innerHTML = current['c'];
						bubble.children()[2].innerHTML = current['b'];
					}
				}else if(i[2] < 6){
					bubble.children()[0].innerHTML = current['b'];
					if(i[3] < 5){
						bubble.children()[1].innerHTML = current['a'];
						bubble.children()[2].innerHTML = current['c'];
					}else{
						bubble.children()[1].innerHTML = current['c'];
						bubble.children()[2].innerHTML = current['a'];
					}
				}else{
					bubble.children()[0].innerHTML = current['c'];
					if(i[3] < 5){
						bubble.children()[1].innerHTML = current['b'];
						bubble.children()[2].innerHTML = current['a'];
					}else{
						bubble.children()[1].innerHTML = current['a'];
						bubble.children()[2].innerHTML = current['b'];
					}
				}
		}
	}
}

function shareme(){
	copy(share);
	}

function check(data){
	if(!answered){
		answered = true;
		if(data.innerHTML == current[current['ans']]){
			question[0].innerHTML += " <span class='right'>Correct!</span>";
			increment_score(2);
		}else{
			question[0].innerHTML += " <span class='wrong'>Wrong..</span>";
		}		
		for(i = 0; i < bubble.children().length; i++){
			if(bubble.children()[i].innerHTML == current[current['ans']]){
				bubble.children()[i].classList.add("right");
			}else{
				bubble.children()[i].classList.add("wrong");
			}
		}
	}else{
		nextquestion();
	}
}

function increment_score(x){
	score += x;
	update_score();
}
function update_score(){
	scorebox[0].innerHTML = "Score<hr>" + score;
}

	function savescore(){
		if(typeof(Storage) !== "undefined") {
			localStorage.setItem("pollscore",score);
		} else {
			console.log("Local Storage not avalible.....");
		}
	}
	window.onunload = savescore;
	
	function loadscore(){
		if(typeof(Storage) !== "undefined") {
			if(localStorage.getItem("pollscore") != null){
				score = Number(localStorage.getItem("pollscore"));
				update_score()
			}
		} else {
			console.log("Local Storage not avalible.....");
		}
	}
	
	function setpolly(who){
		elected = who;
		$('#selectedpolly').val(who);
		question[0].innerHTML = "Loading..";
		for(a = 0; a < pollys.length;a++){
			//console.log(a);
			if(pollys[a]['name'] == who){
				getquestions(pollys[a]['id']);
				$('.mainpolly').css("background-image","url('"+pollys[a]['image']+"')");
			}
		}
		chooser.slideUp();
		player.slideDown();
	}
	
	function getpollies(){
		//pollys = Array({id:0,name:'Trump',image:"DTRUMP.jpg"},{id:1,name:'Tony',image:"DTRUMP.jpg"},{id:1,name:'Kevin07',image:"krud.jpg"});
		$.getJSON( "/api/games/pollisay", { list: true },function(data){pollys=data;update_pollys();} ).fail(function(){alert('Error Retrieving List of Politicans')});
		update_pollys();
	}
	
	function update_pollys(){
		chooser.empty();
		for(i = 0; i < pollys.length; i++){
			chooser.append("<div class=polly onclick='setpolly(this.innerHTML)' style='background-image:url("+pollys[i]['image']+");'>"+pollys[i]['name']+"</div>");
		}
	}
	
	function tchooser(){
		chooser.slideToggle();
	}
	
	function setmode(mode){
		console.log(mode.value);
		alert("Multiple modes not yet implimented");
	}
	
getpollies();
loadscore();new Clipboard('#sharebutton');
if($get("polly") == ""){
	player.hide();
}else{
	setpolly($get("polly"));
}

//getquestions();nextquestion();
</script>

<style>
#player{
	width:100%;
	height:60vmin;
    background-color: rgba(0,0,0,0.1);
    border: black 1px dashed;
}
.polly{
	width:20vmin;
	height:20vmin;
	
    background-color: lightgrey;
	background-image: url('DTRUMP.jpg');
	background-size: contain;
    background-repeat: no-repeat;
}
.bubble{
	background-image: url('saybubble.png');
	background-size: contain;
    background-repeat: no-repeat;
    text-align: -webkit-center;
}
.mainbubble{
	position:relative;
	width:60vmin;
	height:25vmin;
	bottom:-1vmin;
	left:11vmin;
	z-index: 1;
	padding:10vmin; 
	padding-bottom:15vmin; 
}
.mainpolly{
	position:relative;
	bottom:25vmin;
	left:1vmin;
}
#question{
	position:relative;
    width: 70vmin;
    min-height: 10vmin;
    left: 1vmin;
    top: 1vmin;
    font-size: 1.5em;
    font-weight: bolder;
}
#chooser{
	z-index:2;
	width:100%;
	height:100%;
	background-color:white;
    border-spacing: 1vmin;
	display: table;
}
#chooser .polly {
    display: table-cell;
	color: White;
    font-size: x-large;
    text-align: center;
}
#score{
	position: relative;
    width: 5vmin;
    height: 5vmin;
    left: 15vmin;
    top: 1vmin;
    text-align: center;
    vertical-align: top;
}
#share{
	position: relative;
    left: 30vmin;
    bottom: calc(1em + 30vmin);
    z-index: 10;
}
.says {
    padding: 1.5vmin;
    cursor: pointer;
    font-weight: bold;
    font-size: 1.1em;
    font-style: oblique;
}
.says:nth-child(2){
	border-top:black solid 2px;
	border-bottom:black solid 2px;
}
.says:hover{
	background-color:rgba(200,0,0,0.2);
}
#selectedpolly{
    cursor: pointer;
}
.whitebox{
    border-color: grey;
    border-width: 1vmin;
    border-style: double;
    display: inline-block;
	background-color:white;
	padding: 1vmin;
}

.right{
	color:green;
}
.wrong{
	color:red;
}
</style>
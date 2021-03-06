<!DOCTYPE html>
<?php session_start();if(!isset($_SESSION['userid'])){$_SESSION['userid'] = md5(uniqid(rand(), true));}?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="keywords" content="lexical decision, reaction times, on-line, web based task, posters, data visualization">
<meta name="description" content="Web-based implementation of a lexical decision task">
<meta name="author" content="Paolo Toffanin">
<title> Word recognition task </title>
<script src="https://d3js.org/d3.v3.min.js"></script>
<style type="text/css">
h1 {text-align:center;}table,td{border:1px solid black;}.chart rect {fill: steelblue;}.chart text {fill: black;font: 11px sans-serif;text-anchor: middle;}.axis path,.axis line {fill: none;stroke: #000;shape-rendering: crispEdges;}.x.axis path {display: none;}</style>
</head>
<body>
<p id="instructions" >This is a web-implementation of a word-recognition task. Single words or non-words will replace the WELCOME! text below. Push the 'n' key for non words or the 'w' key for words. Please place the cursor on the input field below and push the 'esc' key to start.</p>
<h1 id="wordDisp">WELCOME!</h1>
<p id="demo">
keyboard input field
<input type="text" size="40" onkeypress="what2do(event)"> </input>
<br>
<span id="ajaxButton" style="cursor: pointer; text-decoration: underline"></span>
</p>
<br>
<svg class="rtChart"></svg>
<svg class="accChart"></svg>
<script>
function shuffleArray(array) {
for (var i = array.length - 1; i > 0; i--) {
var j = Math.floor(Math.random() * (i + 1));
var temp = array[i];
array[i] = array[j];
array[j] = temp;}
return array;}
var words = [{name:"abstair", wordIdx: 1, isword:0},{name:"upstairs", wordIdx: 2, isword:1},{name:"forfert", wordIdx: 3, isword:0},{name:"forfeit", wordIdx: 4, isword:1},{name:"infecent", wordIdx: 5, isword:0}, {name:"indecent", wordIdx: 6, isword:1},{name:"sircastic", wordIdx: 7, isword:0}, {name:"sarcastic", wordIdx: 8, isword:1},{name:"erramic", wordIdx: 9, isword:0}, {name:"erratic", wordIdx: 10, isword:1},{name:"prolley", wordIdx: 11, isword:0}, {name:"prolly", wordIdx: 12, isword:1},{name:"sunscribe", wordIdx: 13, isword:0}, {name:"subscribe", wordIdx: 14, isword:1}];
words = shuffleArray(words);
words.unshift({name:"word", wordIdx: 0, isword:1})
var totWords = words.length - 1;
words.push({name:"you", wordIdx: totWords++, isword:1},{name:"fet", wordIdx: totWords++, isword:0},{name:"basterdz", wordIdx: totWords++, isword:0})
var criticalWords = ["illiteraet", "illiterate", "obbuse", "laughable",  "lafaible", "procrastination", "procastination", "misteik"];
var isAword = [0, 1, 0, 1, 0, 1, 0, 0];
idxArray = [2, 5, 9, 11, 16, 19, 21, 25];
fLen = idxArray.length;
for (i = 0; i < fLen; i++){words.splice(idxArray[i], 0, {name:criticalWords[i], wordIdx: totWords++, isword:isAword[i]});}
totWords = words.length-1;
var halfTime = Math.round(totWords / 2);
words.splice(halfTime, 0, {name:"Half Time!", wordIdx: totWords++, isword:0});
totWords = words.length-1;
wordDispObj = document.getElementById("wordDisp");
instDispObj = document.getElementById("instructions");
function countDown() {
setTimeout(myTimeout1, 1000);
setTimeout(myTimeout2, 2000);
setTimeout(myTimeout3, 3000);
setTimeout(startExp, 4000);}
function myTimeout1(){wordDispObj.innerHTML = "starting in 3";}
function myTimeout2(){wordDispObj.innerHTML = "starting in 2";}
function myTimeout3(){wordDispObj.innerHTML = "starting in 1";}
function startExp(){start = 1;displayWord(counter);}
var timeStart;
function displayWord(counter){wordDispObj.innerHTML = words[counter]["name"];timeStart = new Date;}
var nPushed = 0;
var wPushes = false;
function processEvent(x){
switch(x) {
	case 27:
		state = 0;
		text = "Place the index finger of your left hand on the w key and push it";
		break;
	case 119:
		text = "Place the index finger of your left hand on the n key and push it";
		wPushed  = true;
		break;
	case 110:
		text = "Push the 'w' key when you think the word replacing 'displayed word' is a word (e.g., sarcastic).<br>" + "Push the 'n' key when you think the word replacing 'displayed word' is not a word (e.g., sircastic).<br>" + "Push 'n' to start the task after 3 seconds";
		nPushed++;
		break;
	default:
		text = instDispObj.innerHTML + "<br>I do not recognize that key...";}
if ((nPushed>=2) && wPushed){	countDown();} else {
	wordDispObj.innerHTML = "displayed word";
	instDispObj.innerHTML = text;}
}
var counter = 0;
var start = 0;
function what2do(event) {
	var x = event.which || event.keyCode;
	if (start !== 1){
		processEvent(x);
	} else {
		words[counter]["RT"] = new Date - timeStart;
		words[counter]["keyPress"] = x;
		counter++
		if (counter === halfTime){
			instDispObj.innerHTML = "If you want to take a break do it now. <br> Word recognition is part of the battery of tests the Literacy Information and Communication Systems uses to establish language development and vocabolary skills. According to a preliminary analysis of your performance it appears you posses the language skills of 8-years old english speaking children. Unless you are younger than 9 years old, this might be due to inverted button-presses. Please push the letter 'n' for Non-words and the letter 'w' for Words. Thank you very much for you cooperation<br> Push the letter 'n' to continue.";}
		if (counter <= totWords){
			displayWord(counter);
		} else {
			wordDispObj.innerHTML = "Finished<br>Thank you for participating!";
			sendData2server(words);
			document.getElementById("ajaxButton").innerHTML = "Click if you'd like to check your performance";
		}
	}
}

function sendData2server(words) {
var xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function (){
if (xmlhttp.readyState === 4){
if (xmlhttp.status === 200 || xmlhttp.status === 304){
populateDatabase(xmlhttp.responseText, words);}}};
xmlhttp.open("GET", 'getLastID.php', true);
xmlhttp.send();}
function populateDatabase(response, words){
nRequests = words.length;
url = 'data2server.php';
for (i = 0; i < nRequests; i++) {
words[i].respID = response;
var xmlhttp = new XMLHttpRequest();
xmlhttp.open("POST", url + '?prikey=' + JSON.stringify(words[i]), true);
xmlhttp.send();}}
document.getElementById("ajaxButton").onclick = function(){
words.splice(halfTime, 1)
words.forEach(getAccuracy);
var aves = d3.nest()
  .key(function(d) { return d.codeWord; })
  .rollup(function(v) { return d3.mean(v, function(d) { return d.RT; }); })
  .entries(words);
for (i = 0; i < aves.length; i++){aves[i]["values"] = Math.round(aves[i]["values"]);}
plotData(aves, "rtChart", "Reaction Times (ms)");
aves = d3.nest()
  .key(function(d) { return d.codeWord; })
  .rollup(function(v) { return d3.mean(v, function(d) { return d.acc; }); })
  .entries(words);
for (i = 0; i < aves.length; i++){aves[i]["values"] = +aves[i]["values"].toFixed(2);}
plotData(aves, 'accChart', "Proportion Correct");}
function getAccuracy(item){
item["acc"] = (((item["isword"] == 0) && (item["keyPress"]) == 110) ||
	((item["isword"] == 1) && (item["keyPress"]) == 119)) ? 1 : 0;
item["codeWord"] = (item["isword"] == 0) ? "non-word" : "word";}
function plotData(data, whichPlot, titleOfTheGraph){
var margin = {top: 20, right: 30, bottom: 30, left: 40},
    width = 200 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;
var x = d3.scale.ordinal().rangeRoundBands([0, width], .1).domain(data.map(function(d) { return d.key; }));
var y = d3.scale.linear().range([height, 0]).domain([0, d3.max(data, function(d) { return d.values; })]);
var xAxis = d3.svg.axis().scale(x).orient("bottom");
var chart = d3.select("." + whichPlot).attr("width", width + margin.left + margin.right).attr("height", height + margin.top + margin.bottom).append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
var bar = chart.selectAll("g").data(data).enter().append("g").attr("transform", function(d) { return "translate(" + x(d.key) + ", 0)"; });
bar.append("rect").attr("y", function(d) { return y(d.values); }).style('fill', 'steelblue').attr("width", x.rangeBand()).attr("height", function(d) {return height - y(d.values); });
bar.append("text").attr("x", x.rangeBand() / 2).attr("y", function(d) { return y(d.values) + 3; }).attr("dy", ".75em").style("fill", "white").style("text-anchor", "middle").text(function(d) { return d.values; });
chart.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis);
chart.append("text").attr("x", (width / 2)).attr("y", 0 - (margin.top / 2)).attr("text-anchor", "middle").style("font-size", "12px").style("fill", "black").text(titleOfTheGraph);}</script>
</body>
</html>

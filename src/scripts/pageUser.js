
// Tabs
function openLink(evt, linkName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("myLink");
  for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-grey", "");
  }
  document.getElementById(linkName).style.display = "block";
  evt.currentTarget.className += " w3-grey";
}
// Click on the first tablink on load
var temp = location.search.split('filter=')[1];
if (temp == null) { 
document.getElementsByClassName("tablink")[0].click();}
else {document.getElementsByClassName("tablink")[temp].click();}


function tooglefunc() {
    var x = document.getElementById("toogleDIV");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}
var x = document.getElementById("toogleDIV");
x.style.display="none";




window.onscroll = function() {myFunction()};

var navbar = document.getElementById("navbar");
var sticky = navbar.offsetTop;

function myFunction() {
  if (window.pageYOffset >= sticky) {
    navbar.classList.add("sticky")
  } else {
    navbar.classList.remove("sticky");
  }
}





function Filter2(s) {
  var input, filter,filter2,table,table2, tr,tr2, td,i;
  input = document.getElementById("city_inp");
  input2= document.getElementById("capacity_inp");
  input3= document.getElementById("hname_inp");
  filter = input.value.toUpperCase();
  filter2 = input2.value.toUpperCase();
  filter3 = input3.value.toUpperCase();
  table = document.getElementById("RoomsByLocation");
  table2 = document.getElementById("RoomsByCapacity");
  table3 = document.getElementById("Hotelstable");
  tr = table.getElementsByTagName("tr");
  tr2 = table2.getElementsByTagName("tr");
  tr3 = table3.getElementsByTagName("tr");
if (s==0) {
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[6];
    if (td ) {  
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
 } else if(s==1){
  for (i = 0; i < tr.length; i++) {
    td = tr2[i].getElementsByTagName("td")[2];
    if (td ) {  
      if (td.innerHTML.toUpperCase().indexOf(filter2) > -1) {
        tr2[i].style.display = "";
      } else {
        tr2[i].style.display = "none";
      }
    }       
  }
 }else if(s==2){
  for (i = 0; i < tr.length; i++) {
    td = tr3[i].getElementsByTagName("td")[0];
    if (td ) {  
      if (td.innerHTML.toUpperCase().indexOf(filter3) > -1) {
        tr3[i].style.display = "";
      } else {
        tr3[i].style.display = "none";
      }
    }       
  }
 }
}

function FilterFunc(w) {
  var input, filter,filter2,filter3,filter4,table, tr, td,td2,td3,i;
  var filter5=[];
  filter5.push("");
  for (i=1; i<13; i++){
	var inp="ament"+i;
	input5=document.getElementById(inp);
	if (document.getElementById(inp).checked) {filter5.push(input5.value);}
	else {filter5.push("");}
  }

  input = document.getElementById("HG_inp");
  input2= document.getElementById("stars_inp");
  input3= document.getElementById("min_inp");
  input4= document.getElementById("max_inp");
  input6= document.getElementById("min_N");
  input7= document.getElementById("max_N");
  filter = input.value.toUpperCase();
  filter2 = input2.value.toUpperCase();
  filter3 = parseInt(input3.value.toUpperCase());
  filter4 = parseInt(input4.value.toUpperCase());
  filter6 = parseInt(input6.value.toUpperCase());
  filter7 = parseInt(input7.value.toUpperCase());
  if (isNaN(filter3)) {filter3=0;}
  if (isNaN(filter4)) {filter4=1000;}
  if (isNaN(filter6)) {filter6=0;}
  if (isNaN(filter7)) {filter7=200;}
  table = document.getElementById("myTable1");
  tr = table.getElementsByTagName("tr");
  if (w==1) {
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[6];
    td2 = tr[i].getElementsByTagName("td")[5];
    td3 = tr[i].getElementsByTagName("td")[1];
    td4 = tr[i].getElementsByTagName("td")[7];
    td5 = tr[i].getElementsByTagName("td")[8];
    if (td && td2 && td3 && td4 && td5) {  
      if (((td.innerHTML.toUpperCase().indexOf(filter) > -1) && (td2.innerHTML.toUpperCase().indexOf(filter2) > -1)) && (td3.innerHTML*1 > filter3) && (td3.innerHTML*1 < filter4) && CheckAm(filter5,td4) && (td5.innerHTML*1 > filter6) && (td5.innerHTML*1 < filter7)) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
 } else {document.getElementById("cl").innerHTML="";}
}


function CheckAm (arr,td){
	var x=true;
for (i=1; i<13; i++){
	if (!(td.innerHTML.indexOf(arr[i]) > -1)){
		x=false;
	}
}
return x;
}




const ctx = document.getElementById('performanceChart');

if(ctx){

new Chart(ctx,{

type:'doughnut',

data:{

labels:['Excellent','Good','Average','Poor'],

datasets:[{

data:[70,20,7,3],

backgroundColor:[

'#22c55e',

'#2563eb',

'#f59e0b',

'#ef4444'

],

borderWidth:0

}]

},

options:{

cutout:'72%',

plugins:{

legend:{display:false}

}

}

});

}
// =========================
// AOS
// =========================
AOS.init({
    duration: 800,
    once: true
});

// =========================
// Loader
// =========================
window.addEventListener("load", function(){

    setTimeout(function(){

        document.getElementById("loader").classList.add("hide");

    },500);

});

// =========================
// Counter Animation
// =========================

document.querySelectorAll(".dashboard-card h2").forEach(counter=>{

let target=parseInt(counter.innerText);

if(isNaN(target)) return;

let count=0;

let speed=Math.max(10,target/60);

let interval=setInterval(function(){

count+=speed;

if(count>=target){

counter.innerText=target;

clearInterval(interval);

}else{

counter.innerText=Math.floor(count);

}

},20);

});

// =========================
// Sidebar Collapse
// =========================

const sidebar=document.getElementById("sidebar");

const toggle=document.getElementById("menuToggle");

if(toggle){

toggle.onclick=function(){

sidebar.classList.toggle("collapsed");

document.querySelector(".main").classList.toggle("expand");

}

}

// =========================
// Dark Mode
// =========================

const darkBtn=document.getElementById("darkMode");

if(darkBtn){

darkBtn.onclick=function(){

document.body.classList.toggle("dark-mode");

}

}

// =========================
// Scroll To Top
// =========================

const topBtn=document.getElementById("scrollTop");

window.onscroll=function(){

if(window.scrollY>300){

topBtn.classList.add("show");

}else{

topBtn.classList.remove("show");

}

}

topBtn.onclick=function(){

window.scrollTo({

top:0,

behavior:'smooth'

});

}
document.addEventListener('DOMContentLoaded',function(){

var calendarEl=document.getElementById('calendar');

var calendar=new FullCalendar.Calendar(calendarEl,{

initialView:'dayGridMonth',

height:320,

headerToolbar:{

left:'prev',

center:'title',

right:'next'

},

events:[

{

title:'Lecture',

start:'2026-08-21'

}

]

});

calendar.render();

});
// =========================
// SIDEBAR TOGGLE
// =========================

const menuBtn = document.querySelector(".menu-btn");
const sidebar = document.querySelector(".sidebar");

if(menuBtn){

menuBtn.addEventListener("click",()=>{

sidebar.classList.toggle("active");

});

}


// =========================
// LIVE CLOCK
// =========================

function updateClock(){

const now = new Date();

const options = {
weekday:'long',
day:'numeric',
month:'long',
year:'numeric'
};

document.getElementById("today").innerHTML =
now.toLocaleDateString('en-US',options);

document.getElementById("clock").innerHTML =
now.toLocaleTimeString();

}

setInterval(updateClock,1000);

updateClock();

// =========================
// COUNTER
// =========================

const counters=document.querySelectorAll(".counter");

counters.forEach(counter=>{

counter.innerText='0';

const updateCounter=()=>{

const target=+counter.getAttribute("data-target");

const c=+counter.innerText;

const increment=target/100;

if(c<target){

counter.innerText=`${Math.ceil(c+increment)}`;

setTimeout(updateCounter,15);

}else{

counter.innerText=target;

}

}

updateCounter();

});

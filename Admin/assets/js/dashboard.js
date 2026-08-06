/*====================================================
    FORCES ACADEMY LMS
    ADMIN DASHBOARD
======================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*==============================
        Sidebar Toggle
    ==============================*/

    const sidebar = document.querySelector(".sidebar");
    const menuBtn = document.querySelector(".menu-toggle");
    const mainContent = document.querySelector(".main-content");

    if (menuBtn) {

        menuBtn.addEventListener("click", function () {

            sidebar.classList.toggle("active");

            mainContent.classList.toggle("full");

        });

    }



    /*==============================
        Counter Animation
    ==============================*/

    const counters = document.querySelectorAll(".counter");

    counters.forEach(counter => {

        counter.innerText = "0";

        const updateCounter = () => {

            const target = Number(counter.getAttribute("data-target")) ||
                           Number(counter.textContent);

            const current = Number(counter.innerText);

            const increment = Math.ceil(target / 80);

            if (current < target) {

                counter.innerText = current + increment;

                setTimeout(updateCounter, 20);

            } else {

                counter.innerText = target;

            }

        };

        updateCounter();

    });



    /*==============================
        Card Hover Animation
    ==============================*/

    const cards = document.querySelectorAll(".dashboard-card");

    cards.forEach(card => {

        card.addEventListener("mouseenter", function () {

            card.style.transform = "translateY(-8px) scale(1.02)";

        });

        card.addEventListener("mouseleave", function () {

            card.style.transform = "translateY(0px) scale(1)";

        });

    });



    /*==============================
        Quick Action Hover
    ==============================*/

    const quickBoxes = document.querySelectorAll(".quick-box");

    quickBoxes.forEach(box => {

        box.addEventListener("mouseenter", function () {

            box.style.transition = ".3s";

            box.style.transform = "translateY(-8px)";

        });

        box.addEventListener("mouseleave", function () {

            box.style.transform = "translateY(0px)";

        });

    });



    /*==============================
        Loading Screen
    ==============================*/

    window.addEventListener("load", function () {

        const loader = document.querySelector(".loading");

        if (loader) {

            loader.style.opacity = "0";

            setTimeout(function () {

                loader.style.display = "none";

            }, 500);

        }

    });



    /*==============================
        Smooth Scroll
    ==============================*/

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {

        anchor.addEventListener("click", function (e) {

            e.preventDefault();

            document.querySelector(this.getAttribute("href"))
            ?.scrollIntoView({

                behavior: "smooth"

            });

        });

    });

});
/*====================================================
        NOTIFICATION ANIMATION
=====================================================*/

const notificationIcon = document.querySelector(".fa-bell");

if(notificationIcon){

notificationIcon.addEventListener("click",function(){

notificationIcon.classList.add("fa-shake");

setTimeout(function(){

notificationIcon.classList.remove("fa-shake");

},1000);

});

}



/*====================================================
        LIVE SEARCH
=====================================================*/

const searchInput=document.querySelector(".search-box input");

if(searchInput){

searchInput.addEventListener("keyup",function(){

let filter=this.value.toLowerCase();

let table=document.querySelectorAll("tbody tr");

table.forEach(function(row){

let text=row.innerText.toLowerCase();

if(text.indexOf(filter)>-1){

row.style.display="";

}else{

row.style.display="none";

}

});

});

}



/*====================================================
        CURRENT DATE & TIME
=====================================================*/

const dateContainer=document.getElementById("currentDate");

function updateDate(){

const now=new Date();

const options={

weekday:'long',

year:'numeric',

month:'long',

day:'numeric'

};

if(dateContainer){

dateContainer.innerHTML=now.toLocaleDateString("en-US",options);

}

}

updateDate();



/*====================================================
        DIGITAL CLOCK
=====================================================*/

const clock=document.getElementById("clock");

function updateClock(){

const now=new Date();

let h=now.getHours();

let m=now.getMinutes();

let s=now.getSeconds();

h=h<10?"0"+h:h;

m=m<10?"0"+m:m;

s=s<10?"0"+s:s;

if(clock){

clock.innerHTML=h+":"+m+":"+s;

}

}

setInterval(updateClock,1000);



/*====================================================
        RIPPLE BUTTON EFFECT
=====================================================*/

const buttons=document.querySelectorAll(".btn");

buttons.forEach(btn=>{

btn.addEventListener("click",function(e){

let ripple=document.createElement("span");

let rect=this.getBoundingClientRect();

let size=Math.max(rect.width,rect.height);

ripple.style.width=size+"px";

ripple.style.height=size+"px";

ripple.style.left=e.clientX-rect.left-size/2+"px";

ripple.style.top=e.clientY-rect.top-size/2+"px";

ripple.classList.add("ripple");

this.appendChild(ripple);

setTimeout(()=>{

ripple.remove();

},600);

});

});



/*====================================================
        AUTO REFRESH DASHBOARD
=====================================================*/

setInterval(function(){

const refresh=document.querySelector(".refresh-icon");

if(refresh){

refresh.classList.add("fa-spin");

setTimeout(function(){

refresh.classList.remove("fa-spin");

},1000);

}

},60000);



/*====================================================
        SCROLL TO TOP BUTTON
=====================================================*/

const topBtn=document.getElementById("topButton");

window.addEventListener("scroll",function(){

if(window.pageYOffset>250){

if(topBtn){

topBtn.style.display="block";

}

}else{

if(topBtn){

topBtn.style.display="none";

}

}

});

if(topBtn){

topBtn.addEventListener("click",function(){

window.scrollTo({

top:0,

behavior:"smooth"

});

});

}



/*====================================================
        SIDEBAR ACTIVE MENU
=====================================================*/

const menuItems=document.querySelectorAll(".sidebar-menu li");

menuItems.forEach(item=>{

item.addEventListener("click",function(){

menuItems.forEach(i=>{

i.classList.remove("active");

});

this.classList.add("active");

});

});



/*====================================================
        TOOLTIP
=====================================================*/

const tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

tooltipTriggerList.map(function(element){

return new bootstrap.Tooltip(element);

});



/*====================================================
        FADE IN CONTENT
=====================================================*/

const fadeElements=document.querySelectorAll(".card,.dashboard-card");

fadeElements.forEach((element,index)=>{

element.style.opacity="0";

element.style.transform="translateY(20px)";

setTimeout(()=>{

element.style.transition=".6s";

element.style.opacity="1";

element.style.transform="translateY(0px)";

},index*120);

});

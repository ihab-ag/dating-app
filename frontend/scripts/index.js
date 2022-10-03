// DOM elements
const location_btn= document.getElementById('location-btn');
const signup_form= document.getElementById('signup');
const longitude= signup_form.elements.longitude;
const latitude= signup_form.elements.latitude;
const validation_msg= document.getElementById('validation');

// functions
// get geolocation from browser
getLocation=()=>{
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
      validation_msg.innerHTML = "Geolocation is not supported by this browser.";
    }
  }
// get position values
showPosition=(position)=> {
    longitude.value= position.coords.longitude;
    latitude.value= position.coords.latitude;
  }
// validation

// events
location_btn.onclick=(e)=>{
    e.preventDefault();
    getLocation();
}
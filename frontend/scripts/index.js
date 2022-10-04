// DOM elements
const location_btn= document.getElementById('location-btn');
const signup_form= document.getElementById('signup');
const longitude= signup_form.elements.longitude;
const latitude= signup_form.elements.latitude;
const name= signup_form.elements.name;
const email= signup_form.elements.email;
const password= signup_form.elements.password;
const gender= signup_form.elements.gender;
const interest= signup_form.elements.interest;
const validation_msg= document.getElementById('validation');
const signup_submit=document.getElementById('signup-submit');
const close= document.getElementById('close');
const signup= document.querySelector('.signup');
const signup_section_btn = document.getElementById('signup-section-btn');
// variables
const base_url="http://127.0.0.1:8000/api/";
let route;
let data;
let token;
// functions
// get geolocation from browser
const getLocation=()=>{
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
      validation_msg.innerText = "Geolocation is not supported by this browser.";
    }
  }
// get position values
const showPosition=(position)=> {
    longitude.value= position.coords.longitude;
    latitude.value= position.coords.latitude;
  }
// signup validation
const validation=()=>{
    if(name.value==""){
        return "Name cannot be empty";
    }
    else if((!(/\w{3,}[@]\w{5,}[.][a-zA-Z]*$/).test(email.value))||email.value==""){
        return "Email must be in the following format: name@mail.com";
    }
    else if((!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/).test(password.value))||password.value==""){
        return "Password must be at least 8 chars long with at least one uppercase, lowercase and numeric character";
    }
    else if(longitude.value==""||latitude.value==""){
        return "Enter location";
    }
    else if(gender.value!='male'&&gender.value!='female'){
        return "Choose gender";
    }
    else if(gender.value!='male'&&gender.value!='female'&&gender.value!='both'){
        return "Choose interest";
    }
    else
    return "good";
}
// send sign up request
const postReq= async (route,data,token=null)=>{
    try{
        return await axois.post(base_url+route,data,{
            headers: {
            'Authorization': 'Bearer ' + token
          }});
    }catch(error) {
        return "failed: "+error.response.data.message;
    }
}
// events
location_btn.onclick=(e)=>{
    e.preventDefault();
    getLocation();
}
signup_submit.onclick=(e)=>{
    e.preventDefault();
    validation_msg.innerHTML= validation();
}
close.onclick=()=>{
    signup.style.width=0;
}
signup_section_btn.onclick=()=>{
    signup.style.width='100%';
}
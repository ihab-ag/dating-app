// DOM elements
    // Edit form
    const signup_form= document.getElementById('signup');
    const location_btn= document.getElementById('location-btn');
    const longitude= signup_form.elements.longitude;
    const latitude= signup_form.elements.latitude;
    const name= signup_form.elements.name;
    const bio= signup_form.elements.bio;
    const gender= signup_form.elements.gender;
    const interest= signup_form.elements.interest;
    const privacy= signup_form.elements.private;
    const file= signup_form.elements.file;
    const validation_msg= document.getElementById('validation');
    const signup_submit=document.getElementById('signup-submit');
// variables
    let url;
    const base_url="http://127.0.0.1:8000/api/";
    const token = localStorage.getItem('token');
// functions
    // validation
    const validation=()=>{
        if(name.value==""){
            return "Name cannot be empty";
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
        else if(private.value!=1&&private.value!=0){
            return "Choose privacy";
        }
        else
        return "good";
    }
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
    // get image uploaded
    const getImage=(e)=>{
        let image_file= e.target.files[0];
                reader= new FileReader;
                reader.onload=(e)=>{
                  url= reader.result.split(",")[1];
                }
                reader.readAsDataURL(image_file);
    }
    // send post request
    const postReq= async (route,data,token=null)=>{
    try{
        return await axios.post(base_url+route,data,{
            headers: {
            'Authorization': 'Bearer ' + token
          }});
    }catch(error) {
        return error;
    }
    }
    // get info of user to fill profile
    const getUserInfo= async()=>{
        let response= await postReq('get-user'," ",token);
        response=response.data.user[0];
        name.value=response.name;
        bio.value=response.bio;
        longitude.value=response.location.longitude;
        latitude.value=response.location.latitude;
        privacy.value=response.private;
        gender.value=response.gender;
        interest.value=response.interest;
        url=response.picture.url;
    }
    // }get users from db
    const getUsers= async()=>{
        let response= await postReq('get-users'," ",token);
        for(const user of response.data){
            createCard(user);
        }
    }
    // create user card
    const createCard=(user)=>{
        const card= document.createElement('div');
        card.classList="card flex-column";
        card.innerHTML=`<div class="card__img">
                        <img src="../backend/laravel-backend${user.url.slice(2)}" alt="">
                        </div>
                    <h3>${user.name}, ${user.age}</h3>
                    <p>${user.bio}</p>
                    <div class="flex-row">
                        <button class="btn" onclick="block(${user.id})">block</button>
                        <button class="btn" onclick="addFav(${user.id})">favourite</button>
                    </div>`;
        const cards= document.querySelector('.cards');
        cards.appendChild(card);
    }
    // add to Fav
    const addFav=(id)=>{
        data = new FormData();
        data.append("id",id);
        postReq("add-favourite",data,token);
    }
    // block
    const block=(id)=>{
        data = new FormData();
        data.append("id",id);
        postReq("add-block",data,token);
    }
// events
    file.onchange=async(e)=>{
        await getImage(e);
    }
    location_btn.onclick=(e)=>{
        e.preventDefault();
        getLocation();
    }
    signup_submit.onclick=async (e)=>{
        e.preventDefault();
        validation_msg.innerText= validation();
        if(validation_msg.innerText=="good"){
            route="update-user";
            data= new FormData(signup_form);
            data.append('longitude',longitude.value);
            data.append('latitude',latitude.value);
            data.append('url',url);
            await postReq(route,data,token);
        }
    }
    // main
    getUserInfo();
    getUsers();
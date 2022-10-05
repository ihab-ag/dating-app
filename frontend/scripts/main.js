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
    // header
    const main= document.getElementById('main');
    const profile= document.getElementById('profile');
    const fav= document.getElementById('fav');
    const main_sect= document.querySelector('.home');
    const profile_sect= document.querySelector('.profile');
    const fav_sect= document.querySelector('.fav');
    // home
    const cards= document.querySelector('.cards');
    // favourite
    const fav_cards= document.querySelector('.fav-cards');
    // chat
    const chat =document.getElementById('chat');
    const close =document.getElementById('close');
    const chat_name =document.getElementById('chat-name');
    const chat_messages=document.getElementById('messages');
    const chat_message=document.getElementById('message');
    const send_btn=document.getElementById('send-btn');
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
                const reader= new FileReader;
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
        const route='get-user';
        let response= await postReq(route," ",token);
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
        const route='get-users';
        let response= await postReq(route," ",token);
        for(const user of response.data){
            createCard(user);
        }
    }
    const getFavs= async()=>{
        const route='favourites';
        let response= await postReq(route," ",token);
        for(const user of response.data){
            createCard(user,true);
        }
    }
    // create user card
    const createCard=(user,fav=false)=>{
        const card= document.createElement('div');
        card.classList="card flex-column";
        let buttons= fav?`<button class="btn" onclick="openChat(${user.id},'${user.name}')">chat</button>`:`<button class="btn" onclick="block(${user.id})">block</button>
        <button class="btn" onclick="addFav(${user.id})">favourite</button>`;
        card.innerHTML=`<div class="card__img">
                        <img src="../backend/laravel-backend/public/images${user.id}.jpg" alt="">
                        </div>
                    <h3>${user.name}, ${user.age}</h3>
                    <p>${user.bio}</p>
                    <div class="flex-row">
                        ${buttons}
                    </div>`;
        fav?fav_cards.appendChild(card):cards.appendChild(card);
    }
    // add to Fav
    const addFav=(id)=>{
        const route="add-favourite";
        const data = new FormData();
        data.append("id",id);
        postReq(route,data,token);
    }
    // block
    const block=(id)=>{
        const route="add-block";
        const data = new FormData();
        data.append("id",id);
        postReq(route,data,token);
    }
    // open chat
    const openChat=async(id,name)=>{
        const route="get-chat";
        const data= new FormData();
        data.append("id",id);
        let request= await postReq(route,data,token);
        request= request.data;
        let chat_id= request.chat_id;
        let messages =request.messages;
        chat_name.innerHTML=name;
        chat.style.display='flex';
        loadChat(id,messages);
        send_btn.onclick=(e)=>{
            e.preventDefault();
            sendMessage(id,chat_id,name);
        } ;
    }
    // get chat messages
    const loadChat=(id,messages)=>{
        chat_messages.innerHTML="";
        for(const message of messages){
            const content= document.createElement('p');
            content.innerHTML = message.user_id==id?`<p class="away">${message.messages}</p>`:`<p class="here">${message.messages}</p>`
            chat_messages.append(content);
        }
    }
    // send messages
    const sendMessage=async(id,chat_id,name)=>{
        const route='send-message';
        const data= new FormData();
        data.append('chat_id',chat_id);
        data.append('message',chat_message.value);
        chat_message.value="";
        await postReq(route,data,token);
        openChat(id,name);
    }
    // refresh token
    const refresh=async()=>{
        const route='refresh';
        const response= await postReq(route, " ",token);
        
        console.log(response)
        localStorage.setItem("token",response['data'].authorisation['token']);
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
    main.onclick=()=>{
        profile_sect.style.display="none";
        main_sect.style.display="block";
        fav_sect.style.display="none";
    }
    profile.onclick=()=>{
        main_sect.style.display="none";
        profile_sect.style.display="block";
        fav_sect.style.display="none";
    }
    fav.onclick=()=>{
        main_sect.style.display="none";
        profile_sect.style.display="none";
        fav_sect.style.display="block";
    }
    close.onclick=()=>{
        chat.style.display='none';
    }
    // main
    getUserInfo();
    getUsers();
    getFavs();
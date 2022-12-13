userForm = document.getElementById("user-form");
checkbox = document.getElementById("want_account");
userForm.style.display="none";
checkbox.onchange=function(){
    if (this.checked){
        userForm.style.display="flex";
    }else{
        userForm.style.display="none";
    }
    
}
window.onload=function(){
    if(checkbox.checked){
        userForm.style.display="flex";
    }else{
        userForm.style.display="none";
    }
}

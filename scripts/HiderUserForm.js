userForm = document.getElementById("user-form");
checkbox = document.getElementById("want_account");
userForm.style.display="none";
checkbox.onchange=function(){
    if (this.checked){
        userForm.style.display="flex";
    }else{
        userForm.style.display="none";
    }
    
    if(checkbox.checked==false){
        alert("VAROVÁNÍ: Pokud tento přepínač nebude při odeslání zaškrtnutý, Bude účet smazán!");
    }
    
}
window.onload=function(){
    if(checkbox.checked){
        userForm.style.display="flex";
    }else{
        userForm.style.display="none";
    }
}

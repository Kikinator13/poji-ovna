passwordContainerForm = document.getElementById("password-container-form").style.display = "none";
userNameContainerForm = document.getElementById("user_name-container-form").style.display = "none";
accountContainerForm = document.getElementById("account-container-form").style.display = "none ";
if (hasAccount()) {

  accountContainer = document.getElementById("account-container").style.display = "none";
  accountTh = document.getElementById("account-th").style.display = "none";
} else {
  passwordTh = document.getElementById("password-th").style.display = "none";
  userNameContainer = document.getElementById("user_name-container").style.display = "none";
  passwordcontainer = document.getElementById("password-container").style.display = "none";
  userNameTh = document.getElementById("user_name-th").style.display = "none";


}

function openForm(origin) {
  form = document.getElementById(origin + "-form");
  origin = document.getElementById(origin);

  origin.style.display = "none";
  form.className = "col-6 p-1 td d-flex";

  //Při kliknutí na formulář zabráníme probublání události aby se formulář zavřel jen při kliknutí mimo formulář.
  form.onmousedown = function (e) {
    //e.stopPropagation();
    e.cancelBubble = true; // vypnutí pro starší Explorery
    if (e.stopPropagation)
      e.stopPropagation(); // vypnutí pro ostatní
  }
  //Při kliknutí na dokument se formulář zavře. 
  document.onmousedown = function () {
    inputContainers = Array.from(form.firstElementChild.children);
    inputContainers.pop();
    let inputs = [];
    inputContainers.forEach(function (input) {
      inputs.push(input.firstElementChild);
    });
    closeForm(origin, form, inputs);
  }

  form.firstElementChild.onsubmit = function (e) {
    e.preventDefault();
    change(origin, form);
  }
}

function change(origin, form) {

  let XHR = createXHR();
  XHR.onreadystatechange = function () {
    if (XHR.readyState == 4 && XHR.status == 200) {
      let datatext = XHR.responseText;
      

      let data = eval("(" + XHR.responseText + ")");
      //Pokud byla operace úspěšná a dostali jsme tak zprávu o úspěchu
      if (data.message.type == "SUCCESS") {

        //Připravíme si pole na inputy.
        let inputs = [];
        //Projdem nová data a nahradíme nimi na stránce ta stará.
        
        for (inputId in data.data) {
          spanElement = document.getElementById(inputId + "-span");
          let input = document.getElementById(inputId)
          if (data.data.password == "") {
            input.value = "";
          } else if (data.data.user_name && data.data.password == "") {

          } else {
            spanElement.innerText = data.data[inputId];
          }



          inputs.push(input);
          input.className = input.className.replace(" is-valid", "");
          input.className = input.className.replace(" is-invalid", "");

        }
        closeForm(origin, form, inputs);
        addMessage(data.message.text, data.message.type);
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        if (data.data.user_name && data.data.password == "") {
          
          document.getElementById("user_name-container").innerText=data.data.user_name;
          document.getElementById("password-th").style.display = "flex";
          document.getElementById("user_name-th").style.display = "flex";
          document.getElementById("password-container").style.display = "flex";
          document.getElementById("user_name-container").style.display = "flex";
          document.getElementById("account-container-form").style.display = "none";
          document.getElementById("account-th").style.display = "none";
          document.getElementById("account-container").style.display = "none";
        }
      } else {
        errors = data.errors;
        if (data.message) {

          addMessage(data.message.text, data.message.type);

        }
        for (error in errors) {
          input = document.getElementById(error);
          input.className = input.className.replace(" is-valid", "");
          input.className = input.className.replace(" is-invalid", "");

          if (errors[error].type == "invalid") {
            input.className = input.className + " is-invalid";
            document.getElementById(error + "-feedback").innerText = errors[error].text;
          }

          if (errors[error].type == "valid") {

            input.className = input.className + " is-valid";
            let errorfeedback = document.getElementById(error + "-feedback");
            errorfeedback.innerText = errors[error].text;

          }
        }

      }
    }
  }
  //Získáme děti formuláře (divi obalující inputy).
  inputs = Array.from(form.firstElementChild.children);
  //odstraníme z pole odesílací tlačítko
  inputs.pop();


  POST = "";
  inputs.forEach(function (input) {
    inputs[input] = input.firstElementChild
    POST += inputs[input].name + "=" + inputs[input].value + "&";

  })

  POST += "ajax=true";

  id = document.getElementById("id").value;
  url = "profile/" + id;
  XHR.open('POST', url);
  XHR.setRequestHeader("Cache-Control", "no-cache");
  XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  XHR.send(POST);

}

function closeForm(origin, form, inputs) {
  document.onmousedown = null;
  origin.style.display = "flex";
  form.className = "d-none";
  inputs.forEach(function (input) {
    input = input;
    if (input.type == "password") {
      input.value = "";
    } else if (input instanceof HTMLInputElement) {
      span = document.getElementById(input.id + "-span");
      input.value = span.innerText;
    } else if (input instanceof HTMLSelectElement) {
      span = document.getElementById(input.id + "-span");
      input.value = span.dataset.index;
    }
    input.className = input.className.replace("is-valid", "");
    input.className = input.className.replace("is-invalid", "");
  });
}

function addMessage(text, typ) {
  let message = '<p onClick = "closser(this)" class="' + typ + '"><strong>' + ((typ == "SUCCESS") ? '' : typ + ':') + ' </strong> ' + text + '</p>';
  let messages = document.getElementById("messages");
  messages.innerHTML = message;
}

function hasAccount() {
  $userName = document.getElementById("user_name-span");
  if ($userName.innerText == "") {
    $userName.innerText = "";
    return false;
  } else {
    return true;
  }
}/*
function hideAccountCreator() {
  accountTh = document.getElementById("account-th").style.display = "none";
  accountContainer = document.getElementById("account-container").style.display = "none";
  accountContainerForm = document.getElementById("account-container-form").style.display = "none";
  userName = document.getElementById("user_name").style.display = "none";
  userNameContainerForm = document.getElementById("user_name-container-form").style.display = "none";
  passwordContainerForm = document.getElementById("password-container-form").style.display = "none";
  
}
function hidePasswordChanger() {
  accountContainerForm = document.getElementById("account-container-form").style.display = "none";
  userNameTh = document.getElementById("user_name-th").style.display = "none";
  userNameContainer = document.getElementById("user_name-container").style.display = "none";
  userNameContainerForm = document.getElementById("user_name-container-form").style.display = "none";
  passwordTh = document.getElementById("password-th").style.display = "none";
  passwordcontainer = document.getElementById("password-container").style.display = "none";
  passwordContainerForm = document.getElementById("password-container-form").style.display = "none";
}
function showAccountCreator() {
  accountTh = document.getElementById("account-th").style.display = "flex";
  accountContainer = document.getElementById("account-container").style.display = "flex";
  accountContainerForm = document.getElementById("account-container-form").style.display = "flex";
}
function showPasswordChanger() {
  userNameTh = document.getElementById("user_name-th").style.display = "flex";
  userNameContainer = document.getElementById("user_name-container").style.display = "flex";
  userNameContainer = document.getElementById("user_name-container-form").style.display = "none";
  userNameContainerForm = document.getElementById("user_name-container-form").style.display = "none";
  passwordTh = document.getElementById("password-th").style.display = "flex";
  passwordcontainer = document.getElementById("password-container").style.display = "flex";
  passwordContainerForm = document.getElementById("password-container-form").style.display = "flex";
}*/
function createXHR() {
  var xhr;
  try {
    xhr = new XMLHttpRequest();
  } catch (e) {
    var MSXmlVerze = new Array('MSXML2.XMLHttp.6.0', 'MSXML2.XMLHttp.5.0', 'MSXML2.XMLHttp.4.0', 'MSXML2.XMLHttp.3.0', 'MSXML2.XMLHttp.2.0', 'Microsoft.XMLHttp');
    for (var i = 0; i <= MSXmlVerze.length; i++) {
      try {
        xhr = new ActiveXObject(MSXmlVerze[i]);
        break;
      } catch (e) {
      }
    }
  }
  if (!xhr)
    alert("Došlo k chybě při vytváření objektu XMLHttpRequest!");
  else
    return xhr;
}

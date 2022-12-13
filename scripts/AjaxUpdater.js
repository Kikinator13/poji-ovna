
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

    closeForm(origin, form, inputContainers);
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
      alert(datatext);
      
      let data = eval("(" + XHR.responseText + ")");
      if (data.errors) {
        errors = data.errors;
        addMessage("Změna se nezdařila!", "ERROR");
        if(data.message){
          alert(data.message);
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
        
      } else {
        
        addMessage("Adresa byla změněna.", "SUCCESS");

        let inputs = [];
        for (spanId in data) {
          spanElement = document.getElementById(spanId + "-span");
          spanElement.innerText = data[spanId];
          inputs.push(document.getElementById(spanId));
        }
        closeForm(origin, form, inputs);
        addMessage(datatext, "SUCCESS");

      }
    }
  }
  //Získáme děti formuláře (divi obalující inputy).
  inputs = Array.from(form.firstElementChild.children);
  //alert(inputs+"end");
  //odstraníme z pole odesílací tlačítko
  inputs.pop();


  POST = "";
  inputs.forEach(function (input) {

    POST += input.firstElementChild.id + "=" + input.firstElementChild.value + "&";

  })

  POST += "ajax=true";

  id = document.getElementById("id").value;
  url = "profile/" + id;
  XHR.open('POST', url);
  XHR.setRequestHeader("Cache-Control", "no-cache");
  XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  XHR.send(POST);

}

function closeForm(origin, form, inputContainers) {
  document.onmousedown = null;
  origin.style.display = "flex";
  form.className = "d-none";
  inputContainers.forEach(function (inputContainer) {
    input = inputContainer.firstElementChild;

    span = document.getElementById(input.id + "-span");
    if (input instanceof HTMLInputElement);
    input.value = span.innerText;
    if (input instanceof HTMLSelectElement)
      input.value = span.dataset.index;

    input.className = input.className.replace("is-valid", "");
    input.className = input.className.replace("is-invalid", "");
  });
}

function addMessage(text, typ) {
  let message = '<p class="' + typ + '"><strong>' + ((typ == "SUCCESS") ? '' : typ + ':') + ' </strong> ' + text + '</p>';
  let messages = document.getElementById("messages");
  messages.innerHTML = message;
}
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

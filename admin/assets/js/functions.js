function SendRequest(method, path, data, response) {
    let xhr = new XMLHttpRequest();
    xhr.open(method, path);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(data);
    xhr.onload = function () {
        if (xhr.status != 200) {
            console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
        } else {
            response(JSON.parse(xhr.response));
        }
    };

    xhr.onerror = function () {
        console.log("Ошибка при отправке запроса");
    };
}

function addOption(oListbox, text, value, isDefaultSelected, isSelected) {
    var oOption = document.createElement("option");
    oOption.appendChild(document.createTextNode(text));
    oOption.setAttribute("value", value);

    if (isDefaultSelected) oOption.defaultSelected = true;
    else if (isSelected) oOption.selected = true;

    oListbox.appendChild(oOption);
}
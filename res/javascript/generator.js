function send() {
    const groups = [];

    for (const value of document.querySelectorAll(".div-textareas fieldset")) {
        const json = {
            title: value.children[0].textContent,
            array: []
        };

        for (let c = 0; c < value.children[1].children.length; c++)
            json.array.push(value.children[1].children[c].value);

        groups.push(json);
    }

    const result = document.querySelector("textarea[readonly]");
    const string = document.getElementById("string").value;

    const file = new Blob([JSON.stringify(groups)], { type: 'application/json;charset=utf-8' });
    const formData = new FormData();
    formData.append('file', file);
    formData.append('string', string);
    fetch('../php/scripts.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            data = data.result;
            console.log(data)
            let str = "Seu texto foi classificado na seguinte ordem:\n";
            for (const key in data) {
                console.log(key)
                str += `${key}: ${(data[key] * 100).toFixed(3)}%\n`;
            }
            result.value = str;
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

function newTextArea(element) {
    if (element?.target)
        element = element.target;

    const counter = element.classList[0].split("-")[1];
    const container = document.querySelectorAll(".div-textareas")[counter - 1].children[1];
    const textarea = document.createElement("textarea");
    textarea.placeholder = "Insira um texto para o treinamento...";
    container.appendChild(textarea);
}

function removeTextArea(element) {
    if (element?.target)
        element = element.target;

    const counter = element.classList[0].split("-")[1];
    const container = document.querySelectorAll(".div-textareas")[counter - 1].children[1];

    if (container.children.length >= 2)
        container.children[container.children.length - 1].remove();
}
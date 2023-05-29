function send() {
    const result = document.querySelector("textarea[readonly]");
    const k = document.getElementById("k").value;

    const json = {
        array: [],
        k: k | 2
    };

    for (const textarea of document.querySelectorAll(".div-textareas div")[0].children)
        json.array.push(textarea.value);

    const file = new Blob([JSON.stringify(json)], { type: 'application/json;charset=utf-8' });
    const formData = new FormData();
    formData.append('file', file);
    fetch('../php/scripts.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            data = data.result;
            let str = "Agrupamentos:\n";

            for (const key in data) {
                let aux = "\n\t- ";
                aux += data[key].join("\n\t- ");
                str += `${key}: ${aux};\n\n`;
            }

            result.value = str;
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

function newTextArea() {
    const container = document.querySelectorAll(".div-textareas div")[0];
    const textarea = document.createElement("textarea");
    textarea.placeholder = "Insira um texto para o agrupamento...";
    container.appendChild(textarea);
}

function removeTextArea() {
    const container = document.querySelectorAll(".div-textareas div")[0];

    if (container.children.length > 2)
        container.children[container.children.length - 1].remove();
}
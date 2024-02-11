document.addEventListener("DOMContentLoaded", function() {
    var configInput = document.getElementById("sk_newsletterhelper_configurator_base64");
    var config =  JSON.parse(atob(configInput.value));

    Object.keys(config).forEach(function(key) {
        if(config[key].method === "save") {
           initSave(config[key]);
        }
    });
});


async function initSave(element) {
    await fetch(element.url).then(function(response) {
        return response.text();
    }).then(function(string) {

        var parser = new DOMParser();
        var doc = parser.parseFromString(string, 'text/html');
        var saveHtml = doc.body.firstChild;
        document.body.append(saveHtml);

        var saveButton = document.getElementsByClassName("sk-save");

        saveButton[0].addEventListener("click", function() {
            var templateContent = document.getElementById('sk-mjml-template').innerHTML;
            fetch(element.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'text/html'
                },
                body: templateContent
            }).then(function(response) {
                return response.text();
            });
        });

    });
}
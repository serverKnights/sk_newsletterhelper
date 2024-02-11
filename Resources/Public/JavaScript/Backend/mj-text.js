const init = div =>
    InlineEditor.create(div, {
        toolbar: [
            "bold",
            "italic",
            "link",
            "bulletedList",
            "numberedList",
            "undo",
            "redo"
        ]
    });

document.addEventListener("DOMContentLoaded", async function() {
    var elements = document.getElementsByClassName("sk-text");

    for (var i = 0; i < elements.length; i++) {
        var id = elements[i].className.replace("sk-text ", "");
        elements[i].children[0].setAttribute("id", id);
        const editor = await init(document.querySelector( "#"+id ));

        editor.model.document.on( 'change:data', () => {
            const id = editor.sourceElement.getAttribute("id");
            console.log(id);
            //console.log( document.getElementById("sk-mjml-template").querySelector('[css-class="sk-text '+id+'"]').innerHTML );
            document.getElementById("sk-mjml-template").querySelector('[css-class="sk-text '+id+'"]').innerHTML = editor.sourceElement.innerHTML;

        });

    }
});

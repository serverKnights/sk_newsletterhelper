const init = div =>
    InlineEditor.create(div, {
        toolbar: [
            "fontSize",
            "bold",
            "italic",
            "link",
            "bulletedList",
            "numberedList",
            "undo",
            "redo"
        ],

        fontSize: {
            options: [
                'default',
                10, 12, 14, 'default', 17, 19, 21, 23, 25, 27, 29, 31, 33, 35
            ],
            supportAllValues: true
        },
        // Configure General HTML Support
        htmlSupport: {
            allow: [
                {
                    name: 'p',
                    styles: true,
                    classes: true,
                    attributes: true
                },
                // ... other elements you want to allow
            ]
        }
    });

document.addEventListener("DOMContentLoaded", async function() {
    var elements = document.getElementsByClassName("sk-text");
    const styleTag = document.createElement('style');
    styleTag.innerHTML = '.ck-content { padding: 0 !important; }'; // Your CSS rule
    document.getElementsByTagName('head')[0].appendChild(styleTag);
    for (var i = 0; i < elements.length; i++) {
        var id = elements[i].className.replace("sk-text ", "");
        elements[i].children[0].setAttribute("id", id);
        const editor = await init(document.querySelector( "#"+id ));
        editor.sourceElement.style.setProperty('padding', '0', 'important');
        editor.model.document.on( 'change:data', () => {
            const id = editor.sourceElement.getAttribute("id");
            //console.log( document.getElementById("sk-mjml-template").querySelector('[css-class="sk-text '+id+'"]').innerHTML );
            document.getElementById("sk-mjml-template").querySelector('[css-class="sk-text '+id+'"]').innerHTML = editor.sourceElement.innerHTML;

        });

    }
});

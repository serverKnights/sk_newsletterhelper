
document.addEventListener("DOMContentLoaded", function() {
  //  var element = document.getElementsByClassName("sk-first-text");
  //  console.log(element);


    var elements = document.getElementsByClassName("sk-text");

    for (var i = 0; i < elements.length; i++) {
        var id = "sk-text-" + (i + 1);
        console.log(elements[i].children);
        elements[i].children[0].setAttribute("id", id);

        InlineEditor
            .create( document.querySelector( "#"+id ) )
            .catch( error => {
                console.error( error );
            } );
    }


});

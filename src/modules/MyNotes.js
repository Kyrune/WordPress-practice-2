import $ from 'jquery';
class MyNotes {
    constructor() {
        // alert("Hello from MyNotes");
        this.events();
    }


    events() {
        $(".delete-note").on("click", this.deleteNote);
        $(".edit-note").on("click", this.editNote);
    }

    // Custom methods
    deleteNote(e) {
        // alert("you clicked delete");
        var thisNote = $(e.target).parents("li");

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'DELETE',
            success: (response) => {
                thisNote.slideUp();
                console.log("Congrats");
                console.log(response);
            },
            error: (response) => {
                console.log("Sorry");
                console.log(response);
            }
        });
    }
}

export default MyNotes;

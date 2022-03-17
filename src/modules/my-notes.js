import $ from 'jquery';
class MyNotes {
    constructor() {
        // alert("Hello from MyNotes");
        this.events();
    }


    events() {
        $(".delete-note").on("click", this.deleteNote);
    }

    // Custom methods
    deleteNote() {
        // alert("you clicked delete");
    }
}

export default MyNotes;

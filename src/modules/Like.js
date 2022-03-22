import $ from 'jquery';

class Like {
    constructor() {
        alert("testing from like.js");
    }

    events() {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this))
    }

    // Custom methods
    ourClickDispatcher() {

    }
}

export default Like;

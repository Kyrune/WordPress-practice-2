import $ from 'jquery';

class Like {
    constructor() {
        this.events();
    }

    events() {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this))
    }

    // Custom methods
    ourClickDispatcher(e) {
        var currentLikeBox = $(e.target).closest(".like-box");

        if (currentLikeBox.data('exists') == 'yes') {
            this.deleteLike();
        } else {
            this.createLike();
        }
    }

    createLike() {
        $.ajax({
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'POST',
            success: (response) => {
                console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
        });
    }

    deleteLike() {
        alert("delete test");
    }
}

export default Like;

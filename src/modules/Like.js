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
            this.deleteLike(currentLikeBox);
        } else {
            this.createLike(currentLikeBox);
        }
    }

    createLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'POST',
            data: {'professorId': currentLikeBox.data('professor')},
            success: (response) => {
                // Fill in heart icon color
                currentLikeBox.attr('data-exists', 'yes');
                // Fetches number of Likes
                var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
                // Update number of Likes
                likeCount++;
                currentLikeBox.find(".like-count").html(likeCount);
                currentLikeBox.attr("data-like", response);
                console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
        });
    }

    deleteLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            data: {'like': currentLikeBox.attr('data-like')},
            type: 'DELETE',
            success: (response) => {
                // Removes heart icon color fill
                currentLikeBox.attr('data-exists', 'no');
                // Fetches number of Likes
                var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
                // Update number of Likes
                likeCount--;
                currentLikeBox.find(".like-count").html(likeCount);
                currentLikeBox.attr("data-like", '');
                console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
        });
    }
}

export default Like;

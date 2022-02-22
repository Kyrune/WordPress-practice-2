import $ from 'jquery';

class Search {
    // 1. describe and create/initiate our object
    constructor() {
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
    }

    // 2. events 
    events() {
        this.openButton.on("click", this.openOverlay);
        this.closeButton.on("click", this.closeOverlay);
    }

    // 3. methods (function, action...)
    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
    }

    closeOverlay() {

    }
}

export default Search;

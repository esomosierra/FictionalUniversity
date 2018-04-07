(function($) {

    $(document).ready(function() {
        
        class Search {

            /* Secion 1.) Describe and create our object. */
            constructor() {
               this.openButton = $('.js-search-trigger');
               this.closeButton = $('.search-overlay__close');
               this.searchOverlay = $('.search-overlay');
               this.resultsDiv = $('#search-overlay__results');
               this.searchField = $('.search-overlay');
               this.isOverlayOpen = false; // Current state of search overlay div.
               this.isSpinnerVisible = false; // Curerent state of spinner icon.
               this.previousValue;
               this.typingTimer;
               this.events();

            }
        
        
            /*=== Secion 2.) Handles all events. ===*/

            events() {
                // Open and Close Search Overlay Events.
                this.openButton.on('click', this.openOverlay.bind(this));
                this.closeButton.on('click', this.closeOverlay.bind(this));
                this.searchField.on('keyup', this.typingLogic.bind(this));
                $(document).on('keydown', this.keyPressDispatcher.bind(this));
            }
        
            /*=== Secion 3.) Methods or Functions or Actions that fires event. ===*/

            typingLogic() {
                if (this.searchField.val() != this.previousValue) { // Only if the current value does not equal to the previousValue
                    
                    clearTimeout(this.typingTimer);

                    if (this.searchField.val()) { // If the search field is not blank or empty, then

                        if (!this.isSpinnerVisible) { // Same as: if isSpinnerVisible == false concept. If spinner is not currently visible. Then,
                            this.resultsDiv.html('<div class="spinner-loader"></div>'); // Show the spinner.
                            this.isSpinnerVisible = true; // Change the state to TRUE to show it.
                        }
                        this.typingTimer = setTimeout(this.getSearchResults.bind(this), 2000); // Typing timer.

                    } else { // Otherwise, remove the content inside resultsDiv.
                        this.resultsDiv.html() = '';
                        this.isSpinnerVisible = false; // Change the state of spinner to FALSE to hide it.
                    }
                    
                }
                
                this.previousValue = this.searchField.val(); // Get the value that user typed in and store it in 'previousValue' property.
            }

            getSearchResults() {
                this.resultsDiv.html('Imagine real search results here.');
                this.isSpinnerVisible = false; // Change the state to false after the user finished typing and search results show up.
            }
            
            openOverlay() {
                this.searchOverlay.addClass('search-overlay--active');
                $('body').addClass('body-no-scroll');
                this.isOverlayOpen = true;
            }
        
            closeOverlay() {
                this.searchOverlay.removeClass('search-overlay--active');
                $('body').removeClass('body-no-scroll');
                this.isOverlayOpen = false;
            }

            keyPressDispatcher(e) { // Keyboard shortcut for Search as 's' and Escape as 'esc' key.
                if (e.keyCode == 83 && !this.isOverlayOpen) { // or if this.isOverlayOpen == false
                    this.openOverlay();
                }
                if (e.keyCode == 27 && this.isOverlayOpen) {
                    this.closeOverlay();
                }
            }
        }
        
        var search = new Search();

    });

})(jQuery);
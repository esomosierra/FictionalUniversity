(function($) {

    $(document).ready(function() {

        class Search {
        
            /* === SECTION 1.) Describe and create our object. === */

            constructor() {
                this.addSearchHTML();
                this.resultsDiv = $("#search-overlay__results");
                this.openButton = $(".js-search-trigger");
                this.closeButton = $(".search-overlay__close");
                this.searchOverlay = $(".search-overlay");
                this.searchField = $("#search-term");
                this.events();
                this.isOverlayOpen = false; // Current state of search overlay div.
                this.isSpinnerVisible = false; // Curerent state of spinner icon.
                this.previousValue;
                this.typingTimer;
            }

            /*=== SECTION 2.) Handles all events. ===*/

            events() {
                this.openButton.on("click", this.openOverlay.bind(this));
                this.closeButton.on("click", this.closeOverlay.bind(this));
                $(document).on("keydown", this.keyPressDispatcher.bind(this));
                this.searchField.on("keyup", this.typingLogic.bind(this));
            }
            

            /*=== SECTION 3.) Methods or Functions or Actions that fires event. ===*/

            typingLogic() { // Only if the current value does not equal to the previousValue
                if (this.searchField.val() != this.previousValue) {
                    clearTimeout(this.typingTimer);

                    if (this.searchField.val()) { // If the search field is not blank or empty, then
                        if (!this.isSpinnerVisible) { // Same as: if isSpinnerVisible == false concept. If spinner is not currently visible. Then,
                        this.resultsDiv.html('<div class="spinner-loader"></div>'); // Show the spinner.
                        this.isSpinnerVisible = true; // Change the state to TRUE to show it.
                        }
                        this.typingTimer = setTimeout(this.getResults.bind(this), 500); // Typing timer.
                    } else { // Otherwise, remove the content inside resultsDiv.
                        this.resultsDiv.html('');
                        this.isSpinnerVisible = false; // Change the state of spinner to FALSE to hide it.
                    }
                }

                this.previousValue = this.searchField.val(); // Get the value that user typed in and store it in 'previousValue' property.
            }

            getResults() {
                /**
                 *  Make the url relative using "wp_localize_script" in functions.php
                 *  wp_localize_script() -> Localizes a registered script with data for a JavaScript variable. "universityData.root_url"
                 */

                // MULTIPLE POSTS TYPE REQUESTS USING OUR OWN CUSTOM SEARCH ROUTE. 'wp_localize_script' functions.php line 30 //
                // @param: results = This will passed to the anonymous function that will hold all the data arrays of getJSON URL.
                $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), (results) => {
                    this.resultsDiv.html(`
                        <div class='row'>
                            <div class='one-third'>
                                <h2 class="search-overlay__section-title">General Information</h2>
                                ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search</p>'}
                                ${results.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a>${item.postType == 'post' ? ` by ${item.authorName}` : ''}</li>`).join('')}
                                ${results.generalInfo.length ? '</ul>' : ''}
                            </div>
                            <div class='one-third'>
                                <h2 class="search-overlay__section-title">Programs</h2>
                                ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs matches that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
                                ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                                ${results.programs.length ? '</ul>' : ''}

                                <h2 class="search-overlay__section-title">Professors</h2>
                                ${results.professors.length ? '<ul class="professor-cards">' : '<p>No professors match that search.</p>'}
                                ${results.professors.map(item => `
                                    <li class="professor-card__list-item">
                                        <a class="professor-card" href="${item.permalink}">
                                            <img src="${item.image}" alt="" class="professor-card__image">
                                            <span class="professor-card__name">${item.title}</span>
                                        </a>
                                    </li>
                                `).join('')}
                                ${results.professors.length ? '</ul>' : ''}
                            </div>
                            <div class='one-third'>
                                <h2 class="search-overlay__section-title">Campuses</h2>
                                ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses match that search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
                                ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                                ${results.campuses.length ? '</ul>' : ''}

                                <h2 class="search-overlay__section-title">Events</h2>
                                ${results.events.length ? '' : `<p>No events match that search. <a href="${universityData.root_url}/events">View all events</a></p>`}
                                ${results.events.map(item => `
                                    <div class="event-summary">
                                        <a class="event-summary__date t-center" href="${item.permalink}">
                                            <span class="event-summary__month">${item.month}</span>
                                            <span class="event-summary__day">${item.day}</span>  
                                        </a>
                                        <div class="event-summary__content">
                                            <h5 class="event-summary__title headline headline--tiny">
                                            <a href="${item.permalink}">${item.title}</a>
                                            </h5>
                                            <p>${item.description}<a href="${item.permalink}" class="nu gray">Learn more</a></p>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `);

                    this.isSpinnerVisible = false;
                });


                // MULTIPLE POSTS TYPE REQUESTS OF THE DEFAULT NAMESPACE 'WP' GETJSON URL. //
                // $.when(
                //     $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search=' + this.searchField.val()),
                //     $.getJSON(universityData.root_url + '/wp-json/wp/v2/pages?search=' + this.searchField.val())
                // ).then( (posts, pages) => {
                //     var combinedResults = posts[0].concat(pages[0]);
                //     this.resultsDiv.html(`
                //         <h2 class="search-overlay__section-title">General Information</h2> 
                //         ${combinedResults.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search</p>'}
                //         ${combinedResults.map(item => `<li><a href="${item.link}">${item.title.rendered}</a> ${item.type == 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
                //         ${combinedResults.length ? '</ul>' : ''}
                //     `);
                //     this.isSpinnerVisible = false;
                // }, () => {
                //     this.resultsDiv.html('<p>Unexpected error: please try again.</p>');
                // });
                

                // SINGLE OR SPECIFIC POST TYPE GETJSON REQUEST //
                // $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search=' + this.searchField.val(), posts => {
                // /* -- Displays the result of getJSON    -- It uses a javascript template literals   -- Conditional template literals using Ternary Operator. */ 
                //     this.resultsDiv.html(`
                //         <h2 class="search-overlay__section-title">General Information</h2>
                //         ${posts.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search</p>'}
                //             ${posts.map(item => `<li><a href="${item.link}">${item.title.rendered}</a></li>`).join('')}
                //         ${posts.length ? '</ul>' : ''}
                //     `);
                //     this.isSpinnerVisible = false; // Change the state to false after the user finished typing and search results already show up.
                // });
            }

            keyPressDispatcher(e) { // Keyboard shortcut for Search as 's' and Escape as 'esc' key.
                if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) { // or if this.isOverlayOpen == false (!this.isOverlayOpen)
                this.openOverlay();
                }

                if (e.keyCode == 27 && this.isOverlayOpen) {
                this.closeOverlay();
                }

            }

            openOverlay() {
                this.searchOverlay.addClass("search-overlay--active");
                $("body").addClass("body-no-scroll");
                setTimeout(() => this.searchField.focus(), 501);
                this.searchField.val('');
                this.isOverlayOpen = true;
                return false; // Disable javascript href link
            }

            closeOverlay() {
                this.searchOverlay.removeClass("search-overlay--active");
                $("body").removeClass("body-no-scroll");
                console.log("our close method just ran!");
                this.isOverlayOpen = false;
            }

            addSearchHTML() {
                /*-- # This is the SEARCH OVERLAY section # */
                $('body').append(`
                    <div class="search-overlay">
                        <!-- Search Overlay Container-->       
                        <div class="search-overlay__top">
                            <div class="container">
                                <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                                <input type="text" class="search-term" placeholder="What are you looking for" id="search-term">
                                <i class="fa fa-close search-overlay__close" aria-hidden="true"></i>
                            </div>
                        </div>
                        
                        <!-- Search Overlay Result Container-->
                        <div class="container">
                            <div id="search-overlay__results">
                                
                            </div>
                        </div>
                    </div>
                `);
            }
        }
        
        var search = new Search();

    });

})(jQuery);
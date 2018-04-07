(function($) {

    $(document).ready(function() {
        class Like {

            /* == INITIALIZE CLASS === */

            constructor() {
                this.events();
            }


            /* === EVENTS HANDLER === */

            events() {
                $('.like-box').on('click', this.ourClickDispatcher.bind(this));    
            }


            /* === FUNCTIONS OR METHODS === */

            ourClickDispatcher(e) {
                var currentLikeBox = $(e.target).closest('.like-box'); // Points to the overall span parent like-box.

                // Passed 'currentLikeBox' as argument to access the "data-exist" attribute. 'Yes' if does have already liked. 'No' if does not have a liked.
                if($(currentLikeBox).attr('data-exists') == 'yes') {
                    this.deleteLike(currentLikeBox); 
                } else {
                    this.creatLike(currentLikeBox); 
                }
            }

            // Overall span parent like-box or "currentLikeBox" as parameter to access any "data-attribute" to be use in sending ajax request to the php server.
            creatLike(currentLikeBox) { 
                $.ajax({
                    beforeSend: (xhr) => {
                        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                    },
                    url: universityData.root_url + '/wp-json/university/v1/manageLike',
                    type: 'POST',
                    data: {
                        'professorId': currentLikeBox.data('professor') // data-attribute="<?php the_ID(); ?>" in single-professor.php for professor being 'liked' by the user.
                    },
                    success: (response) => {
                        currentLikeBox.attr('data-exists', 'yes');
                        // Human readable base 10 number. Converts the like-count icon value to Interger Number.
                        var likeCount = parseInt(currentLikeBox.find('.like-count').html(), 10); 
                        likeCount++;
                        currentLikeBox.find('.like-count').html(likeCount); // Set the actual count of the like-count icon value.
                        
                        // Set the ajax response as a value attribute of 'data-like' = response
                        // Because if success, the server 'response' with the ID number of that newly created like post.
                        currentLikeBox.attr('data-like', response); 

                        console.log(response);
                    },
                    error: (response) => {
                        console.log(response);
                    }
                });
            }

            deleteLike(currentLikeBox) {
                $.ajax({
                    beforeSend: (xhr) => {
                        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                    },
                    url: universityData.root_url + '/wp-json/university/v1/manageLike',
                    type: 'DELETE',
                    data: {'like': currentLikeBox.attr('data-like')}, // AJAX Request that talks to the PHP Server.
                    success: (response) => {
                        currentLikeBox.attr('data-exists', 'no');
                        // Human readable base 10 number. Converts the like-count icon value to Interger Number.
                        var likeCount = parseInt(currentLikeBox.find('.like-count').html(), 10); 
                        likeCount--;
                        currentLikeBox.find('.like-count').html(likeCount); // Set the actual count of the like-count icon value.
                        currentLikeBox.attr('data-like', '');

                        console.log(response);
                    },
                    error: (response) => {
                        console.log(response);
                    }
                });
            }

        }

        var like = new Like();

    });

})(jQuery);
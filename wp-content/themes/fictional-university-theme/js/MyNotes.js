(function($) {

    $(document).ready(function() {

        class MyNotes {

            constructor() {
                this.events()
            }

            /* EVENTS HANDLERS GOES HERE */
            events() {
                // EVENT DELEGATION WITH ADDITIONAL PARAMETER.
                $('#my-notes').on('click', '.delete-note', this.deleteNote);
                $('#my-notes').on('click', '.edit-note', this.editNote.bind(this));
                $('#my-notes').on('click', '.update-note', this.updateNote.bind(this));
                $('.submit-note').on('click', this.createNote.bind(this));
            }


            /* FUNCTIONS AND METHODS GOES HERE */

            // EDIT NOTE: WILL ENABLE AND DISABLE THE FIELD FROM READONLY TO EDITABLE AND VICE VERSA.
            editNote(e) {
                // Storing the location of edit button's parent which is '<li>' that has data attribute of 'data-id=<?php the_ID() ?>
                // And this dynamic ID from php will be use in rest api url to edit specific notes.
                var thisNote = $(e.target).parents('li'); // e.target -> Refers to the edit button.

                if (thisNote.data('state') == 'editable') {
                    this.makeNoteReadOnly(thisNote);
                } else {
                    this.makeNoteEditable(thisNote);
                } 
            }
            
            makeNoteEditable(thisNote) {
                // Disabled the 'readonly' field and make it editable.
                thisNote.find('.note-title-field, .note-body-field').removeAttr('readonly').addClass('note-active-field');

                // Finds and change the html content of Edit button.
                thisNote.find('.edit-note').html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');

                // Adds class to Edit note button.
                thisNote.find('.update-note').addClass('update-note--visible');

                /* ADDS DATA ATTRIBUTE 'STATE' WITH A VALUE OF 'EDITABLE */
                thisNote.data('state', 'editable'); // Will be use as condition in, if else block of editNote()
            }

            makeNoteReadOnly(thisNote) {
                // Disabled the 'readonly' field and make it editable.
                thisNote.find('.note-title-field, .note-body-field').attr('readonly', 'readonly').removeClass('note-active-field');

                // Finds and change the html content of Edit button.
                thisNote.find('.edit-note').html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');

                // Adds class to Edit note button.
                thisNote.find('.update-note').removeClass('update-note--visible');

                /* ADDS DATA ATTRIBUTE 'STATE' WITH A VALUE OF 'CANCEL */
                thisNote.data('state', 'cancel'); // Will be use as condition in, if else block of editNote()
            }


            // CREATE NOTE:
            createNote(e) {
                // This will be the data to be send in WP REST API '/wp-json/wp/v2/posts'
                var ourNewPost = {
                    'title'  :   $('.new-note-title').val(),
                    'content':   $('.new-note-body').val(),
                    'status' :   'publish'
                }

                $.ajax({
                    beforeSend: (xhr) => {
                        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                    },
                    url: universityData.root_url + '/wp-json/wp/v2/note/',
                    type: 'POST',
                    data: ourNewPost,
                    success: (response) => {
                        $('.new-note-title, .new-note-body').val('');
                        $(`
                            <li data-id="${response.id}">
                                <input readonly class="note-title-field" value="${response.title.raw}">
                                <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                                <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                                <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                                <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                            </li>
                        `).prependTo('#my-notes').hide().slideDown();
                        console.log('Congrats');
                        console.log(response);
                    },
                    error: (response) => {
                        // Displays a message in the my notes form section if user reached note limits.
                        if (response.responseText == 'You have reached your note limit!') {
                            $('.note-limit-message').addClass('active');
                        }
                        console.log('Sorry');
                        console.log(response);
                    }
                });
            }

            
            // UPDATE NOTE:
            updateNote(e) {
                // Storing the location of update button's parent which is '<li>' that has data attribute of 'data-id=<?php the_ID() ?>
                // And this dynamic ID from php will be use in rest api url to update specific notes.
                var thisNote = $(e.target).parents('li'); // e.target -> Refers to the update button.

                // This will be the data to be send in WP REST API '/wp-json/wp/v2/posts'
                var ourUpdatedPost = {
                    'title':   thisNote.find('.note-title-field').val(),
                    'content': thisNote.find('.note-body-field').val()
                }

                $.ajax({
                    beforeSend: (xhr) => {
                        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                    },
                    url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
                    type: 'POST',
                    data: ourUpdatedPost,
                    success: (response) => {
                        this.makeNoteReadOnly(thisNote);
                        console.log('Congrats');
                        console.log(response);
                    },
                    error: (response) => {
                        console.log('Sorry');
                        console.log(response);
                    }
                });
            }

            
            // DELETE NOTE:
            deleteNote(e) {
                // Storing the location of delete button's parent which is '<li>' that has data attribute of 'data-id=<?php the_ID() ?>
                // And this dynamic ID from php will be use in rest api url to delete specific notes.
                var thisNote = $(e.target).parents('li'); // e.target -> Refers to the delete button.

                $.ajax({
                    beforeSend: (xhr) => {
                        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                    },
                    url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
                    type: 'DELETE',
                    success: (response) => {
                        thisNote.slideUp();
                        console.log('Congrats');
                        console.log(response);

                        // Remove the error message in the my notes form section if user not yet reach note limits.
                        if (response.userNoteCount < 5) {
                            $('.note-limit-message').removeClass('active');
                        }

                    },
                    error: (response) => {
                        console.log('Sorry');
                        console.log(response);
                    }
                });
            }

        }

        var myNote = new MyNotes();

    });

})(jQuery);
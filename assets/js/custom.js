
(function($) {
	"use strict";

    // This functions opens the next column or field in the search
    function openNextColumn(currentColumn,nextColumn,array,toolTip) {
        // Enable select dropdown of next field
        nextColumn.removeClass('text-muted');  
        nextColumn.find('.form-select').prop('disabled', false);
        // Disable tooltip of next field since it's next to be active
        nextColumn.tooltip('disable');
        //Update tooltip of the rest             
        $.each( array, function( i, item ) {
            // Only Update tooltips of disabled or muted fields
            if( $( '.' + item.selector ).is('.text-muted')) {
                // apply new tooltip to disabled fields    
                $( '.' + item.selector ).attr({
                    'data-bs-original-title':toolTip,            
                });
            }                    
        });
        //Remove highlight                                                            
        currentColumn.find('.border').removeClass('border-primary').addClass('border-light');
        //Move highlight to the next one
        currentColumn.next().find('.border').removeClass('border-light').addClass('border-primary');
        // Focus on current Field upon opening of next column
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });        
    }

    // This function populates the column or field selection for event types and venue types
    function populateField(selected,array1,array2,nextField,column,toolTip){
        var existsInArray1 = false;
        var existsInArray2 = false;
        $.each( array1, function( i, array1_item ) {
            if(array1_item == selected) {
                existsInArray1 = true;
            }                 
        });
        $.each( array2, function( i, array2_item ) {
            if(array2_item == selected) {
                existsInArray2 = true;
            }                 
        });
        if(existsInArray1 === true && existsInArray2 === false) {           
            nextField.find('.form-select').val(0);
            nextField.find('.form-select').trigger('change'); // Notify any JS components that the value changed
            // Move to the next available selection
            openNextColumn(nextField,nextField.next(),column,toolTip);                   
        } else if(existsInArray1 === false && existsInArray2 === true) {                    
            nextField.find('.form-select').val(1);
            nextField.find('.form-select').trigger('change'); // Notify any JS components that the value changed
            // Move to the next available selection
            openNextColumn(nextField,nextField.next(),column,toolTip);
        }   
    }

    function getArrayFromJson(cat) {
        var cat_url = 'https://vivace.rrwebstudio.com/?generate_json='+cat;
        var cat_array = [];
        $.getJSON(cat_url, function(result) {
            cat_array.push(result);
        });
        return cat_array;
    }

    // Search Form Validation and Appearance
    // HTML select or dropdown is being replaced my select2 and jquery
    function select2Func() {

        // Just some arrays and objects to population the form:

        // formCols = the div columns in the search form
        // Contain objects for selector or element class, placeholder for option/dropwdown, description or instruction, and toolTip
        var formCols = [
            {
                'selector' : 'col-event-cat',
                'placeholder' : 'What do you need the equipments for?',
                'inputDesc' : 'Please enter your event category.',
                'toolTip' : 'You must select event category first.',
            },
            {
                'selector' : 'col-event-type',
                'placeholder' : 'Local or foreign?',
                'inputDesc' : 'Please enter your event type.',
                'toolTip' : 'You must select event type first.',
            },
            {
                'selector' : 'col-venue-cat',
                'placeholder' : 'What is your venue?',
                'inputDesc' : 'Please enter your venue category.',
                'toolTip' : 'You must select venue category first.',
            },
            {
                'selector' : 'col-venue-type',
                'placeholder' : 'Indoor or outdoor?',
                'inputDesc' : 'Please enter your venue type.',
                'toolTip' : 'You must select venue type first.',
            },
            {
                'selector' : 'col-price-range',
                'placeholder' : 'Select your price range',
                'inputDesc' : 'Please enter your price range.',
                'toolTip' : 'You must select your price range.',
            }
        ];

        // Event, Venue, Price arrays
        // This is needed to make the switch between form columns possible
        // and for validation as well.
        //var event_cat = getArrayFromJson('event_cat');
        //var event_types = getArrayFromJson('event_types');
        var foreign_events = getArrayFromJson('foreign_events');
        var local_events = getArrayFromJson('local_events');
        //var venue_cat = getArrayFromJson('venue_cat');
        //var venue_type = getArrayFromJson('venue_type');
        var indoor_venue = getArrayFromJson('indoor_venue');
        var outdoor_venue = getArrayFromJson('outdoor_venue');
        //var price_range = getArrayFromJson('price_range');
        //var price_range_foreign = getArrayFromJson('price_range_foreign');

        // Run through each columns (field) aka 'formCols' in the search form
        $.each( formCols, function( i, Col ) {    

            // Call select2 to beautify/replace the default html select in each column, with autocomplete functionality
            // Select2 is a JQuery plugin            
            // There is an html select in each column, let's find it and invoke select2 plugin
            // Documentation: https://select2.org/
            if(Col.selector !== 'col-price-range') {
                $( '.' + Col.selector ).find('.form-select').select2({
                placeholder: Col.placeholder,
                allowClear: true,
                minimumInputLength: 1,
                language: {
                    inputTooShort: function() {
                        return Col.inputDesc;
                    }
                }
            });
            } else {
                $( '.' + Col.selector ).find('.form-select').select2({
                placeholder: Col.placeholder,
                allowClear: true,
            });
            }          

            // In each column, do something when a selection is selected from a dropdown such as:
            // Apply applicable tooltip to the next field for validation / warning / instructions            
            if( $( '.' + Col.selector ).find('.form-select').hasClass('select2-hidden-accessible') ) {
                // Apply applicable toolTip to the next column/field
                // .text-muted is the name of the class that indicates a column is 'disabled'
                if( $( '.' + Col.selector ).is('.text-muted')) {
                    // Get tooltip text from current active field or the select field that has just been selected
                    var toolTip = null;            
                    $.each( formCols, function( i, Col2 ) {
                        if( !$( '.' + Col2.selector ).is('.text-muted')) {                  
                            toolTip = Col2.toolTip;
                        }                        
                    });
                    //  apply the taken tooltip text to the next disabled fields/param
                    // tootips are for instructions or warning
                    $( '.' + Col.selector ).attr({
                        'data-toggle':'tooltip',
                        'data-placement':'top',
                        title:toolTip,            
                    });                    
                }               
            }

            // Enable the next field to be answered
            // See select2 documentation for select2 events
            // select2:select means a selection from the dropdown has just been selected
            // Populate next field according to selected option that just got selected.
            // Eg. set event as foreign automatically when event selected is tagged as foreign
            $( '.' + Col.selector ).find('.form-select').on('select2:select', function (e) {
                //console.log($(this));
                // next - next item in formCols array
                var next = formCols[i+1];
                var next_next = formCols[i+2];
                // get next item's tooltip
                var toolTip = next.toolTip ? next.toolTip : null;
                var next_toolTip = next_next.toolTip ? next_next.toolTip : null;
                // get the next column/field
                var next_field = $( '.' + Col.selector ).next();
                // Enable select dropdown of next field
                openNextColumn($( '.' + Col.selector ),next_field,formCols);                
                // Get selected value from current column/selection
                var selected = $( '.' + Col.selector ).find('.form-select').select2('data');
                //Set event type
                populateField(selected[0].text,local_events[0],foreign_events[0],next_field,formCols,next_toolTip);
                //Set venue type
                populateField(selected[0].text,indoor_venue[0],outdoor_venue[0],next_field,formCols,next_toolTip);

            });

            $( '.' + Col.selector ).find('.form-select').on('select2:open', function (e) {
                if( $('#event_type').find(':selected').text() == 'Foreign' ){
                    $('#price_range option:first-child').hide();
                    $('#select2-price_range-results').addClass('foreign').removeClass('local');
                }

                if( $('#event_type').find(':selected').text() == 'Local' ){
                    $('#price_range option:first-child').show();
                    $('#select2-price_range-results').removeClass('foreign').addClass('local');
                }
            });

            // When a selection is cleared the succeeding selections must be cleared as well
            $( '.' + Col.selector ).find('.form-select').on('select2:clear', function (e) {
                //var next = formCols[i+1];
                //console.log($(this).parent('.border'));                

                // Run through remaining columns to clear it's selection
                for(var n = i; n < (formCols.length); n++) {
                    $( '.' + formCols[n].selector ).next().addClass('text-muted'); // add text-muted class to disable cleared columns
                    $( '.' + formCols[n].selector ).find('.form-select').val(null).trigger('change'); // reset select2 selected option
                    $( '.' + formCols[n].selector ).find('.form-select').prop('disabled', true); // disable html select for the cleared columns
                    $( '.' + formCols[n].selector ).tooltip('enable'); // enable tooltip for the cleared columns to display instructions/warning
                    $( '.' + formCols[n].selector ).find('.border').removeClass('border-primary').addClass('border-light'); //
                }
                
                // move highlight back to cleared field.. it becomes the current field now
                $(this).parent('.border').removeClass('border-light').addClass('border-primary');

                // Enable the select dropdown to the column/field that's been reset.
                $( '.' + Col.selector ).find('.form-select').prop('disabled', false);                
            });
        });

    }

    $(document).ready(function() {   
                
        // select2 on register page
        //$('#account_type').select2();
        
        // Disable Other select except the first
        $('#search-section .col, #search-section .col-2, #search-section .col-auto').not(':first-child').find('.form-select').prop('disabled', true);
            
        // Enable Tootltips
        $(function () {
            $('[data-toggle=tooltip]').tooltip()
        });

        // Call select2 custom function
        select2Func();

        // Validate search form on submission
        // Run through all the fields and check if the fields are answered
        // Show error when not
        $(document).on('submit', '#search_form', function(e) {
            var inputs = $(this).find(':input');
            if(inputs.val().length == 0) {
                e.preventDefault();
                $('.validation-msg').html('All fields are required.');                
            }
        });

        // Focus on current Field upon page load
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        // Registered emails array
        var registeredEmails = getArrayFromJson('user_emails');
        //console.log(registeredEmails);
        $.validator.addMethod('alreadyexist', function(value, element) {
            return registeredEmails[0].indexOf(value) == -1;
        }, 'The email is already taken.');

        // jQuery validation - register form
        $('#register_form').validate({
            errorClass: 'border-danger text-danger',
            rules: {
                'email_address': {
                    required: true,
                    alreadyexist: true
                },
                'password': {
                    minlength: 5,
                },
                'password_confirm': {
                    minlength: 5,
                    equalTo: "#password"
                }
                
            },
            errorPlacement: function(error, element) { },
        });

        // jQuery validation - login form
        $('#login_form').validate({
            errorClass: 'border-danger text-danger',
        });

        //Post Rental Form
        $('#post_form #event_category').select2({
            placeholder: 'Select Event Type',
        });

        $('#post_form #venue_category').select2({
            placeholder: 'Select Venue Type',
        });     

        // jQuery validation - post form
        var local_events = getArrayFromJson('local_events');
        var foreign_events = getArrayFromJson('foreign_events');        
        var indoor_venue = getArrayFromJson('indoor_venue');
        var outdoor_venue = getArrayFromJson('outdoor_venue');

        $('#post_form').validate({
            errorClass: 'border-danger text-danger',
        });

        var isForeignEvent = false;
        var isLocalEvent = false;
        var isIndoorEvent = false;
        var isOutdoorEvent = false;
        $( '#post_form #event_category').on('select2:select', function () {
            var selected = $(this).select2('data');
            $.each( local_events[0], function( i, local_event ) {
                if(local_event == selected[0].text) {                    
                    isLocalEvent = true;
                }                 
            });
            $.each( foreign_events[0], function( i, foreign_event ) {
                if(foreign_event == selected[0].text) {
                    isForeignEvent = true;
                }                 
            });
            if(isLocalEvent === true && isForeignEvent === false) {           
                $( '#post_form #event_type_Local').prop( "checked", true ).trigger('change'); 
                $( '#post_form #event_type_Foreign').prop( "checked", false ).trigger('change');    
            } else if(isLocalEvent=== false && isForeignEvent === true) {                    
                $( '#post_form #event_type_Foreign').prop( "checked", true ).trigger('change'); 
                $( '#post_form #event_type_Local').prop( "checked", false ).trigger('change');  
            }  
        });
        $( '#post_form #venue_category').on('select2:select', function () {
            var selected = $(this).select2('data');
            $.each( indoor_venue[0], function( i, indoor_venue ) {
                if(indoor_venue == selected[0].text) {
                    isIndoorEvent = true;
                }                 
            });
            $.each( outdoor_venue[0], function( i, outdoor_venue ) {
                if(outdoor_venue == selected[0].text) {
                    isOutdoorEvent = true;
                }                 
            });
            if(isIndoorEvent === true && isOutdoorEvent === false) {           
                $( '#post_form #venue_type_Indoor').prop( "checked", true ).trigger('change');
                $( '#post_form #venue_type_Outdoor').prop( "checked", false ).trigger('change');   
            } else if(isIndoorEvent === false && isOutdoorEvent === true) {                    
                $( '#post_form #venue_type_Outdoor').prop( "checked", true ).trigger('change'); 
                $( '#post_form #venue_type_Indoor').prop( "checked", false ).trigger('change');   
            }  
        });

        // Call tinymice editor
        tinymce.init({
            selector: 'textarea#set_desc',
            promotion: false,
            toolbar: 'undo redo | bold italic alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
        });
        
    });

})(jQuery)
// require('./bootstrap');

/**
 * Functions
 */
function notify(title, message, icon, type) {

    // Set options
    var options = {
        title: title,
        message: message,
        icon: icon,
        target: "_blank"
    };

    // Set settings
    var settings = {
        element: 'body',
        type: type,
        position: 'fixed',
        placement: {
            from: "top",
            align: "left"
        },
        z_index: 10000,
        delay: 5000,
        url_target: '_blank',
        animate: {
            enter: "animated fadeInDown",
            exit: "animated fadeOutUp"
        },
        template: ' <div data-notify="container" class="col-xs-11 col-sm-2 alert alert-{0}" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            '</div>'
    };

    // Show notify
    $.notify(options, settings);

}

/**
 * Bootstrap activate tabs
 */
(function($) {

    // Get triggerTabList
    var triggerTabList = [].slice.call(document.querySelectorAll('.nav-tabs a'));

    // Loop over triggerTabList
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

}(jQuery));

/**
 * Update amount of tasks
 */
(function($) {

    // Set variables
    var amountOfTasks;
    var selected;

    // Fill variables
    amountOfTasks = $("select[name='amountOfTasks']");

    // On change
    amountOfTasks.on("change", function(){

        // Get selected
        selected = $(this).val();

        // Ajax call
        $.ajax({
            url: '/api/user/update/amountoftasks',
            type: 'POST',
            data: { amount: selected },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(result) {
                // Success
            },
            error: function(request,error) {
                // Error
            }
        });

    });

}(jQuery));

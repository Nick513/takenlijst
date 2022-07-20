/**
 * Define global variables
 */
var snippet;

/**
 * Fill global variables
 */
snippet = $.ajax({
    url: '/api/tasks/snippet',
    type: 'GET',
    data: { empty: true },
    success: function(html) {
        // Success
    },
    error: function(request,error) {
        // Error
    }
});

/**
 * Functions
 */

// Get tasks from database
function getTasksFromDatabase() {

    // Set variables
    var rawData;
    var length;
    var id;
    var name;
    var status;
    var order;

    var noItemsElm;
    var refreshElm;

    // Get variables
    noItemsElm = $(".no-items");
    refreshElm = $(".refresh");

    // Ajax Call
    $.ajax({
        url: '/api/tasks',
        type: 'GET',
        success: function(data) {

            // Set rawData
            rawData = data['data'];
            length = rawData.length;

            if(length > 0) {

                // Loop over raw data
                $.each(rawData, function( key, value ){
                    id = value['identifier'];
                    name = value['name'];
                    status = value['status'];
                    order = value['sequence'];
                    addTask(name, order, status, true, id);
                });

            } else {

                // Show no-items
                noItemsElm.removeClass("hidden");

                // Hide refresh
                refreshElm.addClass("hidden");

            }

        },
        error: function(request,error) {
            // error
        }
    });

}

// Get new order
function getNewOrder() {

    // ...

}

// Generate Task ID
function generateID() {

    // Set variables
    var randLetter;

    // Fill variables
    randLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26));

    // Return
    return randLetter + Date.now();

}

// Mark a task as done
function setToDone(toggle, li) {

    // Set variables
    var id;

    // Fill variables
    id = li.data().id;

        // Ajax call
    $.ajax({
        url: '/api/tasks/toggle/' + id,
        type: 'POST',
        data: { toggle },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(result) {

            // Only set to done when result is valid
            if(result) {

                // Toggle class danger + animation
                li.toggleClass("danger");
                li.toggleClass("animated flipInX");

                // Remove animation
                setTimeout(function() {
                    li.removeClass("animated flipInX");
                }, 500);

            }

        },
        error: function(request,error) {
            // Error
        }
    });

}

// Delete task
function deleteTask(id, todoList, box, noItemsElm, refreshElm) {

    // Ajax call to add to DB
    $.ajax({
        url: '/api/tasks/delete',
        type: 'DELETE',
        data: { identifier: id },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(result) {

            // Only delete if result is valid
            if(result) {

                // Check if last task or not
                if(todoList.find('li').length == 1) {
                    box.removeClass("animated flipInX").addClass("animated bounceOutLeft");
                    setTimeout(function() {
                        box.remove();
                        noItemsElm.removeClass("hidden");
                        refreshElm.addClass("hidden");
                    }, 500);
                } else {
                    box.removeClass("animated flipInX").addClass("animated bounceOutLeft");
                    setTimeout(function() {
                        box.remove();
                    }, 500);
                }

            }

        },
        error: function(request,error) {
            // Error
        }
    });

}

// Toggle task
function toggleTask(li) {

    // Set task on status done
    setToDone(li.hasClass('danger'), li);

}

// Add task
function addTask(text, order, status, initialLoad = false, id = generateID()) {

    // Set variables
    var noItemsElm;
    var addTaskElm;
    var todoListElm;
    var refreshElm;

    var c;
    var item;

    // Fill variables
    noItemsElm = $(".no-items");
    addTaskElm = $(".add-task");
    todoListElm = $(".todo-list");
    refreshElm = $(".refresh");

    // Get status
    c = status === "done" ? "danger" : "";

    // Wait until snippet call is done
    snippet.done(function(html) {

        // Set item
        item = html.replace('__name__', text).replace('__status__', c).replace('__id__', id).replace('__id__', id);

        // Check if text is empty
        if(initialLoad) {

            // Append item to DOM
            todoListElm.append(item);

            // Manipulate DOM
            noItemsElm.addClass("hidden");
            refreshElm.removeClass("hidden");

        } else {

            // Check if text is empty, if it isn't empty -> ajax call
            if(text === "") {
                addTaskElm.addClass("error");
            } else {

                // Ajax call to add to DB
                $.ajax({
                    url: '/api/tasks/add',
                    type: 'POST',
                    data: { identifier: id, name: text, status: c === 'danger' ? 'done' : 'new' },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(result) {

                        // Check if result is true
                        if(result) {

                            // Append item
                            todoListElm.append(item);

                            // Manipulate DOM
                            addTaskElm.removeClass("error");
                            noItemsElm.addClass("hidden");
                            refreshElm.removeClass("hidden");

                            // Reset value
                            addTaskElm.val("");

                        } else {

                            // Add error
                            addTaskElm.addClass("error");

                        }

                    },
                    error: function(request,error) {

                        // Add error
                        addTaskElm.addClass("error");

                    }
                });

            }

        }

        // Set time out, and remove animation
        setTimeout(function() {
            todoListElm.find("li").removeClass("animated flipInX");
        }, 500);

    });

}


// Delete all tasks
function deleteAllTasks(todoList, noItemsElm, refreshElm, maxDuration) {

    // Ajax call
    $.ajax({
        url: '/api/tasks/delete/all',
        type: 'DELETE',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(result) {

            // Only delete if result is valid
            if(result) {

                // Remove all tasks (with animation)
                todoList.find("li").each(function(i) {
                    $(this)
                        .delay(70 * i)
                        .queue(function() {
                            $(this).addClass("animated bounceOutLeft");
                            $(this).dequeue();
                        });
                });

                // Wait 800ms to remove all items and show no items element
                setTimeout(function() {
                    todoList.find("li").remove();
                    noItemsElm.removeClass("hidden");
                    refreshElm.addClass("hidden");
                }, maxDuration);

            }

        },
        error: function(request,error) {
            // Error
        }
    });

}

// Refresh
function refresh() {

    // Set variables
    var todoList;
    var noItemsElm;
    var refreshElm;
    var maxDuration;

    // Fill variables
    todoList = $(".todo-list");
    noItemsElm = $(".no-items");
    refreshElm = $(".refresh");
    maxDuration = todoList.find("li").length * 70 + 200;

    // Delete all tasks
    deleteAllTasks(todoList, noItemsElm, refreshElm, maxDuration);

}

/**
 * Task functionality
 */
(function($) {

    // Set variables
    var err;
    var todoList;
    var noItemsElm;
    var refreshElm;
    var addTaskElm;
    var addTaskVal;
    var isError;

    // Fill variables
    err = $(".err");
    todoList = $(".todo-list");
    noItemsElm = $(".no-items");
    refreshElm = $(".refresh");
    addTaskElm = $(".add-task");
    isError = addTaskElm.hasClass("hidden");

    // Check if not error
    if(!isError) {
        addTaskElm.blur(function() {
            err.addClass("hidden");
        });
    }

    // Handle add-btn click
    $(".add-btn").on("click", function() {
        addTaskVal = addTaskElm.val();
        addTask(addTaskVal, getNewOrder());
        addTaskElm.focus();
    });

    // Refresh
    refreshElm.on("click", refresh);

    // Handle checkbox click (Done toggle)
    todoList.on("click", 'input[type="checkbox"]', function() {

        // Get list item
        var li = $(this).closest('li');

        // Toggle task
        toggleTask(li);

    });

    // Handle checkbox click (Done toggle)
    todoList.on("dblclick", '.task', function() {

        // Get list item
        var li = $(this);

        // Toggle task
        toggleTask(li);

    });

    // Handle close click (Remove task)
    todoList.on("click", ".close", function() {

        // Get box
        var box = $(this).parent().parent();

        // Delete task
        deleteTask(box.data().id, todoList, box, noItemsElm, refreshElm);

    });

    // Handle pressing enter for adding new task
    addTaskElm.keypress(function(e) {

        // Detect enter (key 13) press
        if(e.which == 13) {

            // Get value
            addTaskVal = $(this).val();

            // Add task
            addTask(addTaskVal, getNewOrder());

        }

    });

    // Sort list
    $(todoList).sortable({
        update: function( event, ui ) {

            // Set variables
            var item;
            var identifier;
            var nodes;
            var idx;
            var data;

            // Fill variables
            item = ui.item;
            identifier = item.attr('data-id');
            nodes = Array.from( todoList[0].children );
            idx = nodes.indexOf(item[0]);
            data = $(this).sortable('serialize');

            // Ajax call
            $.ajax({
                url: '/api/tasks/order',
                type: 'POST',
                data: data,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(result) {

                    // Check if result is false
                    if(!result) {

                        // Revert
                        $(this).sortable('cancel');

                    }

                },
                error: function(request,error) {

                    // Revert
                    $(this).sortable('cancel');

                }
            });

        }
    });

    // Disable selection
    todoList.disableSelection();

    // Get tasks from Database
    getTasksFromDatabase();

    /*
    var options = {
        title: '',
        message: 'Taak verwijderd!',
        icon: 'fas fa-times',
        target: "_blank"
    };

    var settings = {
        element: 'body',
        type: 'danger',
        position: 'fixed',
        placement: {
            from: "top",
            align: "left"
        },
        z_index: 10000,
        delay: 999999,
        url_target: '_blank',
        animate: {
            enter: "animated fadeInDown",
            exit: "animated fadeOutUp"
        },
        template: '<div data-notify="container" class="col-xs-11 col-sm-2 alert alert-{0}" role="alert">' +
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
    $.notify(options, settings);
    */

}(jQuery));

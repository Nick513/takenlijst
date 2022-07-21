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

            // Check if length > 0
            if(length > 0) {

                // Loop over raw data
                $.each(rawData, function( key, value ){
                    id = value['identifier'];
                    name = value['name'];
                    status = value['status'];
                    addTask(name, status, true, id);
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
                if(todoList.find('.task').length == 1) {
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
function addTask(text, status, initialLoad = false, id = generateID()) {

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
        item = html.replace(/__name__/gi, text).replace(/__status__/gi, c).replace(/__id__/gi, id);

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

        // Wait 0,5 sec
        setTimeout(function(){

            // Initialize menu
            initializeMenu();

        }, 500);

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

// Initialize menu
function initializeMenu() {

    // Set variables
    var menuElm;

    // Fill variables
    menuElm = $('[data-menu]');

    // jQuery menu
    menuElm.menu();

    // Remove hidden
    menuElm.removeClass("hidden");

}

/**
 * Delete all tasks
 */
(function($) {

    // Set variables
    var refreshElm;

    // Fill variables
    refreshElm = $(".refresh");

    // Refresh
    refreshElm.on("click", refresh);

}(jQuery));

/**
 * Toggle task
 */
(function($) {

    // Set variables
    var todoList;

    // Fill variables
    todoList = $(".todo-list");

    // Handle checkbox click (Done toggle)
    todoList.on("click", 'input[type="checkbox"]', function() {

        // Get list item
        var li = $(this).closest('li');

        // Toggle task
        toggleTask(li);

    });

}(jQuery));

/**
 * Double click task
 */
(function($) {

    // Set variables
    var todoList;

    // Fill variables
    todoList = $(".todo-list");

    // Handle checkbox click (Done toggle)
    todoList.on("dblclick", '.task', function() {

        // Get list item
        var li = $(this);

        // Toggle task
        toggleTask(li);

    });

}(jQuery));

/**
 * Delete task
 */
(function($) {

    // Set variables
    var todoList;
    var noItemsElm;
    var refreshElm;

    // Fill variables
    todoList = $(".todo-list");
    noItemsElm = $(".no-items");
    refreshElm = $(".refresh");

    // Handle close click (Remove task)
    todoList.on("click", "[data-delete]", function() {

        // Get box
        var box = $(this).closest('.task');

        // Delete task
        deleteTask(box.data().id, todoList, box, noItemsElm, refreshElm);

    });

}(jQuery));

/**
 * Add task using add button
 */
(function($) {

    // Set variables
    var addTaskElm;
    var addTaskVal;
    var addButton;

    // Fill variables
    addTaskElm = $(".add-task");
    addButton = $(".add-btn");

    // Handle add-btn click
    addButton.on("click", function() {
        addTaskVal = addTaskElm.val();
        addTask(addTaskVal);
        addTaskElm.focus();
    });

}(jQuery));

/**
 * Add task using enter key
 */
(function($) {

    // Set variables
    var addTaskElm;
    var addTaskVal;

    // Fill variables
    addTaskElm = $(".add-task");

    // Handle pressing enter for adding new task
    addTaskElm.keypress(function(e) {

        // Detect enter (key 13) press
        if(e.which == 13) {

            // Get value
            addTaskVal = $(this).val();

            // Add task
            addTask(addTaskVal);

        }

    });

}(jQuery));

/**
 * Edit task
 */
(function($) {

    // Set variables
    var $this;

    // On click
    $(document).on("click", ".task .edit", function(e){

        // Get $this
        $this = $(e.target);

        console.log($this);

    });

}(jQuery));

/**
 * Disable selection
 */
(function($) {

    // Set variables
    var todoList;

    // Fill variables
    todoList = $(".todo-list");

    // Disable selection
    todoList.disableSelection();

}(jQuery));

/**
 * Sort list
 */
(function($) {

    // Set variables
    var todoList;

    // Fill variables
    todoList = $(".todo-list");

    // Sortable
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
            nodes = Array.from(todoList[0].children);
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

}(jQuery));

/**
 * Load tasks from DB
 */
(function($) {

    // Get tasks from Database
    getTasksFromDatabase();

}(jQuery));

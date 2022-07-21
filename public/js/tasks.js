/**
 * Define global variables
 */
var snippet;
var mloaded;

/**
 * Fill global variables
 */
mloaded = false;

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
    var description;

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
                    description = value['description'];
                    addTask(name, description, status, true, id);
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
    id = li.attr("data-id");

        // Ajax call
    $.ajax({
        url: '/api/tasks/toggle/' + id,
        type: 'POST',
        data: { toggle },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(result) {

            // Only set to done when result is valid
            if(result) {

                // Change value of li
                li.attr("data-status", toggle ? 'new' : 'done');

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
function deleteTask(id, todoList, task, noItemsElm, refreshElm) {

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
                    task.removeClass("animated flipInX").addClass("animated bounceOutLeft");
                    setTimeout(function() {
                        task.remove();
                        noItemsElm.removeClass("hidden");
                        refreshElm.addClass("hidden");
                    }, 500);
                } else {
                    task.removeClass("animated flipInX").addClass("animated bounceOutLeft");
                    setTimeout(function() {
                        task.remove();
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
function addTask(text, description, status, initialLoad = false, id = generateID()) {

    // Set variables
    var noItemsElm;
    var addTaskElm;
    var todoListElm;
    var refreshElm;
    var pplaceholder;
    var maxim;

    var c;
    var item;

    // Fill variables
    noItemsElm = $(".no-items");
    addTaskElm = $(".add-task");
    todoListElm = $(".todo-list");
    refreshElm = $(".refresh");
    pplaceholder = $(".pplaceholder");

    // Get status
    c = status === "done" ? "danger" : "";

    // Wait until snippet call is done
    snippet.done(function(html) {

        // Check if status is done
        if(status === 'done') {

            // Set item
            item = html.replace(/__name__/gi, text).replace(/__status__/gi, c).replace(/__rs__/gi, status).replace(/__id__/gi, id).replace(/__description__/gi, description).replace(/__selected__/gi, 'selected');

        } else {

            // Set item
            item = html.replace(/__name__/gi, text).replace(/__status__/gi, c).replace(/__rs__/gi, status).replace(/__id__/gi, id).replace(/__description__/gi, description).replace(/__selected__/gi, '');

        }

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
                        if(result['result']) {

                            // Set max
                            maxim = result['max']-1;

                            // Empty placeholder
                            if(Number.isSafeInteger(maxim/10)) {
                                pplaceholder.empty();
                            }

                            // Check if amount of tasks is 10
                            if(Number.isSafeInteger(maxim/10) && pplaceholder.find('ul.pagination').length === 0) {

                                // Ajax call
                                $.ajax({
                                    url: '/api/tasks/links',
                                    type: 'GET',
                                    success: function(html) {
                                        pplaceholder.empty();
                                        pplaceholder.append(html);
                                    },
                                    error: function(request,error) {
                                        // Error
                                    }
                                });

                            }

                            // Check if amount of tasks is 10 or higher
                            if(todoListElm.find(".task").length < 10) {

                                // Append item
                                todoListElm.append(item);

                            }

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

    // Set variables
    var pagination;

    // Fill variables
    pagination = $(".todo-pagination");

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
                    pagination.remove();
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

    // Check if menu loaded
    if(!mloaded) {

        // Set variables
        var menuElm;

        // Fill variables
        menuElm = $('[data-menu]');

        // jQuery menu
        menuElm.menu();

        // Remove hidden
        menuElm.removeClass("hidden");

    }

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
    var li;

    // Fill variables
    todoList = $(".todo-list");

    // Handle checkbox click (Done toggle)
    todoList.on("click", '.checkbox-mask', function() {

        // Get list item
        li = $(this).closest('li');

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
    var target;

    // Fill variables
    todoList = $(".todo-list");

    // Handle checkbox click (Done toggle)
    todoList.on("dblclick", '.task', function(e) {

        // Get target
        target = $(e.target);

        // Only toggle task if target has class checkbox
        if(target.hasClass("checkbox") || target.is('label')) {

            // Toggle task
            toggleTask($(this));

        }

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
    var task;

    // Fill variables
    todoList = $(".todo-list");
    noItemsElm = $(".no-items");
    refreshElm = $(".refresh");

    // Handle close click (Remove task)
    todoList.on("click", "[data-delete]", function() {

        // Get task
        task = $(this).closest('.task');

        // Delete task
        deleteTask(task.attr("data-id"), todoList, task, noItemsElm, refreshElm);

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
        addTask(addTaskVal, '');
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
            addTask(addTaskVal, '');

        }

    });

}(jQuery));

/**
 * Edit task
 */
(function($) {

    // Set variables
    var todoList;
    var task;
    var additional;
    var pContainer;

    // Fill variables
    todoList = $(".todo-list");
    pContainer = $(".pcontainer");

    // Handle edit click
    todoList.on("click", "[data-edit]", function() {

        // Get task
        task = $(this).closest('.task');

        // Get additional data
        additional = {
            id: task.attr("data-id"),
            name: task.attr("data-name"),
            description: task.attr("data-description"),
            status: task.attr("data-status"),
        };

        // Ajax call
        $.ajax({
            url: '/modal',
            type: 'GET',
            data: {
                view: 'edittask',
                additional,
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(html) {

                // Append to DOM
                pContainer.append(html);

                // Show popup
                $(".popup[data-id='" + task.attr("data-id") + "']").popup("show");

            },
            error: function(request,error) {
                // Error
            }
        });

    });

}(jQuery));

/**
 * Close popup
 */
(function($) {

    // Set variables
    var closeBtn;
    var $this;
    var pContainer;

    // Fill variables
    closeBtn = $(".closePopup");
    pContainer = $(".pcontainer");

    // On click
    $(document).on("click", closeBtn, function(e) {

        // Get $this
        $this = $(e.target);

        // Check if $this has class closePopup
        if($this.hasClass("closePopup")) {

            // Close popup
            $this.closest('.popup').popup("hide");

            // Empty pContainer
            pContainer.empty();

        }

    });

}(jQuery));

/**
 * Save changes in edit task modal
 */
(function($) {

    // Set variables
    var saveBtn;
    var $this;

    var identifier;
    var name;
    var description;
    var status;
    var pContainer;

    // Fill variables
    saveBtn = $(".savePopup");
    pContainer = $(".pcontainer");

    // On click
    $(document).on("click", saveBtn, function(e) {

        // Get $this
        $this = $(e.target);

        // Check if $this has class closePopup
        if($this.hasClass("savePopup")) {

            // Get data
            identifier = $this.closest('.popup').find('[name="identifier"]').val();
            name = $this.closest('.popup').find('[name="name"]').val();
            description = $this.closest('.popup').find('[name="description"]').val();
            status = $this.closest('.popup').find('[name="status"]').val();

            // Ajax call
            $.ajax({
                url: '/api/tasks/edit/' + identifier,
                type: 'POST',
                data: {
                    name,
                    description,
                    status
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(result) {

                    // Check if result is true
                    if(result) {
                        location.reload();
                    } else {

                        // Close popup
                        $this.closest('.popup').popup("hide");

                        // Empty pContainer
                        pContainer.empty();

                        // Notify
                        notify('', $("[name='task_edit_failed']").val(), 'fas fa-times', 'danger');

                    }

                },
                error: function(request,error) {
                    // Error
                }
            });

        }

    });

}(jQuery));

/**
 * Popup settings
 */
(function($) {

    // Set defaults for popups
    $.fn.popup.defaults.transition = 'all 0.5s';
    $.fn.popup.defaults.escape = true;
    // $.fn.popup.defaults.blur = false;

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
        distance: 10,
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
    // getTasksFromDatabase();

}(jQuery));

/**
 * Initialize menu
 */
(function($) {

    // Initialize menu
    initializeMenu();

}(jQuery));

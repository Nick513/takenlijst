// require('./bootstrap');

/**
 * Define global variables
 */
var state;
var todayContainer;
var date;
var weekday;
var currentday;
var randomWordArray;
var randomWord;
var loadedFromDb;

/**
 * Fill global variables
 */
state = [];
loadedFromDb = false;

/**
 * Today message
 */

// Get today container in DOM
todayContainer = document.querySelector(".today");

// Create new Date instance
date = new Date();

// Fill array with weekdays
weekday = new Array(7);
weekday[0] = $("input[name='monday']").val().toLowerCase() + " 💪😀";
weekday[1] = $("input[name='tuesday']").val().toLowerCase() + " 😜";
weekday[2] = $("input[name='wednesday']").val().toLowerCase() + " 😌☕️";
weekday[3] = $("input[name='thursday']").val().toLowerCase() + " 🤗";
weekday[4] = $("input[name='friday']").val().toLowerCase() + " 🍻";
weekday[5] = $("input[name='saturday']").val().toLowerCase() + " 😴";
weekday[6] = $("input[name='sunday']").val().toLowerCase() + " 🖖";

// Get current day
currentday = weekday[date.getDay()-1];

// Create random word array
randomWordArray = Array(
    $("input[name='rw1']").val(),
    $("input[name='rw2']").val(),
    $("input[name='rw3']").val(),
    $("input[name='rw4']").val(),
    $("input[name='rw5']").val(),
    $("input[name='rw6']").val(),
    $("input[name='rw7']").val(),
    $("input[name='rw8']").val(),
);

// Create random word
randomWord =
    randomWordArray[Math.floor(Math.random() * randomWordArray.length)];

// Add random word + current day to DOM
todayContainer.innerHTML = randomWord + currentday;

/**
 * Functions
 */

// Get state from Database
function getStateFromDb() {

    // Set variables
    var state;
    var rawData;
    var id;
    var name;
    var status;

    // Fill variables
    state = {};

    // Ajax Call
    $.ajax({
        url: '/api/tasks',
        type: 'GET',
        success: function(data) {

            // Set rawData
            rawData = data['data'];

            // Loop over raw data
            $.each(rawData, function( key, value ){
                id = generateID();
                name = value['name'];
                status = value['status'];
                pushToState(name, status, id);
                addTask(name, status, true, true, id);
            });

        },
        error: function(request,error) {
            // error
        }
    });

    // Set loaded from Db
    loadedFromDb = true;

    // Return state
    return state;

}

// Set default state
function setDefaultState() {

    // Set variables
    var baseState;

    // Fill variables
    baseState = {};

    // Sync state
    syncState(baseState);

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

// Push a task to the state
function pushToState(title, status, id) {

    // Set variables
    var baseState;

    // Fill variables
    baseState = getState();

    // Add task to base state
    baseState[id] = { id: id, title: title, status: status };

    // Sync state
    syncState(baseState);

}

// Mark a task as done
function setToDone(id) {

    // Set variables
    var baseState;

    // Fill variables
    baseState = getState();

    // Toggle status
    if(baseState[id].status === 'new') {
        baseState[id].status = 'done'
    } else {
        baseState[id].status = 'new';
    }

    // Sync state
    syncState(baseState);

}

// Delete task
function deleteTask(id) {

    // Set variables
    var baseState;

    // Fill variables
    baseState = getState();

    // Delete task from state
    delete baseState[id];

    // Sync state
    syncState(baseState);

}

// Destroy state
function destroyState() {
    localStorage.removeItem("state");
}

// Reset state
function resetState() {
    localStorage.setItem("state", null);
}

// Sync state
function syncState(state) {
    localStorage.setItem("state", JSON.stringify(state));
}

// Get state
function getState() {

    // Set variables
    var state;

    // Fill variables
    state = JSON.parse(localStorage.getItem("state"));

    // Check if state is not null
    if(!localStorage.getItem("state") && !loadedFromDb) {
        state = getStateFromDb();
    } else if(!state) {
        state = {};
    }

    // Return state
    return state;

}

// Toggle task
function toggleTask(li) {

    // Toggle class danger + animation
    li.toggleClass("danger");
    li.toggleClass("animated flipInX");

    // Set task on status done
    setToDone(li.data().id);

    // Remove animation
    setTimeout(function() {
        li.removeClass("animated flipInX");
    }, 500);

}

// Add task
function addTask(text, status, noUpdate, initialLoad = false, id = generateID()) {

    // Set variables
    var noItemsElm;
    var addTaskElm;
    var todoListElm;
    var refreshElm;

    var c;
    var item;
    var isError;

    // Fill variables
    noItemsElm = $(".no-items");
    addTaskElm = $(".add-task");
    todoListElm = $(".todo-list");
    refreshElm = $(".refresh");

    // Get status
    c = status === "done" ? "danger" : "";

    // Create DOM element
    item =
        '<li data-id="' +
        id +
        '" class="task animated flipInX ' +
        c +
        '"><div class="checkbox"><span class="edit"><i class="fa fa-pencil"></i></span><span class="close"><i class="fa fa-times"></i></span><label><span class="checkbox-mask"></span><input type="checkbox" />' +
        text +
        "</label></div></li>";

    // Check if text is empty
    if(!initialLoad) {
        if(text === "") {
            addTaskElm.addClass("error");
            noUpdate = true;
        } else {
            addTaskElm.removeClass("error");
            todoListElm.append(item);
            noItemsElm.addClass("hidden");
            refreshElm.removeClass("hidden");
        }
    } else {
        todoListElm.append(item);
        noItemsElm.addClass("hidden");
        refreshElm.removeClass("hidden");
    }

    // Reset value
    addTaskElm.val("");

    // Set time out, and remove animation
    setTimeout(function() {
        todoListElm.find("li").removeClass("animated flipInX");
    }, 500);

    // Update if needed
    if(!noUpdate) {
        pushToState(text, "new", id);
    }

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

    // Reset state
    resetState();

}

/**
 * On document ready
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

    var state;
    var stateLength;

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
        addTask(addTaskVal);
        addTaskElm.focus();
    });

    // Refresh
    refreshElm.on("click", refresh);

    // Handle checkbox click (Done toggle)
    todoList.on("click", 'input[type="checkbox"]', function() {

        // Add danger class + animation
        var li = $(this)
            .parent()
            .parent()
            .parent();

        // Toggle task
        toggleTask(li);

    });

    // Handle checkbox click (Done toggle)
    todoList.on("dblclick", '.task', function() {

        // Add danger class + animation
        var li = $(this);

        // Toggle task
        toggleTask(li);

    });

    // Handle close click (Remove task)
    todoList.on("click", ".close", function() {

        // Get box
        var box = $(this)
            .parent()
            .parent();

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

        // Delete task
        deleteTask(box.data().id);

    });

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

    // Sort list
    todoList.sortable({
        update: function( event, ui ) {

            // Reset state
            resetState();

            // Set default state
            setDefaultState();

            // Loop over list items in todoList
            todoList.find("li").each(( i, e ) => {

                // Set variables
                var element;
                var text;
                var id;

                // Fill variables
                element = $(e);
                text = element.text();
                id = element.attr("data-id");

                // Push to state
                pushToState(text, "new", id);

            });

        }
    });

    // Disable selection
    todoList.disableSelection();

    // Get state
    state = getState();

    // Get length of state (amount of tasks)
    stateLength = Object.entries(state).length;

    // If no state is found, get state
    if(stateLength > 0) {

        // Loop over state, and add tasks
        Object.keys(state).forEach(function(todoKey) {
            var todo = state[todoKey];
            addTask(todo.title, todo.status, true, true, todo.id);
        });

    } else {

        // Show no-items
        noItemsElm.removeClass("hidden");

        // Hide refresh
        refreshElm.addClass("hidden");

    }

}(jQuery));

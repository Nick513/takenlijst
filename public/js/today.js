/**
 * Define global variables
 */
var todayContainer;
var date;
var weekday;
var currentday;
var randomWordArray;
var randomWord;

/**
 * Today message
 */

// Get today container in DOM
todayContainer = document.querySelector(".today");

// Create new Date instance
date = new Date();

// Fill array with weekdays
weekday = new Array(7);
weekday[0] = $("input[name='sunday']").val().toLowerCase() + " 🖖";
weekday[1] = $("input[name='monday']").val().toLowerCase() + " 💪😀";
weekday[2] = $("input[name='tuesday']").val().toLowerCase() + " 😜";
weekday[3] = $("input[name='wednesday']").val().toLowerCase() + " 😌☕️";
weekday[4] = $("input[name='thursday']").val().toLowerCase() + " 🤗";
weekday[5] = $("input[name='friday']").val().toLowerCase() + " 🍻";
weekday[6] = $("input[name='saturday']").val().toLowerCase() + " 😴";

// Get current day
currentday = weekday[date.getDay()];

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

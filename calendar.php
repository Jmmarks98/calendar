<?php

$mysqli = new mysqli('localhost', 'jmarks', 'SQL032301', 'calendar');
if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Javascript Calendar</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <!-- JS library for PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
        <script src="js/jsPDF/dist/jspdf.umd.js"></script>
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    </head>
    <body>
        <div id="Login_Form" class="form"> <!-- Take in username and Password -->
            <input type="text" id="username" placeholder="Username" />
            <input type="password" id="password" placeholder="Password" />
            <button id="login_btn">Log In</button>
        </div>

        <div id="Register_Form" class="form"> <!-- Take in new username and Password -->
            <input type="text" id="newUsername" placeholder="New Username" />
            <input type="password" id="newPassword" placeholder="New Password" />
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
            <button id="register_btn">Register</button>
        </div>

        <div id="curUser"></div> <!-- Displays the currently logged in user -->

        <div id="Event_Form" class="form">   <!-- Take in event information for addition to database -->
            <input type="text" id="eventName" placeholder="Event Name" />
            <input type="number" id="eventYear" placeholder="Year" />
            <input type="number" id="eventMonth" placeholder="Month" />
            <input type="number" id="eventDay" placeholder="Day" />
            <input type="text" id="eventTime" placeholder="Time as hh:mm" />
            <input type="text" id="eventTag" placeholder="Tag (red, orange, yellow, green, blue, or purple)" />
            <input type="text" id="share" placeholder="Make public? yes or no"/>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
            <button id="addEvent_btn">Add Event</button>
        </div>

        <div id="Edit_Form" class="form"> <!-- Take in event information for editing event-->
            <input type="number" id="eventId" placeholder="Event ID" />
            <input type="text" id="newName" placeholder="Event Name" />
            <input type="number" id="newYear" placeholder="Year" />
            <input type="number" id="newMonth" placeholder="Month" />
            <input type="number" id="newDay" placeholder="Day" />
            <input type="text" id="newTime" placeholder="Time as hh:mm" />
            <input type="text" id="newTag" placeholder="Tag (red, orange, yellow, green, blue, or violet)" />
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
            <button id="editEvent_btn">Edit Event</button>
        </div>

        <div id="Delete_Form" class="form"> <!-- Take in event id for deletion -->
            <input type="number" id="deleteId" placeholder="Event Id" />
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
            <button id="deleteEvent_btn">Delete Event</button>
        </div>


        <div id="Month_Buttons"> <!-- Allows for displaying subsequent or previous months -->
            <button id="prev_month_btn" class="month_button">Prev</button>
            <button id="next_month_btn" class="month_button">Next</button>
        </div>

        <button id="tag_toggle">Toggle Tags</button> <!-- Enables or disables event highlighting -->
        <div id="editor"></div>
        <button id="cmd" onclick="CreatePDFfromHTML()">Download PDF</button>

        <div id="Calendar">
        <div id="today">

        </div>
        <div id="Calendar_Head"></div> <!-- Displays the current month and year -->
        <div id="Calendar_Body"> <!-- Contains the calendar table itself -->
            <table id="Calendar_Table"> <!-- 6 x 7 table with each row being a week and each column being a day -->
                <tr>
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
                <tr>
                    <td id="d00"></td>
                    <td id="d01"></td>
                    <td id="d02"></td>
                    <td id="d03"></td>
                    <td id="d04"></td>
                    <td id="d05"></td>
                    <td id="d06"></td>
                    </tr>
                <tr>
                    <td id="d10"></td>
                    <td id="d11"></td>
                    <td id="d12"></td>
                    <td id="d13"></td>
                    <td id="d14"></td>
                    <td id="d15"></td>
                    <td id="d16"></td>
                </tr>
                <tr>
                    <td id="d20"></td>
                    <td id="d21"></td>
                    <td id="d22"></td>
                    <td id="d23"></td>
                    <td id="d24"></td>
                    <td id="d25"></td>
                    <td id="d26"></td>
                </tr>
                <tr>
                    <td id="d30"></td>
                    <td id="d31"></td>
                    <td id="d32"></td>
                    <td id="d33"></td>
                    <td id="d34"></td>
                    <td id="d35"></td>
                    <td id="d36"></td>
                </tr>
                <tr>
                    <td id="d40"></td>
                    <td id="d41"></td>
                    <td id="d42"></td>
                    <td id="d43"></td>
                    <td id="d44"></td>
                    <td id="d45"></td>
                    <td id="d46"></td>
                </tr>
                <tr>
                    <td id="d50"></td>
                    <td id="d51"></td>
                    <td id="d52"></td>
                    <td id="d53"></td>
                    <td id="d54"></td>
                    <td id="d55"></td>
                    <td id="d56"></td>
                </tr>
            </table>
        </div>
        </div>
        <script>
            // Wiki Calendar Functions
            (function(){Date.prototype.deltaDays=function(c){return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)};Date.prototype.getSunday=function(){return this.deltaDays(-1*this.getDay())}})();
            function Week(c){this.sunday=c.getSunday();this.nextWeek=function(){return new Week(this.sunday.deltaDays(7))};this.prevWeek=function(){return new Week(this.sunday.deltaDays(-7))};this.contains=function(b){return this.sunday.valueOf()===b.getSunday().valueOf()};this.getDates=function(){for(var b=[],a=0;7>a;a++)b.push(this.sunday.deltaDays(a));return b}}
            function Month(c,b){this.year=c;this.month=b;this.nextMonth=function(){return new Month(c+Math.floor((b+1)/12),(b+1)%12)};this.prevMonth=function(){return new Month(c+Math.floor((b-1)/12),(b+11)%12)};this.getDateObject=function(a){return new Date(this.year,this.month,a)};this.getWeeks=function(){var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);return c}};

            var username = "" // Initialize username variable

            var events = [] // Initialize user events
            var showTags = true; // Initialize tag display state

            function loginAjax(event) { // Ajax call to login user
                username = document.getElementById("username").value; // Get the username from the form


                const password = document.getElementById("password").value; // Get the password from the form

                // Make a URL-encoded string for passing POST data:
                const data = { 'username': username, 'password': password };

                fetch("login_ajax.php", { // Checks database for username/password
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'content-type': 'application/json' }
                    })
                .then(response => response.json())
                .then(data =>{
                if(data.success)
                {
                        document.getElementById("curUser").textContent = "Logged in as " + username; // Displays currently logged in user
                currentMonth = new Month(2022, 9); // Returns the current month to default
                    getEventsAjax(); // Updates the calendar with the default month's events


                }
                else
                {
                        document.getElementById("curUser").textContent = "Incorrect Credentials";
                }

                } ) // Retrieves user events from getEventsAjax
                .catch(err => console.error(err));
            }

            function registerAjax(event){ // Ajax call to register new user
                username = document.getElementById("newUsername").value; // Get the username from the form
                const password = document.getElementById("newPassword").value; // Get the password from the form

                // Make a URL-encoded string for passing POST data:
                const data = { 'username': username, 'password': password };
                fetch("register_ajax.php", { // Adds new user to database
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'content-type': 'application/json' }
                    })
                .then(response => response.json())
                .then(data => console.log(data.success ? "You've been registered!" : `You were not registered ${data.message}`))
                .catch(err => console.error(err));
            }
            function addEventAjax(event){ // Ajax call to create new event
                const name = document.getElementById("eventName").value; // Get values from the form
                const year = document.getElementById("eventYear").value;
                const month = document.getElementById("eventMonth").value;
                const day = document.getElementById("eventDay").value;
                const time = document.getElementById("eventTime").value;
                const tag = document.getElementById("eventTag").value;
                const share= document.getElementById("share").value;
                const data = { 'name': name, 'year': year, 'month': month, 'day': day, 'time': time, 'username': username, 'tag': tag,'share':share};
                fetch("eventAdd_ajax.php", { // Adds event to database
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'content-type': 'application/json' }
                    })
                .then(response => response.json())
                .then(data => getEventsAjax()) // Call getEventsAjax to update the page with new event in database

                .catch(err => console.error(err));
            }
            function editEventAjax(event){ // Ajax call to edit event
                const id = document.getElementById("eventId").value; // Get values from the form
                const name = document.getElementById("newName").value;
                const year = document.getElementById("newYear").value;
                const month = document.getElementById("newMonth").value;
                const day = document.getElementById("newDay").value;
                const time = document.getElementById("newTime").value;
                const tag = document.getElementById("newTag").value;
                const data = { 'id': id, 'name': name, 'year': year, 'month': month, 'day': day, 'time': time, 'username': username, 'tag': tag};
                fetch("eventEdit_ajax.php", { // Updates event in database
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'content-type': 'application/json' }
                    })
                .then(response => response.json())
                .then(data => getEventsAjax()) // Calls getEventsAjax to update the page with the edited event in database

                .catch(err => console.error(err));
            }
            function deleteEventAjax(event){ // Ajax call to delete event
                const id = String(document.getElementById("deleteId").value); // Get value from form
                const data = { 'id': id,'username': username};
                fetch("eventDelete_ajax.php", { // Removes event from database
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'content-type': 'application/json' }
                    })
                .then(response => response.json())
                .then(data => getEventsAjax()) // Calls getEventsAjax to update the page without the deleted event

                .catch(err => console.error(err));
            }
            document.getElementById("login_btn").addEventListener("click", loginAjax, false); // Event listeners for the above function buttons, each calling their respective function
            document.getElementById("register_btn").addEventListener("click", registerAjax, false);
            document.getElementById("addEvent_btn").addEventListener("click", addEventAjax, false);
            document.getElementById("editEvent_btn").addEventListener("click", editEventAjax, false);
            document.getElementById("deleteEvent_btn").addEventListener("click", deleteEventAjax, false);
            document.getElementById("tag_toggle").addEventListener("click", function(event){
                if (showTags){
                    showTags = false;
                }
                else{
                    showTags = true;
                }
                console.log("Tags Toggled")
                getEventsAjax();
            }, false);
            var currentMonth = new Month(2022, 9); // Default month is October 2022
            getEventsAjax(); // Update the page on refresh, will result in no events being displayed if no one is logged in

            // Change the month when the ">" button is pressed
            document.getElementById("next_month_btn").addEventListener("click", function(event){
                    currentMonth = currentMonth.nextMonth(); // Increments the current month
                    getEventsAjax(); // Updates the calendar with the next month's events
            }, false);

            // Change the month when the "<" button is pressed
            document.getElementById("prev_month_btn").addEventListener("click", function(event){
                    currentMonth = currentMonth.prevMonth(); // Decrements the current month
                    getEventsAjax(); // Updates the calendar with the previous month's events
            }, false);

            function getEventsAjax(){ // Ajax call to retrieve all events belonging to user
                    const data = {'username': username};

                fetch("getEvents_ajax.php", { // Selects events belonging to user in database sorted by time
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'content-type': 'application/json' }
                    })
                .then(response => response.json())
                .then(data => updateCalendar(data)) // Updates the calendar with the retrieved data
                .catch(err => console.error(err));
            }

            function updateCalendar(events){ // Updates the calendar
                var monthDict = {
                    0: "January",
                    1: "February",
                    2: "March",
                    3: "April",
                    4: "May",
                    5: "June",
                    6: "July",
                    7: "August",
                    8: "September",
                    9: "October",
                    10: "November",
                    11: "December"
            }
                var to = new Date();
                events = events.success; // Gets data from javascript object
                document.getElementById("Calendar_Head").textContent = monthDict[currentMonth.month]+" "+currentMonth.year; // Display current month and year

                    var weeks = currentMonth.getWeeks();
                var i = 0; // Week index
                    for(var w in weeks){ // Iterate over weeks
                            var days = weeks[w].getDates();
                            // days contains normal JavaScript Date objects.
                    var j = 0; // Day index
                            for(var d in days){
                        document.getElementById('d'+String(i)+String(j)).textContent = days[d].getDate() // Displays the correct date in the appropriate table cell
                        var dateEvents = []; // Initialize list of events on the specific date
                        for (var k in events){
                            if(days[d].getMonth() == currentMonth.month && events[k][2] == currentMonth.year && events[k][3]-1 == currentMonth.month && events[k][4] == days[d].getDate()){ // Checks if event occurs on specific date
                                dateEvents.push(events[k])
                            }
                        }


                        var date = document.getElementById('d'+String(i)+String(j)) // Get appropriate table cell
                        var dateList = document.createElement("ul"); // Create HTML unordered list
                        dateList.style.listStyle = 'none'; // No bullets for list
                        for (var k in dateEvents) { // Create a new line item for event in dateEvents
                            var li = document.createElement("li"); // Creates new line item
                            if (dateEvents[k][6] != 'none' && showTags){ // Highlights event respective tag color if event has a tag and showTags is toggled on
                                console.log("Tagging Event: " + dateEvents[k][6])
                                li.style.color = dateEvents[k][6];
                            }
                            li.appendChild(document.createTextNode(dateEvents[k][0]+" "+dateEvents[k][1]+": "+dateEvents[k][5])); // Writes event information to the line item
                            dateList.appendChild(li); // Adds line item to list
                        }
                        date.appendChild(dateList); // Adds list to calendar
                        j = j + 1;
                    }
                    i = i+1;
                    }
            }

            //Download calendar page as PDF
            function CreatePDFfromHTML() {
                window.jsPDF = window.jspdf.jsPDF;
                var doc = new jsPDF();

                // Source string containing HTML.
                var elementHTML = document.querySelector("#Calendar");

                doc.html(elementHTML, {
                    callback: function(doc) {
                        // Save the PDF
                        doc.save(username + '-calendar.pdf'); // File saved as username-calendar
                        },
                    orientation:'l',
                    x: 15,
                    y: 15,
                    width: 220, //target width in the PDF document
                    windowWidth: 1800 //window width in CSS pixels
                });
            }
        </script>
    </body>
</html>

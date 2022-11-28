# CSE330
Jacob Marks
465106
Jmmarks98

Soham Upadeo
513532
Soham13U

Calendar Page: http://ec2-3-145-218-114.us-east-2.compute.amazonaws.com/~jmarks/module5/calendar.php

Phpmyadmin: http://ec2-3-145-218-114.us-east-2.compute.amazonaws.com/phpmyadmin/index.php
Username: jmarks Password: SQL032301

Creative Portion: Event tagging feature, tags can be toggled on or off. Events can be made public and visible on all users calendars. Can download current month calendar as pdf.

# Grade

## 67/75

> 0 / 3 – CSRF tokens are passed when editing or removing events

CSRF Token never passed.

> 0 / 3 – Session cookie is HTTP-Only

Didn't set `ini_set("session.cookie_httponly", 1);`

> 2 / 4 – Site is intuitive to use and navigate
>
> Take off 2 points if the UI thinks they're logged out on page refresh

User logged out on page refresh.


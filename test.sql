-- List out the event category preference of each user
SELECT email, categories.name, count(*) as count
FROM booking INNER JOIN events 
ON booking.eventID = events.id
INNER JOIN categories
ON events.category = categories.id
INNER JOIN users
ON booking.userid = users.id
GROUP BY email, categories.name;

-- List out the number of users booking in each category of events
SELECT categories.name, count(*) 
from booking INNER JOIN events
on booking.eventID = events.id
INNER JOIN categories
ON events.category = categories.id
GROUP BY categories.name;


(SELECT categories.name, count(*) as absent
from absent INNER JOIN events
on absent.eventID = events.id
INNER JOIN categories
ON events.category = categories.id
GROUP BY categories.name);

-- Attendance Stat by cat
select booking as expectedAttendance , (booking - absence) as realAttendance , A.category from
(select count(*) as booking , events.category
from booking INNER JOIN events
on booking.eventID = events.id
group by events.category) A
INNER JOIN
(select count(*) as absence, events.category
from absent INNER JOIN events
on absent.eventID = events.id
GROUP BY events.category) B
ON A.category = B.category;

-- Attendance Stat by month
SELECT booking as expectedAttendance , (booking - absence) as realAttendance , A.month from
(select count(*) as booking , MONTHNAME(time) as month
from booking INNER JOIN events
on booking.eventID = events.id
WHERE events.time BETWEEN '2017-09-01' AND '2018-04-30'
group by MONTHNAME(time)
ORDER BY time) A
INNER JOIN
(select count(*) as absence, MONTHNAME(time) as month
from absent INNER JOIN events
on absent.eventID = events.id
WHERE events.time BETWEEN '2017-09-01' AND '2018-04-30'
GROUP BY MONTHNAME(time)
ORDER BY time) B
ON A.month = B.month;

-- Attendance Stat by student type
SELECT count(*) as booking, categories.name FROM
booking INNER JOIN events
ON booking.eventID = events.id
INNER JOIN users
ON booking.userID = users.id
INNER JOIN categories
ON events.category = categories.id
WHERE role = 2
GROUP BY categories.name;

-- Profit Stat by category
SELECT SUM(total), categories.name, concat('#',SUBSTRING((lpad(hex(@curRow := @curRow + 10),6,0)),-6)) AS color 
FROM buy INNER JOIN events
ON buy.eventID = events.id
INNER JOIN categories
ON categories.id = events.category
INNER JOIN (SELECT @curRow := 5426175) color_start_point
GROUP BY categories.name;

-- Profit Stat by time
SELECT SUM(total), date(time) as time
FROM buy INNER JOIN events
ON buy.eventID = events.id
WHERE time BETWEEN '2017-09-01' AND '2018-04-30'
GROUP BY time
ORDER BY time ASC;


-- Emails of whom interested in particular event category
SELECT email, categoryID FROM subscribe INNER JOIN users
ON subscribe.userID = users.id
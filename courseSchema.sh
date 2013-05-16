#!/bin/bash

ELASTICSEARCH_URL = "localhost:9200"
MYSQL_DATABASE = "192.168.1.1:3306/courseCritique"
MYSQL_USERNAME = "*****"
MYSQL_PASSWORD = "*****"
MYSQL_DATABASE_NAME = "Data"
curl -XPUT "$ELASTICSEARCH_URL/_river/class/_meta" -d '{
    "type" : "jdbc",
    "jdbc" : {
        "driver" : "com.mysql.jdbc.Driver",
        "url" : "jdbc:mysql://$MYSQL_DATABASE",
        "user" : "$MYSQL_USERNAME",
        "password" : "$MYSQL_PASSWORD",
        "sql" : "SELECT DISTINCT \"class\" as \"_index\", REPLACE(Course,\" \",\"\") as \"_id\", Course as \"course\", ROUND(AVG(GPA),2) as \"grades.gpa\", ROUND(AVG(A)) as \"grades.a\", ROUND(AVG(B)) as \"grades.b\", ROUND(AVG(C)) as \"grades.c\", ROUND(AVG(D)) as \"grades.d\", ROUND(AVG(F)) as \"grades.f\", ROUND(AVG(W)) as \"grades.w\"  FROM $MYSQL_DATABASE_NAME GROUP BY Course"
   }
}'

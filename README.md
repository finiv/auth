Laravel Benchmarking Test
Client Account Self Registration

OVERVIEW
Objective of this proposal to build an api using laravel+mysql (details below) to allow clients to self register accounts to gain login access on xyz applications.
GOALS
Goal is to build two API endpoints, 
1. To be able to self register accounts using google SSO and 
2. Login using google SSO + Login form 3. Only authenticated users should be able to to access GET /home endpoint, which should list current weather data of user’s location on JSON format
POST /login endpoint
Build simple UI for login + registration page: https://i.imgur.com/kGE5vEJ.png
Ensure to apply proper validations on all fields of POST /login endpoint.

GET /home endpoint
Sample response body: https://i.imgur.com/SyONqNG.png
this endpoint can ONLY be access by authenticated users
Weather data should be of the authenticated user’s location.
Weather data should be cached and retrieved from REDIS
For this demo, you can register yourself for a free account on openweathermap API.  openweathermap API documentation can be found here
SPECIFICATIONS
Candidates are expected to build both API endpoints using laravel (ver 8) repository design pattern and mysql as DB server. The REDIS database should be used to cache weather data fetched by openweathermap APIs. Please write unit test cases for both endpoints. This is a TEST Task. DO NOT seek outside guidance/help to complete this task. Submit your code via Github only.
**Optionally the candidate can use docker-container to containerize application.

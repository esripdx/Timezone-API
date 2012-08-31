
Example
-------

    curl "http://api.example.com/timezone?latitude=45.5118&longitude=-122.6433"

or, if you don't like query string parameters:

    curl http://api.example.com/timezone/45.5118/-122.6433

Example response:

    {
      timezone: "America/Los_Angeles",
      offset: "-07:00",
      seconds: -25200
    }

Setup
-----

You will first need to create a PostGIS-enabled database.

Set up your database config in `config.php`, then run `import.php` which will create
the timezone table and import all the data from the tz_cities.txt file.

After configuring Nginx or another web server, you will be able to make requests 
like the example above.


Nginx Config
------------

    server {
        listen 80;
        access_log /var/log/nginx/timezone.log main;
        error_log /var/log/nginx/timezone.log notice;

        root /web/Timezone-API;

        location / {
            try_files $uri /index.php?$query_string;
        }

        location ~ \.php {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_split_path_info ^(.+\.php)(.*)$;
            include fastcgi_params;
            fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
    }

License
-------

This code is available under the BSD license. See LICENSE for full details.

Data compiled from the GeoNames.org `cities15000.zip` file available here:
http://download.geonames.org/export/dump/ under a Creative Commons Attribution 3.0 License.

The Data is provided "as is" without warranty or any representation of accuracy, timeliness or completeness.



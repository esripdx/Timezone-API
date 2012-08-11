
Example
-------

    curl -u geoloqi:api "http://timezone-api.geoloqi.com/timezone?latitude=45.5118&longitude=-122.6433"

or, if you don't like query string parameters:

    curl -u geoloqi:api http://timezone-api.geoloqi.com/timezone/45.5118/-122.6433

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


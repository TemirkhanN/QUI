<h1>Configurations</h1>


<h2>Main</h2>
Basic application configs are defined in "config/main.php" as array that will be required by app core.<br>
Config includes information about database connection and routes. For convenience<br>
these are being kept separate at "config/database.php" and "config/routes.php".<br>
<br>
<pre>
Configuration array contains following keys
    <code class="php">
        //indicates if it is development or production mode.
        'debugMode' => false,

        //information about local language and timezone.
        //It does not affect anything for now.
        'local' => [
            'lang' => 'ru_RU',
            'timezone' => 'Europe/Moscow'
        ],

        //information about cache status and cache expiration time
        'cache' => [
            'active' => true,
            'cacheTime' => 360,
        ],
    </code>
</pre>

<h2>Database</h2>
<pre>
    Database configuration file contains array of items with following keys
    <code class="php">
        //default database source name(mysql,pgsql, mssql and etc.)
        'default' => 'mysql',

        //connection information
        'mysql' => [
            'host' => 'localhost',
            'name' => 'beauty',
            'user' => 'root',
            'password' => ''
        ],
    </code>
</pre>

<h2>Routes</h2>
<pre>
    Routing system is described in <a href="/wiki/router/">Routing system</a>
</pre>
<h1>Router</h1>

<h2>Basic route set</h2>
<div class="well">
    Base routes in QUI are defined in config/routes.<br>
    To keep your application logic obvious it's recommended to keep them all there.<br>
    All routes lead to controller's method with prefix "page"
    Route has following parameters:<br>
    <br>
    <pre>
        <code class="html">
            /**
            * @param string route - pattern that will be searched in url for match.
            * It has RegEx format.
            * @param string action - controller name and its method divided with "/"
            * @param bool full - if query vars shall be included in pattern match checking
            * @param array params - names of items in identical order as they set
            * in route regex
            */
        </code>
    </pre>
</div>

<pre>
    Route has following pattern:

    <code class="php">
        ['route' => '^/?$', 'action' => 'main/index']
    </code>
    The array keys are necessary. The route value has RegEx format.
    It means that default page will lead to controller "MainController" and run its method "pageIndex".
</pre>


<div class="alert alert-info" role="alert">
    <strong>tip:</strong> pageIndex is default method that will run if no method defined.
</div>



<pre>
    Previous example is similar to
    <code class="php">
        ['route' => '^/?$', 'action' => 'main']
    </code>
</pre>


<br>
<br>

<h2>Dynamical route set</h2>
<pre>
    If you want pass method name to controller dynamically you may use following composition:
    <code class="php">
        ['route' => '^/([a-z]+)/$', 'action' => 'main/{1}']
    </code>
    It will call MainController's methods dynamically based on passed reference at regular expression.
    The reference in brackets is digital offset of match.
    For example:
    <code class="php">
        ['route' => '^/(xml|xls)/method-([a-z]+)$', 'action' => 'main/{2}']
    </code>
</pre>

<div class="alert alert-warning" role="alert">
    <strong>important:</strong> placeholder between brackets shall be only digital
</div>

<pre>
    You may define which params from url shall be parsed bypassing "params" key with array of items in identical order as they
    set in route regex
    <code class="php">
        ['route' => '^/(.+?)\.html/$', 'params'=>['short_link'], 'action' => 'main/content']
    </code>
    And access it from global
    <code class="php">
        App::$app->routedParam('short_link');
    </code>
</pre>
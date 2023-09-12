<style>
    .result:empty {
        display: none;
    }

    code{
        background-color: #eee;
        padding: 2px;
        border-radius: 2px;
    }
</style>
<h1 class="ui header">Welcome!</h1>

<p>This is the welcome controller's default view file. It is located at <code>/views/welcome/welcome_index.php</code>.</p>

<h2 class="ui header">Examples</h2>
<p>Below are some examples how to use Halo</p>

<h3 class="ui header">Adding pages</h3>
<p>For example, to have the URL localhost/halo/<span class="ui label blue">posts/view/3</span> working, visit
    <a class="ui link" href="halo">Halo admin</a> and create a subpage there, or do it manually:</p>

<ol class="ui list">
    <li>Create new file <code>/controllers/<i>posts</i>.php</code>
    </li>
    <li>In that file create <code>class posts</code> (lower case letters) which <code>extends Controller</code> (capitalized)</li>
    <li>Create <code>function index()</code> within that class. This is the default action which will be called when no action is specified (e.g. just /posts). There you can set all the variables your view will need.</li>
    <li>Create <code>function view()</code> within that class.
        This is the <i>action</i> that gets run when users access <code>posts/view...</code>.
        Here you usually make a database query and put its result into a variable that is preceded with
        <code>$this</code>
        (so that you can later access it from the <i>view</i>).
        To access what is put after the action name on the URL (<code>3</code> in our example), use <code>$this->params[0]</code>.
        An example: <code>$this->post = Db::getOne("SELECT * FROM post WHERE id={$this->params[0]}");</code> (You would
        have to create the <i>post</i> table in your database and add at least <i>id</i> field to it, of course)
    </li>
    <li>Create new folder <code>/views/posts</code></li>
    <li>Create new file <code>/views/posts/posts_view</code></li>
    <li>Place content to that file. You could <code>&lt;?php var_dump($post)?></code> for starters.</li>
</ol>

<h3 class="ui header">Sending data to server</h3>
<h4 class="ui header">Ajax example</h4>
<p>Fill the name field below and click <i>submit form using ajax</i>.</p>
<form id="ajax-form">
    Your name: <input class="ui input" type="text" placeholder="Write something here" name="name"/>
    <a class="ui button green" onclick="success()">Submit form using ajax (success)</a>
    <a class="ui button red" onclick="error()">Submit form using ajax (error)</a>
</form>

<p>The form containing the name field will be submitted to the server by jQuery and server's response will be written to the box below.</p>

<div class="ui warning message result"></div>

<!-- Code for ajax -->

<script type="text/javascript">
    function success() {
        ajax("welcome/success", $("#ajax-form").serialize(), function (json) {
            $(".result").html(json.data);
        });
    }

    function error() {
        ajax("welcome/error", $("#ajax-form").serialize(), function (json) {
            $(".result").html(json.data);
        });
    }
</script>

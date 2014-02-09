<h1>Welcome!</h1>
<!-- Code for ajax -->
<script type="text/javascript">
    function clickme() {
        $.post("welcome", $( "#ajax-form" ).serialize(), function (data) {
            $(".result").html(data);
        });
    }
</script>


<p>This is the welcome controller's default view file. It is located at <code>/views/welcome/welcome_index.php</code>.</p>

<h2>Examples</h2>
<p>Below are some examples how to use Halo</p>

<h3>Adding pages</h3>
<p>For example, to To have the URL localhost/halo/<span class="label label-primary">posts/view/3</span> working:</p>
<ol>
	<li>Create new file <code>/controllers/<i>posts</i>.php</code></li>
	<li>In that file create <code>class posts</code> (lower case letters) which <code>extends Controller</code> (upper case letters)</li>
	<li>Create <code>function index()</code> within that class. This is the default action which will be called when no action is specified (e.g. just /posts). There you can set all the variables your view will need.</li>
	<li>Create <code>function view()</code> within that class.
		This is the <i>action</i> that gets run when users access <code>posts/view...</code>.
		Here you usually make a database query and put its result into a variable that is preceded with <code>$this</code>
		(so that you can later access it from the <i>view</i>).
		To access what is put after the action name on the URL (<code>3</code> in our example), use <code>$this->params[0]</code>.
		An example: <code>$this->post = get_one("SELECT * FROM post WHERE id={$this->params[0]}");</code> (You would have to create the <i>post</i> table in your database and add at least <i>id</i> field to it, of course)</li>
	<li>Create new folder <code>/views/posts</code></li>
	<li>Create new file <code>/views/posts/posts_view</code></li>
	<li>Place content to that file. You could <code>&lt;?var_dump($post)?></code> for starters.</li>
</ol>

<h3>Sending data to server</h3>
<h4>jQuery $.post (Ajax) submit example</h4>
Fill the name field below and click <i>submit form using ajax</i>.
<form id="ajax-form">
Your name: <input type="text" placeholder="Write something here" name="name"/><br/>
</form>
<a href="#" onclick="clickme()">Submit form using ajax</a><br/>

The form containing the name field will be submitted to the
server by jQuery and server's response will be written to the box below.

<div class="well result"></div>



<h4>Traditional POST submit example</h4>
<p>Here is an example how to use traditional POST to send data to the server. Click Post after filling the form. The server will invoke <code>post::index_post()</code> action (which is in <code>/controllers/posts.php</code> file) which just dumps $_POST to the screen.</p>
<!-- Button for executing post -->
<form method="post">
    <input type="text" name="foobar"/>
    <input type="submit" value="Post"/>
</form>


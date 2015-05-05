<?php
 session_start ();
   if ( array_key_exists('menu',$_GET) )
   {
     $_SESSION["menu"] = json_decode($_GET['menu']);
   } else {
      $_SESSION["menu"] = array("name"=>"root"); 
   }    
  ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Recursive JSON Editor Example</title>

    <!-- Foundation CSS framework (Bootstrap and jQueryUI also supported) -->
    <link rel='stylesheet' href='//cdn.jsdelivr.net/bootstrap/3.2.0/css/bootstrap.css'>
    <!-- Font Awesome icons (Bootstrap, Foundation, and jQueryUI also supported) -->
    <link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css'>

    <link rel='stylesheet' href='//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css'>
    <link rel='stylesheet' href='//cdn.jsdelivr.net/sceditor/1.4.3/themes/modern.min.css'>
    <script src='//cdn.jsdelivr.net/jquery/2.1.1/jquery.min.js'></script>
    <script src='//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.min.js'></script>
    <script src='//cdn.jsdelivr.net/sceditor/1.4.3/plugins/xhtml.js'></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    <script src="jsoneditor.js"></script>
</head>
<body>

<style>

.dropdown-submenu {
    position: relative;
}

.dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -6px;
    margin-left: -1px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover>.dropdown-menu {
    display: block;
}

.dropdown-submenu>a:after {
    display: block;
    content: " ";
    float: right;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-left-color: #ccc;
    margin-top: 5px;
    margin-right: -10px;
}

.dropdown-submenu:hover>a:after {
    border-left-color: #fff;
}

.dropdown-submenu.pull-left {
    float: none;
}

.dropdown-submenu.pull-left>.dropdown-menu {
    left: -100%;
    margin-left: 10px;
    -webkit-border-radius: 6px 0 6px 6px;
    -moz-border-radius: 6px 0 6px 6px;
    border-radius: 6px 0 6px 6px;
}
</style>
<div class='container'>
       <nav role="navigation" class="navbar navbar-default">         
			 <div class="navbar-header">
            <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" class="navbar-brand">Edit Menu</a>
        </div>
		        <div id="navbarCollapse" class="collapse navbar-collapse">
<UL class="nav navbar-nav">
	<?php
	
	   function recursive_menu( $obj ,$deep) 
	   {
	     if ( is_object($obj) ) {
		    if ( property_exists ($obj,"children") && is_array($obj->children) && count($obj->children)>0) {

			if ($deep > 0) 
			{			
			echo '<li class="dropdown-submenu">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$obj->name.'</a> <ul class="dropdown-menu  multi-level" >';	
			}
			else 
			{
				echo '<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$obj->name.'<b class="caret"></b></a> <ul class="dropdown-menu" >';
			}
			
			foreach( $obj->children as $m ) 
				{	 
					recursive_menu($m , $deep +1);  
				}
				
            echo "</ul>\n </li> \n";
			  }
			else {
			   if ( property_exists ($obj,"isdivider") && $obj->isdivider == "true") 
			   {
			     echo  "<li class='divider'></li>";
			   }
			   else
			   {
		         echo "<li><a href='#'>".$obj->name."</a></li>\n";
			  }
			}
         } 		 
	   }
	   
	   if ( array_key_exists("menu", $_SESSION) ) 
	  {
		 $menu = $_SESSION["menu"];
		//  print_r(get_object_vars($menu));	
	      
		if ( is_object($menu) && property_exists ($menu,"children") ) 
		{
			foreach( $menu->children as $m )
			{
				recursive_menu($m,0);
			}
		}
	  }
	?>
	</UL>
	        </div>
    </nav>
    <div class='row' style='padding-bottom: 15px;'>
        <div class='col-md-12'>
            <button id='submit' class='btn btn-info'>Submit</button>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
            <div id='editor_holder'></div>
        </div>
    </div>
</div>

<script>
    JSONEditor.defaults.theme = 'bootstrap3';
    JSONEditor.defaults.iconlib = 'fontawesome4';
  //  JSONEditor.plugins.sceditor.style = "//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css";

	JSONEditor.defaults.options.object_layout = 'grid';

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder'),{
        disable_edit_json:true,
	     //disable_properties:true,
		// The schema for the editor
        schema: {
            title: "Menu",
			format: "grid",
            $ref: "#/definitions/menu",
            definitions: {
                menu: {
                    type: "object",
                    id: "menu",
					    "headerTemplate": "{{ self.name }}",
                    // The object will start with only these properties
                    defaultProperties: [
                        "name",
						//"children"
                    ],
                    properties: {                        
                        name: {
                            title: "Name",
                            type: "string"
                        },
						isdivider: {
						    title: "divider",
						   type: "string",
						       "enum": [false,true]

						  },
                        children: {
                          type: "array",
                          // Self-referential schema in array items
                          items: {
                            title: "Children",
                            $ref: "#/definitions/menu" 
                          } 
                        }
                    }
                }
            }
        }
    });
	
	

    // Hook up the submit button to log to the console
    document.getElementById('submit').addEventListener('click',function() {
        // Get the value from the editor
		window.location.href = "menu.php?menu="+JSON.stringify(editor.getValue()); 
        //sconsole.log(editor.getValue());
    });

    // Hook up the validation indicator to update its
    // status whenever the editor changes
    editor.on('change',function() {
        // Get an array of errors from the validator
        var errors = editor.validate();
        console.log(errors);
    });

<?php if ( is_object($menu) ) {  ?>
	 editor.setValue(JSON.parse('<?php  echo json_encode($menu);?>'));
<?php  } ?>
</script>
</body>
</html>

<?php

$VALIDATIONS = Array(
  "firstname" => Array("regexp" => '.+', "error" => "Enter a name"),
  "lastname" => Array("regexp" => '.+', "error" => "Enter a name"),
  "heads" => Array("regexp" => '^\d+$', "error" => "Number of heads should be a whole number"),
    "dob" => Array("regexp" => '^\d\d[\/.-]\d\d[\/.-]\d\d\d\d$', "error" => "Enter dates in  format DD/MM/YYYY"),
  "email" => Array("regexp" => '^.+@.+\..+$', "error" => "This address is not valid")
);
$ERRORS = Array();

if (isset($_GET["submit"])) {
  # form was submitted
  foreach ($VALIDATIONS as $field => $data) {
    if (!isset($_GET[$field])) continue; # skip any that aren't sent
    $regexpstr = $data["regexp"];
    if (preg_match("/$regexpstr/", $_GET[$field]) == 0) {
      $ERRORS[$field] = $data["error"];
    } 
  }
  if (count($ERRORS) == 0) echo "Data OK; now redirect!";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>A simple PHP form using regular expressions for validation</title>
<link rel="stylesheet" href="styles.css">
<script type="text/javascript">

validator = {
  VALIDATIONS: {
<?php
    foreach ($VALIDATIONS as $field => $data) {
      $regexpstr = $data["regexp"];
      $errorstr = $data["error"];
      echo "\"$field\": { 'regexp': /$regexpstr/, 'error': '$errorstr' },\n";
    }
?>
  },
  
  init: function() {
    // check this browser has the chops to do the DOM scripting we need
    if (!document.getElementById) return;
    
    // Walk through the VALIDATIONS list and for each one find the field
    //  it applies to and attach an onBlur handler, so when the user leaves
    //  the field, it checks the contents of the field against the regexp.
    for (fieldname in validator.VALIDATIONS) {
      fld = document.getElementById(fieldname);
      if (!fld) continue; // ignore this field if it doesn't exist in the page
      validator.addEvent(fld, "blur", validator.checkField);
    }
    validator.checkForErrors();
  },
  
  checkField: function(e) {
    fld = window.event ? window.event.srcElement : e.target;
    fieldname = fld.id;
    if (validator.VALIDATIONS[fieldname]) {
      re = validator.VALIDATIONS[fieldname]["regexp"];
      if (fld.value.search(re) == -1) {
        // the regular expression didn't match
        // find the span.error element for this field and put the error message in it        
        spans = fld.parentNode.getElementsByTagName('span');
        if (spans.length == 0) {
          // there is no error span, so, create one
          span = document.createElement('span');
          span.className = 'error';
          fld.parentNode.appendChild(span);
        } else {
          span = fld.parentNode.getElementsByTagName('span')[0];
        }
        span.innerHTML = validator.VALIDATIONS[fieldname]["error"];
      } else {
        // the regular expression *did* match
        // is there a span.error already?
        spans = fld.parentNode.getElementsByTagName('span');
        if (spans.length == 0) {
          // there is no error span, so do nothing
        } else {
          // remove the error span
          span = fld.parentNode.getElementsByTagName('span')[0];
          span.parentNode.removeChild(span);
          // and remove class="error" on the field's label, if it has it
          lbl = fld.parentNode.getElementsByTagName('label')[0];
          if (lbl.className == 'error') {
            lbl.className = lbl.className.replace(/\berror\b/,'');
          }
        }
      }
    }
    // finally, disable or enable the submit button as appropriate
    validator.checkForErrors();
  },
  
  checkForErrors: function() {
    // Look for span.error in the page
    var spans = document.getElementsByTagName('span');
    for (var i=0; i<spans.length; i++) {
      // does this span have class=error?
      if (spans[i].className.match(/\berror\b/)) {
        // disable the submit button and exit
        document.getElementById("submitButton").disabled = true;
        return;
      }
    }
    // there were no span.error elements, so enable the submit button
    document.getElementById("submitButton").disabled = false;
  },
  
  addEvent: function( obj, type, fn ) {
  	if (obj.addEventListener) {
  		obj.addEventListener( type, fn, false );
  	} else if (obj.attachEvent) {
  		obj["e"+type+fn] = fn;
  		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
  		obj.attachEvent( "on"+type, obj[type+fn] );
  	}
  }
}

validator.addEvent(window, "load", validator.init);
</script>
</head>
<body>
<h1>A simple PHP form using regular expressions for validation</h1>
<form>
<?php
function field($name, $text) {
  global $ERRORS;
  echo "<p><label for=\"$name\"";
  if (array_key_exists($name,$ERRORS)) {
    echo " class=\"error\"";
  }
  echo ">$text</label>\n";
  echo "<input type=\"text\" id=\"$name\" name=\"$name\">\n";
  if (array_key_exists($name,$ERRORS)) {
    $err = $ERRORS[$name];
    echo "<span class=\"error\">$err</span>";
  }
  echo "</p>\n";
}

field("firstname", "First name");
field("lastname", "Last name");
field("heads", "Number of heads");
field("dob", "Date of birth (DD/MM/YYYY)");
field("email", "Email address of someone you don't like for spamming purposes");
?>
 <p><input type="submit" name="submit" id="submitButton" value="Send answers"></p>
</form>
</body>
</html>


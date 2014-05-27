<html>
<head> 
  <title> Ajax Exmaples! </title>
  <!--Load JQUERY from Google's network -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
  <script> 
    // using JQUERY's ready method to know when all dom elements are rendered
    $( document ).ready(function () {
      // set an on click on the button
      $("#button").click(function () {
        // get the time if clicked via an ajax get queury
        // see the code in the controller time.php
        $.get("/index.php/getTime", function (time) {
          // update the textarea with the time
          $("#text").html("Time on the server is:" + time);
        });
      });
    });
  </script>
</head>
<body>
  <h1> Get Data from Server over Ajax </h1>
  <textarea id="text" readonly>
  </textarea>
  <br/>
  <button id="button">
    Get Time from Server
  </button>
</body>
</html>

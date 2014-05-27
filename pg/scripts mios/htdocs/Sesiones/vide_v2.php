<html>      
<head>
     <title>Dynamic Drop Down List</title>
      <script type="text/javascript">
$('#type_hire').change(function() {
   var selected = $('#type_hire option:selected');  //This should be the selected object
   $.get('DropdownRetrievalScript.php', { 'option': selected.val() }, function(data) {
      //Now data is the results from the DropdownRetrievalScript.php
      $('select[name="employees"]').html(data);
   })
})
</script>
</head>
<body>
    <form id="form1" name="form1" method="post" action="<? $_SERVER['PHP_SELF']?>">
        department: 
        <select id="department" name="department" onchange="run()">  
            <!--Call run() function-->
            <option value="biology">biology</option>
            <option value="chemestry">chemestry</option>
            <option value="physic">physic</option>
            <option value="math">math</option>     
        </select><br><br>
        type_hire: 
        <select id="type_hire" name="type_hire" onchange="run()">  
            <!--Call run() function-->
            <option value="internal">Intenal</option>
            <option value="external">External</option>               
        </select><br><br>
        list of employees:
        <select name='employees'>
            <option value="">--- Select ---</option>
            <?php
            mysql_connect("localhost","root","");
            mysql_select_db("employees_hired");
            $list=mysql_query("SELECT name FROM usuario WHERE (department ='". $value_of_department_list ."') AND (contrasena ='". $value_of_type_hire."')");
            while($row_list=mysql_fetch_assoc($list)){
            ?>
            <option value="<?php echo $row_list['name']; ?>">
                <? if($row_list['name']==$select){ echo $row_list['name']; } ?>
            </option>
            <?php
            }
            ?>
        </select>
    </form> 
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <!--[ I'M GOING TO INCLUDE THE SCRIPT PART DOWN BELOW ]-->
   
</body>
</html>
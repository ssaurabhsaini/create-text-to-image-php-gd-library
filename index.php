<html>

<head>
  <title>Form Iframe Demo</title>
</head>

<body>
  <div style="text-align: center;">
    <form action="create.php" method="get" target="my_frame">
      <p>Company:<input type="text" name="company" value=""></p>
      <p>Category:<input type="text" name="category" value=""></p>
      <p><label for="bg_color">BG Color:</label><input type="color" id="bg_color" name="bg_color" value="#ffffff"></p>
      <p><label for="text_color">Text Color:</label><input type="color" id="text_color" name="text_color" value="#OOOOOO"></p>
      <p>Custom Template User ID:<input type="text" name="custom_template_user_id" value=""></p>
      <p>Questions:<input type="text" name="question" value=""></p>


      <label for="font_family">
        Font Family
        <select name="font_family" id="font_family">
          <?php 
            foreach (glob("fonts/*") as $filename) {
                echo '<option style="font-family:'.ucfirst(strtolower(str_replace(array('fonts/', '.ttf', '.TTF'), '',$filename))).';" value="'.$filename.'">'. ucfirst(strtolower(str_replace(array('fonts/', '.ttf', '.TTF'), '',$filename))) . '</option>';
            }
          ?>
          
        </select>
      </label><br>

      <input type="radio" id="sm_network1" name="sm_network" value="Facebook">
      <label for="sm_network1">Facebook</label><br>
      <input type="radio" id="sm_network2" name="sm_network" value="LinkedIN">
      <label for="sm_network2">LinkedIn</label><br>
      <input type="radio" id="sm_network3" name="sm_network" value="Google">
      <label for="sm_network3">Google My Business</label><br><br>


      <p><input type="submit"></p>
    </form>
  </div>
  <!--
  <iframe name="my_frame" src="create.php" style="width: 100%; height: 100%;">
  </iframe>

  </body>
</html>
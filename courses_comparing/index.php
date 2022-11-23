<?php
    require_once("classes/class.course.php");

    $course = new Course();
    $readed_xml = $course->readXML();
    $course_ecb = $course->getActualCourseFromXML($readed_xml);
    $course_date= DateTime::createFromFormat('Y-m-d', $course->getActualDateFromXML($readed_xml));
    $AXCurrencyAndCourses=$course->getAXCurrencyAndCourse();
?>
<!DOCTYPE html>
<html>
<head>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>

<h2>Aktuálne kurzy (<?php echo $course_date->format('d.m.Y');  ?>)</h2>

<table>
  <tr>
    <th>Mena</th>
    <th>Kurz -> ECB</th>
    <th>Kurz -> AX</th>
    <th>Stav</th>
  </tr>
  <?php 
    foreach($course_ecb as $key => $value){ 
        $AXCourse = $course->getAXCourse($AXCurrencyAndCourses,$key);
    ?>
     
      <tr>
        <td><?php echo $key; ?></td>
        <td><?php echo $value; ?></td>
        <td><?php echo $AXCourse; ?></td>
        <?php echo $course->getCoursStatus($value,$AXCourse); ?>
      </tr>
      
  <?php } ?>
  
</table>

</body>
</html>



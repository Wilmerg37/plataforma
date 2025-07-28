<?php
$yw = "";
$y = date('Y') - 2;
$w = date('W') - 2;
for ($i = 0; $i < 2; $i++) {
  for ($j = 0; $j < 3; $j++) {
    $yw .= ($y + $i) . ($w + $j) . ",";
  }
}
$yw = substr($yw, 0, -1);

echo $yw;

?>
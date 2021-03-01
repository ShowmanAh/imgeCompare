<UL><?php

require_once('../Base.php');
		
$I = Base::Instance();

?> 

<LI> comparison: <?php

$sdf = $I->Compare('monalisa.jpg', 'mon.jpg');
echo print_r($sdf, true);
echo '<br>';

?></LI></UL>



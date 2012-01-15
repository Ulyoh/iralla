<?php
include_once 'Maillon.php';


function show_text_of_the_maillon($first_maillon) {
	$cur_maillon = $first_maillon;
	while ( $cur_maillon != null ) {
		echo "$cur_maillon->text \n";
		$cur_maillon = $cur_maillon->next;
	}
}

echo "***************************************************\n";
echo "         create maillon0";
echo "\n";
$maillon0 = new Maillon ( null, null );
$maillon0->text = 'maillon0';

show_text_of_the_maillon ( $maillon0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon1";
echo "\n";
$maillon1 = new Maillon ( $maillon0, null );
$maillon1->text = 'maillon1';

show_text_of_the_maillon ( $maillon0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon2";
echo "\n";
$maillon2 = new Maillon ( $maillon1, null );
$maillon2->text = 'maillon2';

show_text_of_the_maillon ( $maillon0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon3";
echo "\n";
$maillon3 = new Maillon ( $maillon2, null );
$maillon3->text = 'maillon3';

show_text_of_the_maillon ( $maillon0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon4";
echo "\n";
$maillon4 = new Maillon ( $maillon3, null );
$maillon4->text = 'maillon4';

show_text_of_the_maillon ( $maillon0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon_insert_before_0";
echo "\n";
$maillon_insert_before_0 = new Maillon ();
$maillon_insert_before_0->text = 'maillon_insert_before_0';

Maillon::add_before ( $maillon_insert_before_0, $maillon0 );

show_text_of_the_maillon ( $maillon_insert_before_0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon23";
echo "\n";
$maillon23 = new Maillon ( $maillon2, $maillon3 );
$maillon23->text = 'maillon23';

show_text_of_the_maillon ( $maillon_insert_before_0 );

echo "\n";
echo "***************************************************\n";
echo "         create maillon_insert_before_4";
echo "\n";
$maillon_insert_before_4 = new Maillon ();
$maillon_insert_before_4->text = 'maillon_insert_before_4';

Maillon::add_before ( $maillon_insert_before_4, $maillon4 );

show_text_of_the_maillon ( $maillon_insert_before_0 );

echo "\n";
echo "***************************************************\n";
echo "         swap 1 with 4";
echo "\n";
$maillon1->swap_with ( $maillon4 );
show_text_of_the_maillon ( $maillon_insert_before_0 );



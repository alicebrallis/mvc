<?php

/**
 * Standard view template to generate a simple web page, or part of a web page.
 */

declare(strict_types=1);

$header = $header ?? null;
$message = $message ?? null;


?><h1><?= $header ?></h1>

<p><?= $message ?></p>

<p>DICE!!!</p>

<p><?= $dieLastRoll ?></p>

<p>Dicehand</p>

<p><?= $diehandRoll ?></p>

<p>Dicehand2</p>


<p><?=implode($hand)?></p>

<!doctype html>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">

<p class="dice-utf8">
<?php foreach ($hand as $value) : ?>
    <i class="<?= $value ?>"></i>
<?php endforeach; ?>
</p>


<p>Game 21</p>



<p><?=implode($output)?></p>
<!doctype html>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">

<p class="dice-utf8">
<?php foreach ($output as $value) : ?>
    <i class="<?= $value ?>"></i>
<?php endforeach; ?>
</p>



<?echo "Tärningskastet du angett : " . $_SESSION['output'];
            $output = $_SESSION['output'] ;
            $_SESSION['output'] = null ;
?>

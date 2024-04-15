<?php
namespace Attend;


use Attend\PropelEngine\PropelEngine;

define('FPDF_FONTPATH', '../font/');

include '../lib/bootstrap.php';
$config = bootstrap();


$pdf = new SigninPdf();
$pdf->setEngine(new PropelEngine());
$pdf->getEngine()->connect($config[ 'db' ]);
$pdf->setWeekOf($_GET[ 'week' ]);
if (array_key_exists('dark', $_GET)) {
    for ($i = 0; $i < count($_GET[ 'dark' ]); $i++) {
        $pdf->setDark($_GET{'dark'}[ $i ]);
    }
}
$pdf->Output();

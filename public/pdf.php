<?php
namespace Attend;


use flapjack\attend\AttendancePdf;
use flapjack\attend\PropelEngine;
use flapjack\attend\SigninPdf;

define('FPDF_FONTPATH', '../font/');

include '../lib/bootstrap.php';
$config = bootstrap();


if (array_key_exists('attendance', $_GET)) {
    $pdf = new AttendancePdf();

} else if (array_key_exists('signin', $_GET)) {
    $pdf = new SigninPdf();
}
$pdf->setEngine(new PropelEngine());
$pdf->getEngine()->connect($config[ 'db' ]);
$pdf->setWeekOf($_GET[ 'week' ]);
$pdf->Output();

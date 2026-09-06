<?php

require_once 'app/config/database.php';
require_once 'libs/fpdf/fpdf.php'; 

if (!$conn) {
    die("Database connection failed.");
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Invalid recipe ID.");
}

$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();

if (!$recipe) {
    die("Recipe not found.");
}

/** @var FPDF $pdf */
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

$pdf->SetFillColor(134, 80, 41);
$pdf->Rect(0, 0, 210, 30, 'F');

$pdf->SetTextColor(247, 243, 213);
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetY(10);
$pdf->Cell(0, 10, 'FoodFusion Recipe Card', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(134, 80, 41);
$pdf->Cell(0, 10, $recipe['title'], 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);

$info = '';
if (!empty($recipe['username'])) {
    $info .= "By: " . $recipe['username'] . " | ";
}
if (!empty($recipe['cook_time'])) {
    $info .= "Cook Time: " . $recipe['cook_time'] . " mins | ";
}
if (!empty($recipe['difficulty_level'])) {
    $info .= "Difficulty: " . $recipe['difficulty_level'] . " | ";
}
if (!empty($recipe['category'])) {
    $info .= "Category: " . $recipe['category'];
}

$info = rtrim($info, ' | ');
if (!empty($info)) {
    $pdf->Cell(0, 6, $info, 0, 1, 'C');
}
$pdf->Ln(5);

$pdf->SetDrawColor(134, 80, 41);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(8);

$pdf->SetFillColor(245, 222, 179);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(134, 80, 41);
$pdf->Cell(0, 10, 'Ingredients', 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(50, 50, 50);
$ingredients = explode("\n", $recipe['ingredients']);
foreach ($ingredients as $ingredient) {
    $ingredient = trim($ingredient);
    if (!empty($ingredient)) {
        $pdf->Cell(10, 6, '-', 0, 0);
        $pdf->MultiCell(0, 6, $ingredient);
    }
}
$pdf->Ln(5);

$pdf->SetFillColor(245, 222, 179);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(134, 80, 41);
$pdf->Cell(0, 10, 'Instructions', 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(50, 50, 50);
$instructions = explode("\n", $recipe['instructions']);
$step = 1;
foreach ($instructions as $instruction) {
    $instruction = trim($instruction);
    if (!empty($instruction)) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(20, 6, "Step " . $step . ":", 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 6, $instruction);
        $step++;
        $pdf->Ln(1);
    }
}
$pdf->Ln(5);

if (isset($recipe['likes_count']) && $recipe['likes_count'] > 0) {
    $currentY = $pdf->GetY();
    $pdf->SetDrawColor(134, 80, 41);
    $pdf->Rect(20, $currentY, 170, 12, 'D');
    $pdf->SetY($currentY + 3);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(255, 107, 107);
    $likesText = $recipe['likes_count'] . ' people loved this recipe!';
    $pdf->Cell(0, 6, $likesText, 0, 1, 'C');
}

$pdf->SetY(-15);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->Cell(0, 10, 'Created with FoodFusion', 0, 0, 'C');

$filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $recipe['title']) . '_Recipe_Card.pdf';

$pdf->Output('D', $filename);
exit;
?>
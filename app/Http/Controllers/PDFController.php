<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FPDF;
use App\Models\Activity_request;
use App\Models\Job_request;
use App\Models\Dts_user;

class CustomPDF extends \FPDF
{
    // Header Method
    function Header()
    {
        // Set font
        $this->SetFont('Arial', 'B', 12);

        // Move to the right
        //$this->Cell(80);

        // Add image (path to your image file, x and y position, width, and height)
        //$this->Image('assets/img/blue-curve-frame.png', 0, 0, 210, 297); // Adjust the path, x, y, width, and height
        //$this->Image('assets/img/smooth-purple-wavy.jpg', 0, -10, 210, 60); // Adjust the path, x, y, width, and height

        // Title
        $this->SetFillColor(21, 114, 232);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(190, 12, 'IT Service Requisition Form', 0, 1, 'C', true);

        // Line break
        $this->Ln(4);
    }

    // Footer Method
    function Footer()
    {
        // Set position at 1.5 cm from bottom
        $this->SetY(-15);

        //$this->Image('assets/img/smooth-purple-wavyFooter.jpg', 0, 250, 210, 60); // Adjust the path, x, y, width, and height

        // Set font
        $this->SetFont('Arial', 'I', 8);

        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

class PDFController extends Controller
{
    public function generatePDF($request_code)
    {
        $pendingRequest = Activity_request::where('request_code', $request_code)
            ->where('status', 'pending')
            ->first();

        $acceptedRequest = Activity_request::where('request_code', $request_code)
            ->where('status', 'accepted')
            ->first();

        $completedRequest = Activity_request::where('request_code', $request_code)
            ->where('status', 'completed')
            ->first();

        $requestingTo = Job_request::where('request_code', $request_code)->get();

        $requester = Job_request::where('request_code', $request_code)
            ->with('requester.sectionRel')
            ->first()
            ?->requester;

        $requesterName = trim(($requester?->fname ?? '') . ' ' . ($requester?->mname ?? '') . ' ' . ($requester?->lname ?? '')) ?: 'Unknown';

        $section = $requester?->sectionRel;
        $division = $requester?->divisionRel;

        if (!empty($section?->acronym)) {
            // If acronym exists, combine acronym and division description
            $requesterSection = $section->acronym;

            if (!empty($division?->description)) {
                $requesterSection .= ' - ' . $division->description;
            }
        } else {
            // No acronym, fallback to section description
            $requesterSection = $section?->description ?? 'Unknown';
        }

        $technician = Activity_request::where('request_code', $request_code)
            ->where('status', 'completed')
            ->with('techFromUser')
            ->first();

        $technicianName = trim(($technician?->techFromUser?->fname ?? '') . ' ' . ($technician?->techFromUser?->mname ?? '') . ' ' . ($technician?->techFromUser?->lname ?? '')) ?: 'Unknown';

        $pdf = new CustomPDF();
        $pdf->AddPage();

        $pdf->SetFillColor(177, 208, 247);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(26, 8, "Request Code : ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(68, 8, $pendingRequest->request_code, 0, 0, '', true);
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(11, 8, "Date : ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(35, 8, $pendingRequest->created_at->format('M. d, Y'), 0, 0, '', true);
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(11, 8, "Time : ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(35, 8, $pendingRequest->created_at->format('h:i:s A'), 0, 1, '', true);
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(25, 8, "Requested by : ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(69, 8, iconv('UTF-8', 'windows-1252', $requesterName), 0, 0, 'L', true);
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(13, 8, "Office : ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(81, 8, iconv('UTF-8', 'windows-1252', $requesterSection), 0, 1, '', true);
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(23, 8, "Received by :", 0, 0, '', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', $technicianName), 0, 1, '', true);
        $pdf->Ln(7);
        //----------------------------------------------------

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, 'Requesting to :', 'LTR', 1);

        $startY = $pdf->GetY(); // Save Y position to know where to start the box
        $startX = 10; // margin from left
        $boxWidth = 190;
        $lineHeight = 8;
        $pdf->Ln(3);

        $checkDesktopExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Check Computer Desktop / Laptop');
        });

        $checkPrinterExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Install Printer');
        });

        $checkInternetExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Check Internet Connection');
        });

        $checkInstallAppExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Install Software Application');
        });

        $checkMonitorExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Check Monitor');
        });

        $checkBiometricsExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Biometrics Registration');
        });

        $checkMouseKeybExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Check Mouse / Keyboard');
        });

        $checkTechAssistExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'System Technical Assistance');
        });

        $checkOthersExist = $requestingTo->contains(function ($item) {
            return str_contains($item->description, 'Others');
        });

        $requestingOthers = Job_request::where('request_code', $request_code)->first();

        $othersText = '';
        if ($requestingOthers && $requestingOthers->description) {
            $descriptions = array_map('trim', explode(',', $requestingOthers->description));

            $othersIndex = array_search('Others', $descriptions);

            if ($othersIndex !== false && isset($descriptions[$othersIndex + 1])) {
                $othersText = $descriptions[$othersIndex + 1];
            }
        }

        // Determine image for each description
        $imageDesktop = $checkDesktopExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imagePrinter = $checkPrinterExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageInternet = $checkInternetExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageInstallApp = $checkInstallAppExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageMonitor = $checkMonitorExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageBiometrics = $checkBiometricsExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageMouseKeyb = $checkMouseKeybExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageTechAssist = $checkTechAssistExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';
        $imageOthers = $checkOthersExist ? '/assets/img/checkbox-checked.png' : '/assets/img/checkbox-empty.png';

        $pdf->SetFont('Arial', '', 10);
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageDesktop, 15, $pdf->GetY(), 6, 6);
        $pdf->Cell(12);
        $pdf->Cell(85, 6, "Check Computer Desktop / Laptop", 0, 0);

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imagePrinter, $pdf->GetX(), $pdf->GetY(), 6, 6);
        $pdf->Cell(8);
        $pdf->Cell(5, 6, "Install Printer", 0, 1 );

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageInternet, 15, $pdf->GetY(), 6, 6);
        $pdf->Cell(12);
        $pdf->Cell(85, 6, "Check Internet Connection", 0, 0);

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageInstallApp, $pdf->GetX(), $pdf->GetY(), 6, 6);
        $pdf->Cell(8);
        $pdf->Cell(5, 6, "Install Software Application", 0, 1 );

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageMonitor, 15, $pdf->GetY(), 6, 6);
        $pdf->Cell(12);
        $pdf->Cell(85, 6, "Check Monitor", 0, 0);

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageBiometrics, $pdf->GetX(), $pdf->GetY(), 6, 6);
        $pdf->Cell(8);
        $pdf->Cell(5, 6, "Biometrics Registration", 0, 1 );

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageMouseKeyb, 15, $pdf->GetY(), 6, 6);
        $pdf->Cell(12);
        $pdf->Cell(85, 6, "Check Mouse / Keyboard", 0, 0);

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $imageTechAssist, $pdf->GetX(), $pdf->GetY(), 6, 6);
        $pdf->Cell(8);
        $pdf->Cell(5, 6, "System Technical Assistance", 0, 1 );

        $pdf->Image($_SERVER['DOCUMENT_ROOT'] .  $imageOthers, 15, $pdf->GetY(), 6, 6);
        $pdf->Cell(12);
        $pdf->Cell(0, 6, "Others : (Please Specify)", 0, 1);
        $pdf->Cell(12);
        $pdf->Cell(0, 6, iconv('UTF-8', 'windows-1252', $othersText), 0, 1);

        $endY = $pdf->GetY(); // Save ending Y to calculate height of the box
        $pdf->Rect($startX, $startY, $boxWidth, $endY - $startY); // Draw rectangle around the content
        $pdf->Ln(7);
        //--------------------------------------------------------------------------

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(21, 114, 232);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, 'IT Job Report Form', 0, 1, 'C', true);
        $pdf->Ln(4);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(52, 8, "Fault Detection :", 'LT', 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', $completedRequest->diagnosis), 'LTR', 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(52, 8, "Work Done :", 'LT', 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', $completedRequest->action), 'LTR', 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(52, 8, "Remarks / Recommendation :", 'LTB', 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', $completedRequest->resolution_notes), 'LTRB', 1);
        $pdf->Ln(7);
        //--------------------------------------------------------------------------

        $pdf->Cell(52, 8, "", 'LT', 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(27, 8, "Date :", 'LT', 0);
        $pdf->Cell(111, 8, $acceptedRequest->created_at->format('F j, Y'), 'TR', 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(52, 8, "Acted Upon :", 'L', 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(27, 8, "Time :", 'LT', 0);
        $pdf->Cell(111, 8, $acceptedRequest->created_at->format('h:i:s A'), 'TR',1);

        $pdf->Cell(52, 8, "", 'LB', 0);
        $pdf->Cell(27, 8, "Serviced by :", 'LTB', 0);
        $pdf->Cell(111, 8, iconv('UTF-8', 'windows-1252', $technicianName), 'TRB', 1);
        $pdf->Ln(7);
        //--------------------------------------------------------------------------

        $pdf->Cell(52, 8, "", 'LT', 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(27, 8, "Date :", 'LT', 0);
        $pdf->Cell(111, 8, $completedRequest->created_at->format('F j, Y'), 'TR', 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(52, 8, "Completion :", 'L', 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(27, 8, "Time :", 'LT', 0);
        $pdf->Cell(111, 8, $completedRequest->created_at->format('h:i:s A'), 'TR',1);

        $pdf->Cell(52, 8, "", 'LB', 0);
        $pdf->Cell(27, 8, "Confirmed by :", 'LTB', 0);
        $pdf->Cell(111, 8, iconv('UTF-8', 'windows-1252', $requesterName), 'TRB', 1);
        $pdf->Ln(7);

        // Output the PDF
        $pdf->Output();
        exit;
    }
}

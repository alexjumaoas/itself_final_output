<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Activity_request;

class ExcelReportController extends Controller
{
    public function generateExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'REQUEST CODE');
        $sheet->setCellValue('B1', 'REQUESTER');
        $sheet->setCellValue('C1', 'DESCRIPTION REQUESTS');
        $sheet->setCellValue('D1', 'TECHNICIAN');
        $sheet->setCellValue('E1', 'DATE COMPLETED');
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true
            ]
        ]);

        // Handle date range filter
        $from = null;
        $to = null;
        $query = Activity_request::with(['job_req', 'techFromUser'])
            ->where('status', 'completed');

        if ($request->filled('date_range')) {
            $rawDate = $request->input('date_range');
            $dates = explode(' to ', $rawDate);

            try {
                if (count($dates) === 1) {
                    $from = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $to = \Carbon\Carbon::parse($dates[0])->endOfDay();
                } elseif (count($dates) === 2) {
                    $from = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $to = \Carbon\Carbon::parse($dates[1])->endOfDay();
                }

                if ($from && $to) {
                    $query->whereBetween('updated_at', [$from, $to]);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['date_range' => 'Invalid date format.']);
            }
        }
        $requests = $query->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No records found for the selected date or date range.');
        }

        $row = 2; // Start from row 2
        foreach ($requests as $record) {
            $requester = optional($record->job_req->requester);
            $fullName = trim("{$requester->fname} {$requester->mname} {$requester->lname}");
            $technician = optional($record->techFromUser);
            $techFullName = trim("{$technician->fname} {$technician->mname} {$technician->lname}");

            $sheet->setCellValue("A$row", $record->request_code);
            $sheet->setCellValue("B$row", $fullName ?: 'N/A');
            $sheet->setCellValue("C$row", optional($record->job_req)->description ?? 'N/A');
            $sheet->setCellValue("D$row", $techFullName ?: $record->tech_from);
            $sheet->setCellValue("E$row", $record->updated_at ? $record->updated_at->format('Y-m-d') : 'N/A');
            $row++;
        }

        // Auto-size columns A to E
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Request_Report.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\""
        ]);
    }
}

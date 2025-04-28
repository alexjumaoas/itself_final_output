<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use App\Models\Dtruser;
use App\Models\Dts_user;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Activity_request;


class ExcelAdminPerTechController extends Controller
{
    public function generateExcel($username)
    {
        // Fetch the user and verify existence
        $user = \App\Models\Dtruser::where('username', $username)->with('dtsUser.designationRel')->first();
        if (!$user) {
            abort(404, 'Technician not found.');
        }

        // Fetch completed activity requests assigned to the technician
        $completedRequests = \App\Models\Activity_request::with(['job_req', 'techFromUser'])
            ->where('tech_from', $username)
            ->where('status', 'completed')
            ->get();

        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headings
        $sheet->setCellValue('A1', 'Request Code');
        $sheet->setCellValue('B1', 'Request Date');
        $sheet->setCellValue('C1', 'Requester');
        $sheet->setCellValue('D1', 'Description');
        $sheet->setCellValue('E1', 'Technician');
        $sheet->setCellValue('F1', 'Fault Detection');
        $sheet->setCellValue('G1', 'Action Taken');
        $sheet->setCellValue('H1', 'Resolution Notes');
        $sheet->setCellValue('I1', 'Completion Date');
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true
            ]
        ]);

        // Fill data
        $row = 2;
        foreach ($completedRequests as $request) {
            $sheet->setCellValue('A' . $row, $request->request_code);
            $sheet->setCellValue('B' . $row, $request->job_req->request_date ?? 'N/A');
            $sheet->setCellValue('C' . $row,
                ($request->job_req->requester->fname ?? '') . ' ' .
                ($request->job_req->requester->mname ?? '') . ' ' .
                ($request->job_req->requester->lname ?? ''));
            $sheet->setCellValue('D' . $row, $request->job_req->description ?? 'N/A');
            $sheet->setCellValue('E' . $row,
                ($request->techFromUser->dtrUsers->first()->fname ?? '') . ' ' .
                ($request->techFromUser->dtrUsers->first()->mname ?? '') . ' ' .
                ($request->techFromUser->dtrUsers->first()->lname ?? '')
            );
            $sheet->setCellValue('F' . $row, $request->diagnosis ?? '');
            $sheet->setCellValue('G' . $row, $request->action ?? '');
            $sheet->setCellValue('H' . $row, $request->resolution_notes ?? '');
            $sheet->setCellValue('I' . $row, $request->created_at ?? 'N/A');
            $row++;
        }

         // Auto-size columns A to E
         foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Generate Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'completed_requests_' . $username . '.xlsx';

        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

}

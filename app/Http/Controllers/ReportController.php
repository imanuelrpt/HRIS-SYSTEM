<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyReportExport;
use App\Http\Requests\ReportFilterRequest;
use App\Models\Attendance;
use App\Models\User;
use App\Services\MonthlyReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(ReportFilterRequest $request, MonthlyReportService $reports): View
    {
        Gate::authorize('viewReports', Attendance::class);

        /** @var User $user */
        $user = $request->user();

        return view('reports.index', $reports->build(
            $request->validated(),
            Gate::allows('viewAll', Attendance::class),
            $user->id,
        ));
    }

    public function exportExcel(ReportFilterRequest $request, MonthlyReportService $reports): BinaryFileResponse
    {
        Gate::authorize('viewReports', Attendance::class);

        /** @var User $user */
        $user = $request->user();
        $report = $reports->build(
            $request->validated(),
            Gate::allows('viewAll', Attendance::class),
            $user->id,
        );

        $fileName = sprintf(
            'rekap-absensi-%04d-%02d.xlsx',
            $report['period']['year'],
            $report['period']['month'],
        );

        return Excel::download(
            new MonthlyReportExport(collect($report['rows']), $report['period']),
            $fileName,
        );
    }

    public function exportPdf(ReportFilterRequest $request, MonthlyReportService $reports): Response
    {
        Gate::authorize('viewReports', Attendance::class);

        /** @var User $user */
        $user = $request->user();
        $report = $reports->build(
            $request->validated(),
            Gate::allows('viewAll', Attendance::class),
            $user->id,
        );

        $fileName = sprintf(
            'rekap-absensi-%04d-%02d.pdf',
            $report['period']['year'],
            $report['period']['month'],
        );

        return Pdf::loadView('reports.pdf', $report)
            ->setPaper('a4', 'landscape')
            ->setWarnings(false)
            ->download($fileName);
    }
}

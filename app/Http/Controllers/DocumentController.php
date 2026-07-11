<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use OpenApi\Attributes as OA;

class DocumentController extends Controller
{
        #[OA\Get(
        path: '/api/documents',
        summary: 'Menampilkan semua dokumen HKI',
        tags: ['Documents'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil data dokumen'
            )
        ]
    )]
    public function index(Request $request)
    {
        $query = Document::with([
            'category',
            'user'
        ]);

        if ($request->has('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        if ($request->has('sort')) {
            $query->orderBy(
                $request->sort,
                'asc'
            );
        }

        $documents = $query->paginate(5);

        return response()->json([
            'success' => true,
            'message' => 'Data dokumen berhasil diambil',
            'data' => $documents
        ]);
    }

    #[OA\Post(
        path: '/api/documents',
        summary: 'Tambah Dokumen HKI',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nomor_hki', type: 'string', example: 'HKI001'),
                    new OA\Property(property: 'judul', type: 'string', example: 'Sistem Inventaris HKI'),
                    new OA\Property(property: 'pemilik', type: 'string', example: 'UNPAM'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tanggal_daftar', type: 'string', example: '2026-07-11'),
                    new OA\Property(property: 'status', type: 'string', example: 'Pending')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Dokumen berhasil ditambahkan'
            )
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_hki' => 'required',
            'judul' => 'required',
            'pemilik' => 'required',
            'category_id' => 'required|exists:categories,id',
            'tanggal_daftar' => 'required|date',
            'status' => 'required',
            'file_dokumen' => 'nullable|mimes:pdf|max:2048'
        ]);

        if ($request->hasFile('file_dokumen')) {
            $validated['file_dokumen'] = $request
                ->file('file_dokumen')
                ->store('documents', 'public');
        }
        $validated['user_id'] = $request->user()->id;

        $document = Document::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil ditambahkan',
            'data' => $document
        ], 201);
    }

    #[OA\Get(
        path: '/api/documents/{id}',
        summary: 'Detail Dokumen',
        tags: ['Documents'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail dokumen'
            )
        ]
    )]
    public function show(string $id)
    {
        $document = Document::with([
        'category',
        'user'
    ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $document->id,
                'judul' => $document->judul,
                'file_dokumen' => $document->file_dokumen,
                'file_url' => Storage::url($document->file_dokumen)
            ]
        ]);
    }

    #[OA\Put(
        path: '/api/documents/{id}',
        summary: 'Update Dokumen',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dokumen berhasil diperbarui'
            )
        ]
    )]
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nomor_hki' => 'required|max:50',
            'judul' => 'required|max:255',
            'pemilik' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'tanggal_daftar' => 'required|date',
            'status' => 'required|max:50'
        ]);

        $document = Document::findOrFail($id);

        $document->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diperbarui',
            'data' => $document
        ]);
    }

    #[OA\Delete(
        path: '/api/documents/{id}',
        summary: 'Hapus Dokumen ke Recycle Bin',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dokumen masuk ke recycle bin'
            )
        ]
    )]
    public function destroy(string $id)
    {
        $document = Document::findOrFail($id);

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen masuk ke recycle bin'
        ]);
    }

    #[OA\Get(
        path: '/api/documents/search/{keyword}',
        summary: 'Cari Dokumen',
        tags: ['Documents'],
        parameters: [
            new OA\Parameter(
                name: 'keyword',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hasil pencarian'
            )
        ]
    )]
    public function search($keyword)
    {
        $documents = Document::where('judul', 'like', "%$keyword%")
            ->orWhere('nomor_hki', 'like', "%$keyword%")
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian dokumen',
            'data' => $documents
        ]);
    }

    #[OA\Get(
        path: '/api/documents/status/{status}',
        summary: 'Filter Dokumen Berdasarkan Status',
        tags: ['Documents'],
        parameters: [
            new OA\Parameter(
                name: 'status',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data berdasarkan status'
            )
        ]
    )]
    public function filterStatus($status)
    {
        $documents = Document::where('status', $status)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berdasarkan status',
            'data' => $documents
        ]);
    }

    
    #[OA\Get(
    path: '/api/documents/dashboard',
    summary: 'Dashboard dokumen',
    tags: ['Documents'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Data dashboard'
            )
        ]
    )]
    public function dashboard()
    {
        return response()->json([
    'success' => true,
    'message' => 'Data dashboard',
    'data' => [
        'total_dokumen' => Document::count(),
        'pending' => Document::where('status', 'Pending')->count(),
        'disetujui' => Document::where('status', 'Disetujui')->count(),
        'ditolak' => Document::where('status', 'Ditolak')->count()
        ]
    ]);
    }

        #[OA\Get(
        path: '/api/statistics/categories',
        summary: 'Statistik dokumen per kategori',
        tags: ['Documents'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Statistik dokumen'
            )
        ]
    )]
    public function statisticsByCategory()
    {
        $statistics = Document::join(
            'categories',
            'documents.category_id',
            '=',
            'categories.id'
        )
        ->selectRaw(
            'categories.nama as kategori, COUNT(documents.id) as total'
        )
        ->groupBy('categories.nama')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dokumen per kategori',
            'data' => $statistics
        ]);
    }

    #[OA\Get(
        path: '/api/documents/{id}/download',
        summary: 'Download PDF Dokumen',
        tags: ['Documents'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'File PDF berhasil didownload'
            )
        ]
    )]
    public function download($id)
    {
        $document = Document::findOrFail($id);

        if (!$document->file_dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $path = storage_path(
            'app/public/' . $document->file_dokumen
        );

        return response()->download(
            $path,
            $document->judul . '.pdf'
        );
    }

    #[OA\Get(
        path: '/api/documents/trash',
        summary: 'Lihat Dokumen di Recycle Bin 🗑️',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data dokumen yang dihapus'
            )
        ]
    )]
    public function trash()
    {
        $documents = Document::onlyTrashed()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data recycle bin',
            'data' => $documents
        ]);
    }

    #[OA\Put(
        path: '/api/documents/{id}/restore',
        summary: 'Restore Dokumen dari Recycle Bin ♻️',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dokumen berhasil dipulihkan'
            )
        ]
    )]
    public function restore($id)
    {
        $document = Document::onlyTrashed()
            ->findOrFail($id);

        $document->restore();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'restore',
            'description' => 'Dokumen "' . $document->judul . '" dipulihkan dari recycle bin',
            'document_id' => $id,
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dipulihkan'
        ]);
    }

    #[OA\Get(
        path: '/api/documents/export/excel',
        summary: 'Export Dokumen ke Excel 📊',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'File Excel berhasil diexport'
            )
        ]
    )]
    public function exportExcel()
    {
        try {
            $documents = Document::with('category', 'user')->get();
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'export_excel',
                'description' => 'Export dokumen ke Excel (' . count($documents) . ' dokumen)',
                'ip_address' => request()->ip()
            ]);

            return response()->streamDownload(function () use ($documents) {
                $data = fopen('php://output', 'w');
                
                fputcsv($data, [
                    'ID', 'Nomor HKI', 'Judul', 'Pemilik', 'Kategori', 
                    'User', 'Tanggal Daftar', 'Status', 'Created At'
                ]);

                foreach ($documents as $document) {
                    fputcsv($data, [
                        $document->id,
                        $document->nomor_hki,
                        $document->judul,
                        $document->pemilik,
                        $document->category->nama ?? '-',
                        $document->user->name ?? '-',
                        $document->tanggal_daftar,
                        $document->status,
                        $document->created_at
                    ]);
                }

                fclose($data);
            }, 'documents_' . date('Y-m-d_H-i-s') . '.csv');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export dokumen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: '/api/documents/export/pdf',
        summary: 'Export Dokumen ke PDF 📄',
        tags: ['Documents'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'File PDF berhasil diexport'
            )
        ]
    )]
    public function exportPdf()
    {
        try {
            $documents = Document::with('category', 'user')->get();

            $html = '<html><head><meta charset="UTF-8"><style>';
            $html .= 'body { font-family: Arial; margin: 20px; }';
            $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
            $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
            $html .= 'th { background-color: #4CAF50; color: white; }';
            $html .= 'h1 { color: #333; text-align: center; }';
            $html .= 'tr:nth-child(even) { background-color: #f2f2f2; }';
            $html .= '</style></head><body>';
            $html .= '<h1>📋 Laporan Dokumen HKI</h1>';
            $html .= '<p>Tanggal: ' . date('d-m-Y H:i:s') . '</p>';
            $html .= '<table>';
            $html .= '<tr><th>ID</th><th>Nomor HKI</th><th>Judul</th><th>Pemilik</th>';
            $html .= '<th>Kategori</th><th>User</th><th>Tanggal Daftar</th><th>Status</th></tr>';

            foreach ($documents as $doc) {
                $html .= '<tr>';
                $html .= '<td>' . $doc->id . '</td>';
                $html .= '<td>' . $doc->nomor_hki . '</td>';
                $html .= '<td>' . $doc->judul . '</td>';
                $html .= '<td>' . $doc->pemilik . '</td>';
                $html .= '<td>' . ($doc->category->nama ?? '-') . '</td>';
                $html .= '<td>' . ($doc->user->name ?? '-') . '</td>';
                $html .= '<td>' . $doc->tanggal_daftar . '</td>';
                $html .= '<td>' . $doc->status . '</td>';
                $html .= '</tr>';
            }

            $html .= '</table></body></html>';

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'export_pdf',
                'description' => 'Export dokumen ke PDF (' . count($documents) . ' dokumen)',
                'ip_address' => request()->ip()
            ]);

            return response()->streamDownload(
                function () use ($html) {
                    echo $html;
                },
                'documents_' . date('Y-m-d_H-i-s') . '.html'
            );

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    #[OA\Delete(
    path: '/api/documents/{id}/force-delete',
    summary: 'Hapus Dokumen Permanen 🗑️',
    tags: ['Documents'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Dokumen berhasil dihapus permanen'
        )
    ]
    )]

    public function forceDelete($id)
    {
        $document = Document::onlyTrashed()
            ->findOrFail($id);

        $judul = $document->judul;

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'force_delete',
            'description' => 'Dokumen "' . $judul . '" dihapus permanen dari recycle bin',
            'document_id' => null,
            'ip_address' => request()->ip()
        ]);

        if ($document->file_dokumen) {
            Storage::disk('public')->delete($document->file_dokumen);
        }

        $document->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus permanen'
        ]);
    }
    #[OA\Get(
        path: '/api/activity-log',
        summary: 'Lihat Activity Log 📋',
        tags: ['Activity'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 50)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar activity log'
            )
        ]
    )]
    public function getActivityLog(Request $request)
    {
        $limit = $request->get('limit', 50);
        
        $logs = ActivityLog::with('user', 'document')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Activity Log',
            'data' => $logs
        ]);
    }

    #[OA\Get(
        path: '/api/dashboard/chart',
        summary: 'Dashboard Chart Data 📈',
        tags: ['Dashboard'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data untuk chart dashboard'
            )
        ]
    )]
    public function dashboardChart()
    {
        $categoryStats = Document::join(
            'categories',
            'documents.category_id',
            '=',
            'categories.id'
        )
        ->selectRaw('categories.nama, COUNT(documents.id) as total')
        ->groupBy('categories.nama')
        ->get();

        $statusStats = Document::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $monthlyStats = Document::selectRaw(
            'DATE_FORMAT(created_at, "%Y-%m") as bulan, COUNT(*) as total'
        )
        ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
        ->orderBy('bulan', 'asc')
        ->get();

        $totalRecycleBin = Document::onlyTrashed()->count();

        return response()->json([
            'success' => true,
            'message' => 'Data Chart Dashboard',
            'data' => [
                'category_statistics' => $categoryStats,
                'status_statistics' => $statusStats,
                'monthly_statistics' => $monthlyStats,
                'recycle_bin_count' => $totalRecycleBin,
                'summary' => [
                    'total_documents' => Document::count(),
                    'pending' => Document::where('status', 'Pending')->count(),
                    'approved' => Document::where('status', 'Disetujui')->count(),
                    'rejected' => Document::where('status', 'Ditolak')->count(),
                ]
            ]
        ]);
    }
    

}
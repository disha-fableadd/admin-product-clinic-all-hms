<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patients;
use App\Models\Appointments;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class chartController extends Controller
{
    

    public function getChartData(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $request->user_id;
        $filter = $request->filter ?? 'year';

        $now = now();
        switch ($filter) {
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $groupFormat = 'd M';
                $labelFormat = 'd M';
                $range = collect(range(0, $now->daysInMonth - 1))->map(function ($i) use ($startDate) {
                    return $startDate->copy()->addDays($i)->format('d M');
                });
                break;

            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $groupFormat = 'D';
                $labelFormat = 'D';
                $range = collect(range(0, 6))->map(function ($i) use ($startDate) {
                    return $startDate->copy()->addDays($i)->format('D');
                });
                break;

            default:
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $groupFormat = 'm';
                $labelFormat = 'F';
                $range = collect(range(1, 12))->map(function ($i) {
                    return str_pad($i, 2, '0', STR_PAD_LEFT); // gives "01", "02", ..., "12"
                });
        }

        $patients = $this->getDataByCreatedAt($userId, 'patients', $startDate, $endDate, $groupFormat);
        $doctors = $this->getDataByCreatedAt($userId, 'user', $startDate, $endDate, $groupFormat, 3);
        $receptionists = $this->getDataByCreatedAt($userId, 'user', $startDate, $endDate, $groupFormat, 4);
        $appointments = $this->getDataByCreatedAt($userId, 'appointments', $startDate, $endDate, $groupFormat);
        $inventory = $this->getDataByCreatedAt($userId, 'inventory', $startDate, $endDate, $groupFormat);
        $treatments = $this->getDataByCreatedAt($userId, 'treatments', $startDate, $endDate, $groupFormat);
    // dd($patients);
        $allData = [
            'labels' => $range->toArray(),
            'patients' => $this->fillMissingData($patients, $range),
            'doctors' => $this->fillMissingData($doctors, $range),
            'receptionists' => $this->fillMissingData($receptionists, $range),
            'appointments' => $this->fillMissingData($appointments, $range),
            'inventory' => $this->fillMissingData($inventory, $range),
            'treatments' => $this->fillMissingData($treatments, $range),
        ];
        // dd($allData);
        return response()->json(['chartData' => $allData]);
    }

    private function getDataByCreatedAt($userId, $type, $startDate, $endDate, $groupFormat, $roleId = null)
    {
        $query = DB::table($type);
    
        $loggedInUser = auth()->user();
    
        // Filter based on type
        if ($type === 'patients') {
            $query->where('user_id', $userId);
        } elseif ($type === 'appointments') {
            // Show only doctor's appointments if user is doctor
            if ($loggedInUser->role === 'Doctor' || $loggedInUser->role_id == 3) {
                $query->where('doctor_id', $userId);
            }
            // If admin, no need to filter by doctor_id
            // Optionally add filter for other roles if needed
        }
    
        // Choose date column
        $dateColumn = ($type === 'appointments') ? 'date' : 'created_at';
    
        $query->whereBetween($dateColumn, [$startDate, $endDate]);
    
        if ($roleId) {
            $query->where('role_id', $roleId);
        }
    
        return $query->get()
            ->groupBy(function ($item) use ($groupFormat, $dateColumn) {
                return Carbon::parse($item->$dateColumn)->format($groupFormat);
            })
            ->map(function ($group) {
                return $group->count();
            });
    }
    


    public function getReportOverview(Request $request)
    {
        try {
            $user = auth()->user() ?? auth('sanctum')->user();
            if (!$user) {
                $user = \Illuminate\Support\Facades\Auth::user();
            }

            $filter = $request->filter ?? 'month';
            $branchId = ($request->branch_id && $request->branch_id !== 'null' && $request->branch_id !== 'undefined') ? $request->branch_id : null;

            if (!$branchId && $user && !empty($user->branch_id) && !in_array($user->role_id, [1, 2])) {
                $branchId = $user->branch_id;
            }

            $applyBranch = function ($query) use ($branchId) {
                if (!$branchId) {
                    return $query;
                }
                return $query->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhere('branch_id', (string) $branchId)
                      ->orWhereNull('branch_id')
                      ->orWhere('branch_id', '');
                });
            };

            $now = Carbon::now();
            switch ($filter) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $periodLabel = 'Today (' . $now->format('d M Y') . ')';
                    break;
                case 'week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    $periodLabel = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                    break;
                case 'year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    $periodLabel = 'This Year (' . $now->format('Y') . ')';
                    break;
                case 'month':
                default:
                    $filter = 'month';
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $periodLabel = 'This Month (' . $now->format('F Y') . ')';
                    break;
            }

            $startDateStr = $startDate->toDateString();
            $endDateStr = $endDate->toDateString();

            // 1. Overall counts
            $totalPatients = $applyBranch(\App\Models\Patients::query())->count();
            $totalDoctors = $applyBranch(User::query())
                ->whereNotIn('role_id', [1, 5])
                ->count();
            $totalTreatments = $applyBranch(\App\Models\Treatment::query())->count();
            $totalRefDoctors = $applyBranch(\App\Models\ReferalDoctor::query())->count();

            // 2. Period scoped metrics
            $totalAppointments = $applyBranch(Appointments::query())
                ->where(function ($q) use ($startDateStr, $endDateStr) {
                    $q->whereBetween('date', [$startDateStr, $endDateStr])
                        ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                            $sq->whereNull('date')
                                ->whereBetween(DB::raw('DATE(created_at)'), [$startDateStr, $endDateStr]);
                        });
                })
                ->count();

            $totalFollowups = $applyBranch(\App\Models\Followup::query())
                ->where(function ($q) use ($startDateStr, $endDateStr) {
                    $q->whereBetween('date', [$startDateStr, $endDateStr])
                        ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                            $sq->whereNull('date')
                                ->whereBetween(DB::raw('DATE(created_at)'), [$startDateStr, $endDateStr]);
                        });
                })
                ->count();

            // Invoices query for the period
            $invoiceBaseQuery = $applyBranch(\App\Models\Invoice::query())
                ->where(function ($q) use ($startDateStr, $endDateStr) {
                    $q->whereBetween('date', [$startDateStr, $endDateStr])
                        ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                            $sq->whereNull('date')
                                ->whereBetween(DB::raw('DATE(created_at)'), [$startDateStr, $endDateStr]);
                        });
                });

            $totalRevenue = (clone $invoiceBaseQuery)->sum(DB::raw('COALESCE(grand_total, 0)'));
            $totalTransactions = (clone $invoiceBaseQuery)->count();

            $paidRevenue = (clone $invoiceBaseQuery)
                ->whereRaw("LOWER(COALESCE(payment_status, '')) IN ('paid', 'completed')")
                ->sum(DB::raw('COALESCE(grand_total, 0)'));

            $pendingRevenue = (clone $invoiceBaseQuery)
                ->whereRaw("LOWER(COALESCE(payment_status, '')) NOT IN ('paid', 'completed')")
                ->sum(DB::raw('COALESCE(grand_total, 0)'));

            $paidTransactions = (clone $invoiceBaseQuery)
                ->whereRaw("LOWER(COALESCE(payment_status, '')) IN ('paid', 'completed')")
                ->count();

            // Appointment status distribution for period
            $completedAppointments = $applyBranch(Appointments::query())
                ->where(function ($q) use ($startDateStr, $endDateStr) {
                    $q->whereBetween('date', [$startDateStr, $endDateStr])
                        ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                            $sq->whereNull('date')
                                ->whereBetween(DB::raw('DATE(created_at)'), [$startDateStr, $endDateStr]);
                        });
                })
                ->whereRaw("LOWER(COALESCE(status, '')) = 'completed'")
                ->count();

            $cancelledAppointments = $applyBranch(Appointments::query())
                ->where(function ($q) use ($startDateStr, $endDateStr) {
                    $q->whereBetween('date', [$startDateStr, $endDateStr])
                        ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                            $sq->whereNull('date')
                                ->whereBetween(DB::raw('DATE(created_at)'), [$startDateStr, $endDateStr]);
                        });
                })
                ->whereRaw("LOWER(COALESCE(status, '')) IN ('cancelled', 'canceled')")
                ->count();

            $upcomingAppointments = max(0, $totalAppointments - $completedAppointments - $cancelledAppointments);

            // 3. Revenue Trend Chart
            $chartLabels = [];
            $chartValues = [];

            if ($filter === 'today') {
                for ($h = 8; $h <= 20; $h++) {
                    $hourLabel = sprintf('%02d:00', $h);
                    $chartLabels[] = $hourLabel;
                    $val = $applyBranch(\App\Models\Invoice::query())
                        ->whereDate('date', $now->toDateString())
                        ->whereTime('created_at', '>=', sprintf('%02d:00:00', $h))
                        ->whereTime('created_at', '<=', sprintf('%02d:59:59', $h))
                        ->sum(DB::raw('COALESCE(grand_total, 0)'));
                    $chartValues[] = round((float) $val, 2);
                }
            } elseif ($filter === 'week') {
                for ($d = 0; $d < 7; $d++) {
                    $dayDate = $startDate->copy()->addDays($d);
                    $chartLabels[] = $dayDate->format('D, d M');
                    $val = $applyBranch(\App\Models\Invoice::query())
                        ->where(function ($q) use ($dayDate) {
                            $ds = $dayDate->toDateString();
                            $q->whereDate('date', $ds)
                                ->orWhere(function ($sq) use ($ds) {
                                    $sq->whereNull('date')->whereDate('created_at', $ds);
                                });
                        })
                        ->sum(DB::raw('COALESCE(grand_total, 0)'));
                    $chartValues[] = round((float) $val, 2);
                }
            } elseif ($filter === 'year') {
                for ($m = 1; $m <= 12; $m++) {
                    $monthDate = Carbon::create($now->year, $m, 1);
                    $chartLabels[] = $monthDate->format('M');
                    $val = $applyBranch(\App\Models\Invoice::query())
                        ->where(function ($q) use ($now, $m) {
                            $q->whereYear('date', $now->year)->whereMonth('date', $m)
                                ->orWhere(function ($sq) use ($now, $m) {
                                    $sq->whereNull('date')->whereYear('created_at', $now->year)->whereMonth('created_at', $m);
                                });
                        })
                        ->sum(DB::raw('COALESCE(grand_total, 0)'));
                    $chartValues[] = round((float) $val, 2);
                }
            } else { // month
                $daysInMonth = $now->daysInMonth;
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dayDate = $startDate->copy()->day($d);
                    $chartLabels[] = $dayDate->format('d M');
                    $val = $applyBranch(\App\Models\Invoice::query())
                        ->where(function ($q) use ($dayDate) {
                            $ds = $dayDate->toDateString();
                            $q->whereDate('date', $ds)
                                ->orWhere(function ($sq) use ($ds) {
                                    $sq->whereNull('date')->whereDate('created_at', $ds);
                                });
                        })
                        ->sum(DB::raw('COALESCE(grand_total, 0)'));
                    $chartValues[] = round((float) $val, 2);
                }
            }

            // 4. Treatment Performance Table
            $treatmentList = $applyBranch(\App\Models\Treatment::with('doctor'))
                ->get()
                ->map(function ($t) use ($startDateStr, $endDateStr) {
                    $appCount = Appointments::where('treatment_id', $t->id)
                        ->where(function ($q) use ($startDateStr, $endDateStr) {
                            $q->whereBetween('date', [$startDateStr, $endDateStr])
                                ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                                    $sq->whereNull('date')
                                        ->whereBetween(DB::raw('DATE(created_at)'), [$startDateStr, $endDateStr]);
                                });
                        })
                        ->count();

                    $docName = $t->doctor ? ($t->doctor->fullname ?? $t->doctor->name ?? 'Doctor') : 'General Staff';

                    return [
                        'id' => $t->id,
                        'name' => $t->name ?? 'Treatment',
                        'doctor_name' => $docName,
                        'price' => (float) ($t->price ?? 0),
                        'total_appointments' => $appCount,
                        'status' => 'Active',
                    ];
                })
                ->sortByDesc('total_appointments')
                ->values()
                ->take(100);

            // 5. Recent Transactions Table
            $recentTransactions = $applyBranch(\App\Models\Invoice::with('patient'))
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->take(100)
                ->get()
                ->map(function ($inv) {
                    $pName = $inv->patient ? ($inv->patient->fullname ?? $inv->patient->name ?? 'Patient') : 'N/A';
                    return [
                        'id' => $inv->id,
                        'patient_name' => $pName,
                        'type' => ucfirst($inv->type ?? 'Treatment'),
                        'date' => $inv->date ? Carbon::parse($inv->date)->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '--'),
                        'amount' => (float) ($inv->grand_total ?? 0),
                        'method' => ucfirst($inv->payment_type ?? 'Cash'),
                        'status' => ucfirst($inv->payment_status ?? 'Paid'),
                    ];
                });

            return response()->json([
                'status' => true,
                'filter' => $filter,
                'period_label' => $periodLabel,
                'metrics' => [
                    'total_patients' => $totalPatients,
                    'total_doctors' => $totalDoctors,
                    'total_appointments' => $totalAppointments,
                    'total_followups' => $totalFollowups,
                    'total_treatments' => $totalTreatments,
                    'total_reference_doctors' => $totalRefDoctors,
                    'total_revenue' => (float) $totalRevenue,
                    'total_transactions' => $totalTransactions,
                    'paid_revenue' => (float) $paidRevenue,
                    'pending_revenue' => (float) $pendingRevenue,
                    'paid_transactions' => $paidTransactions,
                    'completed_appointments' => $completedAppointments,
                    'upcoming_appointments' => $upcomingAppointments,
                    'cancelled_appointments' => $cancelledAppointments,
                ],
                'revenue_chart' => [
                    'labels' => $chartLabels,
                    'values' => $chartValues,
                ],
                'appointment_distribution' => [
                    'completed' => $completedAppointments,
                    'upcoming' => $upcomingAppointments,
                    'cancelled' => $cancelledAppointments,
                ],
                'treatment_performance' => $treatmentList,
                'recent_transactions' => $recentTransactions,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Report Overview API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Error generating report: ' . $e->getMessage(),
                'filter' => $request->filter ?? 'month',
                'period_label' => 'Period Report',
                'metrics' => [
                    'total_patients' => 0,
                    'total_doctors' => 0,
                    'total_appointments' => 0,
                    'total_followups' => 0,
                    'total_treatments' => 0,
                    'total_reference_doctors' => 0,
                    'total_revenue' => 0,
                    'total_transactions' => 0,
                    'paid_revenue' => 0,
                    'pending_revenue' => 0,
                    'paid_transactions' => 0,
                    'completed_appointments' => 0,
                    'upcoming_appointments' => 0,
                    'cancelled_appointments' => 0,
                ],
                'revenue_chart' => [
                    'labels' => [],
                    'values' => [],
                ],
                'appointment_distribution' => [
                    'completed' => 0,
                    'upcoming' => 0,
                    'cancelled' => 0,
                ],
                'treatment_performance' => [],
                'recent_transactions' => [],
            ], 200);
        }
    }

    private function fillMissingData($data, $range)
    {
        return $range->map(function ($label) use ($data) {
            return $data[$label] ?? 0;
        })->toArray();
    }
}

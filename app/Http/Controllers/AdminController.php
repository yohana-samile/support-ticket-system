<?php
namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\PaymentChannel;
use App\Models\SaasApp;
use App\Models\Status;
use App\Models\Ticket\Ticket;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = user();
        $ticketQuery = Ticket::with(['topic', 'subtopic', 'tertiaryTopic', 'user', 'assignedTo', 'operators']);
        $this->applyFilters($ticketQuery);

        if ($user->is_reporter) {
            return $this->reporterDashboard($user, $ticketQuery);
        }

        // Get counts and statistics
        $stats = $this->getDashboardStats($ticketQuery, $user);

        $filterOptions = [
            'paymentChannels' => PaymentChannel::active()->ordered()->get(),
            'mobileOperators' => Operator::has('tickets')->ordered()->get(),
            'saasApps' => SaasApp::all(),
            'statuses' => Status::getStatusesWithColors()
        ];

        return view('dashboard.backend.dashboard', array_merge($stats, $filterOptions));
    }

    protected function applyFilters($query)
    {
        if (request()->filled('payment_channel')) {
            $query->where('payment_channel_id', request('payment_channel'));
        }
        if (request()->filled('mobile_operator')) {
            $query->whereHas('operators', function($q) {
                $q->where('operators.id', request('mobile_operator'));
            });
        }
        if (request()->filled('saas_app')) {
            $query->where('saas_app_id', request('saas_app'));
        }
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
    }

    protected function reporterDashboard($user, $query)
    {
        $query->where('user_id', $user->id);

        return view('dashboard.frontend.dashboard', [
            'userTickets' => $query->count(),
            'openTickets' => $query->clone()->where('status', 'open')->count(),
            'resolvedTickets' => $query->clone()->where('status', 'resolved')->count(),
            'reOpenTickets' => $query->clone()->whereIn('status', ['reopen', 'reopened'])->count(),
        ]);
    }

    protected function getDashboardStats($query, $user)
    {
        $totalTickets = $query->count();
        $reopenedCount = $query->clone()->where('status', 'reopened')->count();

        // Status counts
        $statusCounts = [];
        foreach (Status::all() as $status) {
            $statusCounts[$status->slug] = $query->clone()->where('status', $status->slug)->count();
        }

        // Reopen statistics
        $reopenStats = [
            'total_tickets' => $totalTickets,
            'never_reopened' => $query->clone()->where('reopen_history_count', 0)->count(),
            'reopened_once' => $query->clone()->where('reopen_history_count', 1)->count(),
            'reopened_twice' => $query->clone()->where('reopen_history_count', 2)->count(),
            'frequent_reopens' => $query->clone()->where('reopen_history_count', '>=', 3)->count(),
            'problem_tickets' => $query->clone()
                ->where('reopen_history_count', '>=', 3)
                ->orderBy('reopen_history_count', 'desc')
                ->take(5)
                ->get()
        ];

        // Ticket categorization
        $categories = [
            'topics' => $this->getTopCategories($query, 'topic'),
            'subtopics' => $this->getTopCategories($query, 'subtopic'),
            'tertiaryTopics' => $this->getTopCategories($query, 'tertiaryTopic')
        ];

        return [
            // Ticket counts
            'totalTickets' => $totalTickets,
            'openTickets' => $statusCounts['open'] ?? 0,
            'resolvedTickets' => $statusCounts['resolved'] ?? 0,
            'reopenPercentage' => $totalTickets > 0 ? round(($reopenedCount / $totalTickets) * 100, 2) : 0,

            // Statistics
            'reopenStats' => $reopenStats,
            'statusCounts' => $statusCounts,
            'statusColors' => Status::getStatusesWithColors(),

            // Categories
            ...$categories,

            // Recent activity
            'recentTickets' => $query->clone()->latest()->take(10)->get(),
            'frequentlyReopenedTickets' => $reopenStats['problem_tickets'],

            // User-specific stats
            'assignedTickets' => $user->assignedTickets()->count(),
            'overdueTickets' => $user->assignedTickets()
                ->where('created_at', '<', now()->subDays(2))
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),

            'activeFilters' => request()->only(['payment_channel', 'mobile_operator', 'saas_app', 'status'])
        ];
    }

    protected function getTopCategories($query, $relation, $limit = 5)
    {
        $columnMap = [
            'topic' => 'topic_id',
            'subtopic' => 'sub_topic_id',
            'tertiaryTopic' => 'tertiary_topic_id'
        ];
        $columnName = $columnMap[$relation] ?? "{$relation}_id";

        return $query->clone()
            ->with($relation)
            ->selectRaw("{$columnName}, count(*) as count")
            ->groupBy($columnName)
            ->orderBy('count', 'desc')
            ->take($limit)
            ->get()
            ->filter(fn($t) => $t->$relation);
    }
    public function landing()
    {
        return view('auth.login');
    }
}

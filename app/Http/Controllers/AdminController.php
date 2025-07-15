<?php
namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Ticket\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = user();
        if ($user->is_reporter) {
            return view('dashboard.frontend.dashboard', [
                'userTickets' => Ticket::where('user_id', $user->id)->count(),
                'openTickets' => Ticket::where('user_id', $user->id)->where('status', 'open')->count(),
                'resolvedTickets' => Ticket::where('user_id', $user->id)->where('status', 'resolved')->count(),
                'reOpenTickets' => Ticket::where('user_id', $user->id)->whereIn('status', ['reopen', 'reopened'])->count(),
            ]);
        }

        $totalTickets = Ticket::count();
        $reopenedCount = Ticket::where('status', 'reopened')->count();

        // Get all statuses with their colors
        $statuses = Status::all()->mapWithKeys(function ($status) {
            return [$status->slug => [
                'color_class' => $status->color_class,
                'text_color_class' => $status->text_color_class,
                'name' => $status->name
            ]];
        })->toArray();

        // Get status counts including all possible statuses
        $statusCounts = [];
        foreach ($statuses as $slug => $status) {
            $statusCounts[$slug] = Ticket::where('status', $slug)->count();
        }

        return view('dashboard.backend.dashboard', [
            // Ticket counts
            'totalTickets' => $totalTickets,
            'openTickets' => $statusCounts['open'] ?? 0,
            'resolvedTickets' => $statusCounts['resolved'] ?? 0,
            'reopenPercentage' => $totalTickets > 0 ? round(($reopenedCount / $totalTickets) * 100, 2) : 0,

            // Reopen statistics
            'reopenStats' => [
                'total_tickets' => $totalTickets,
                'never_reopened' => Ticket::where('reopen_history_count', 0)->count(),
                'reopened_once' => Ticket::where('reopen_history_count', 1)->count(),
                'reopened_twice' => Ticket::where('reopen_history_count', 2)->count(),
                'frequent_reopens' => Ticket::where('reopen_history_count', '>=', 3)->count(),
                'problem_tickets' => Ticket::where('reopen_history_count', '>=', 3)
                    ->orderBy('reopen_history_count', 'desc')
                    ->take(5)
                    ->get()
            ],

            // Status breakdown - now includes all statuses from the database
            'statusCounts' => $statusCounts,

            // Ticket categorization
            'topics' => Ticket::with('topic')
                ->selectRaw('topic_id, count(*) as count')
                ->groupBy('topic_id')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get(),

            'subtopics' => Ticket::with('subtopic')
                ->selectRaw('sub_topic_id, count(*) as count')
                ->groupBy('sub_topic_id')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get()
                ->filter(fn($t) => $t->subtopic),

            'tertiaryTopics' => Ticket::with('tertiaryTopic')
                ->selectRaw('tertiary_topic_id, count(*) as count')
                ->groupBy('tertiary_topic_id')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get(),

            // Recent activity
            'recentTickets' => Ticket::with(['user', 'assignedTo'])
                ->latest()
                ->take(10)
                ->get(),

            'frequentlyReopenedTickets' => Ticket::where('reopen_history_count', '>', 2)
                ->orderBy('reopen_history_count', 'desc')
                ->take(5)
                ->get(),

            // User-specific stats
            'assignedTickets' => $user->assignedTickets()->count(),
            'overdueTickets' => $user->assignedTickets()
                ->where('created_at', '<', now()->subDays(2))
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),

            // Status colors from database
            'statusColors' => $statuses
        ]);
    }

    public function landing()
    {
        return view('auth.login');
    }
}

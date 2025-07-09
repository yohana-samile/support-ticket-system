<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

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
                'openTickets' => Ticket::where('user_id', $user->id)
                    ->where('status', 'open')->count(),
                'resolvedTickets' => Ticket::where('user_id', $user->id)
                    ->where('status', 'resolved')->count(),
                'recentTickets' => Ticket::where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get()
            ]);
        } else {
            return view('dashboard.backend.dashboard', [
                'totalTickets' => Ticket::count(),
                'openTickets' => Ticket::where('status', 'open')->count(),
                'resolvedTickets' => Ticket::where('status', 'resolved')->count(),
                'avgResponseTime' => round(Ticket::avg('response_time'), 1),
                'statusCounts' => [
                    'open' => Ticket::where('status', 'open')->count(),
                    'in_progress' => Ticket::where('status', 'in_progress')->count(),
                    'resolved' => Ticket::where('status', 'resolved')->count(),
                    'closed' => Ticket::where('status', 'closed')->count(),
                ],
                'categories' => Category::withCount('tickets')
                    ->get()
                    ->map(function($category) {
                        return [
                            'name' => $category->name,
                            'count' => $category->tickets_count,
                            'color' => $category->color ?? $this->generateRandomColor(),
                            'hover_color' => $category->hover_color ?? $this->generateRandomColor(0.7)
                        ];
                    }),
                'recentTickets' => Ticket::with(['user', 'assignedTo'])
                    ->latest()
                    ->take(10)
                    ->get(),
                'assignedTickets' => $user->assignedTickets()->count(),
                'overdueTickets' => $user->assignedTickets()
                    ->where('due_date', '<', now())
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count()
            ]);
        }
    }

    private function generateRandomColor($opacity = 1)
    {
        return 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',' . $opacity . ')';
    }

    public function landing(){
        return view('auth.login');
    }
}

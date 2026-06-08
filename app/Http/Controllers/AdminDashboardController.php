<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Diploma;
use App\Models\Field;
use App\Models\UserDiplomaField;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_students' => User::where('role', 'user')->count(),
            'total_diplomas' => Diploma::count(),
            'total_fields' => Field::count(),
            'total_applications' => UserDiplomaField::count(),
            'pending_applications' => UserDiplomaField::where('status', 'pending')->count(),
            'approved_applications' => UserDiplomaField::where('status', 'approved')->count(),
        ];

        return response()->json([
            'message' => 'Admin dashboard data retrieved successfully',
            'stats' => $stats
        ]);
    }

    public function getUsers(Request $request)
    {
        $query = User::with(['diplomaFields.diploma', 'diplomaFields.field']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        // Pagination
        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 10));

        return response()->json($users);
    }



    public function updateUserRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user = User::findOrFail($userId);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Don't allow deletion of the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return response()->json([
                'message' => 'Cannot delete the last admin user'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function updateApplicationStatus(Request $request, $applicationId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $application = UserDiplomaField::findOrFail($applicationId);
        $application->status = $request->status;
        $application->save();

        return response()->json([
            'message' => 'Application status updated successfully',
            'application' => $application->load(['user', 'diploma', 'field'])
        ]);
    }

  

public function getAllEnrollments(Request $request)
{
    $query = UserDiplomaField::with(['user', 'diploma', 'field']);

    // Filter by status if provided
    if ($request->has('status')) {
        $query->where('status', $request->get('status'));
    }

    // Filter by diploma if provided
    if ($request->has('diploma_id')) {
        $query->where('diploma_id', $request->get('diploma_id'));
    }

    // Filter by field if provided
    if ($request->has('field_id')) {
        $query->where('field_id', $request->get('field_id'));
    }

    // Search by user name or email
    if ($request->has('search')) {
        $search = $request->get('search');
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $enrollments = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

    return response()->json([
        'message' => 'Enrollments retrieved successfully',
        'enrollments' => $enrollments
    ]);
}
public function getUsersByDiplomaAndField()
{
    $enrollments = UserDiplomaField::with(['user', 'diploma', 'field'])
        ->get()
        ->groupBy(['diploma.name', 'field.name']);

    $formatted = [];
    
    foreach ($enrollments as $diplomaName => $fields) {
        $formatted[$diplomaName] = [];
        foreach ($fields as $fieldName => $users) {
            $formatted[$diplomaName][$fieldName] = [
                'users' => $users->pluck('user'),
                'count' => $users->count(),
                'status_breakdown' => $users->groupBy('status')->map->count()
            ];
        }
    }

    return response()->json([
        'message' => 'Users grouped by diploma and field retrieved successfully',
        'data' => $formatted
    ]);
}



public function getEnrollmentStats()
{
    // Most popular diplomas
    $popularDiplomas = UserDiplomaField::select('diploma_id')
        ->selectRaw('COUNT(*) as enrollment_count')
        ->with('diploma')
        ->groupBy('diploma_id')
        ->orderBy('enrollment_count', 'desc')
        ->get();

    // Most popular fields
    $popularFields = UserDiplomaField::select('field_id')
        ->selectRaw('COUNT(*) as enrollment_count')
        ->with('field')
        ->groupBy('field_id')
        ->orderBy('enrollment_count', 'desc')
        ->get();

    // Enrollment status breakdown
    $statusBreakdown = UserDiplomaField::select('status')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('status')
        ->get();

    // Recent enrollments
    $recentEnrollments = UserDiplomaField::with(['user', 'diploma', 'field'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    return response()->json([
        'message' => 'Enrollment statistics retrieved successfully',
        'popular_diplomas' => $popularDiplomas,
        'popular_fields' => $popularFields,
        'status_breakdown' => $statusBreakdown,
        'recent_enrollments' => $recentEnrollments
    ]);

}

public function getUserApplications($userId)
{
    $user = User::with(['diplomaFields.diploma', 'diplomaFields.field'])
               ->findOrFail($userId);

    // Group applications by status
    $applicationsByStatus = $user->diplomaFields->groupBy('status');

    return response()->json([
        'user' => $user,
        'applications' => $user->diplomaFields,
        'applications_by_status' => $applicationsByStatus,
        'total_applications' => $user->diplomaFields->count(),
        'approved_count' => $user->diplomaFields->where('status', 'approved')->count(),
        'pending_count' => $user->diplomaFields->where('status', 'pending')->count(),
        'rejected_count' => $user->diplomaFields->where('status', 'rejected')->count()
    ]);
}

}

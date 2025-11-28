<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicMemberListController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('companies')
            ->whereNotNull('membership_card_number')
            ->whereNotNull('membership_card_expires_at')
            ->where('membership_card_expires_at', '>=', now());

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('companies', function($companyQuery) use ($search) {
                      $companyQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                                   ->orWhere('address', 'like', "%{$search}%");
                  });
            });
        }

        $members = $query->orderBy('name')->paginate(20);

        return view('public.member-list', compact('members'));
    }
}

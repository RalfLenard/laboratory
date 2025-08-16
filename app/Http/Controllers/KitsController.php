<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KitsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $kitQuery = Kit::orderBy('created_at', 'desc');
    
        if ($search) {
            $kitQuery->where(function ($query) use ($search) {
                $query->where('kit_name', 'like', "%{$search}%")
                    ->orWhere('kit_lot_no', 'like', "%{$search}%")
                    ->orWhere('kit_types', 'like', "%{$search}%");
            });
        }
    
        $kits = $kitQuery->paginate(50)->withQueryString();
    
        return Inertia::render('Kits', [
            'kits' => $kits->through(function ($kit) {
                return [
                    'id' => $kit->id,
                    'kit_name' => $kit->kit_name,
                    'kit_types' => $kit->kit_types,
                    'kit_lot_no' => $kit->kit_lot_no,
                    'kit_expiration_date' => Carbon::parse($kit->kit_expiration_date)->format('F d, Y'),
                ];
            }),
            'filters' => [
                'search' => $search,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kit_name' => 'required|string|max:255',
            'kit_types' => 'nullable|string|max:255',
            'kit_lot_no' => 'nullable|string|max:255',
            'kit_expiration_date' => 'nullable|date',
        ]);

        Kit::create($validated);

        return redirect()->route('kits.index')->with('success', 'Kit created successfully.');
    }

    public function update(Request $request, $id)
    {
        $kit = Kit::findOrFail($id);

        $validated = $request->validate([
            'kit_name' => 'required|string|max:255',
            'kit_types' => 'nullable|string|max:255',
            'kit_lot_no' => 'nullable|string|max:255',
            'kit_expiration_date' => 'nullable|date',
        ]);

        $kit->update($validated);

        return redirect()->route('kits.index')->with('success', 'Kit updated successfully.');
    }

    public function destroy($id)
    {
        $kit = Kit::findOrFail($id);
        $kit->delete();

        return redirect()->route('kits.index')->with('success', 'Kit deleted successfully.');
    }
}

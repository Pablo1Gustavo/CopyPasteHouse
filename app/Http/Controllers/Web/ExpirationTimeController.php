<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ExpirationTimeService;
use Illuminate\Http\Request;

class ExpirationTimeController extends Controller
{
    public function __construct(
        private ExpirationTimeService $expirationTimeService
    ) {}

    /**
     * Display a listing of expiration times
     */
    public function index()
    {
        $expirationTimes = $this->expirationTimeService->list();
        return view('admin.expiration-times.index', compact('expirationTimes'));
    }

    /**
     * Show the form for creating a new expiration time
     */
    public function create()
    {
        return view('admin.expiration-times.create');
    }

    /**
     * Store a newly created expiration time
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50|unique:expiration_times,label',
            'minutes' => 'required|integer|min:1|unique:expiration_times,minutes',
        ]);

        $this->expirationTimeService->create($validated);

        return redirect()->route('expiration-times.index')
            ->with('success', 'Expiration time created successfully');
    }

    /**
     * Show the form for editing the specified expiration time
     */
    public function edit(string $expiration_time)
    {
        $expirationTime = $this->expirationTimeService->show($expiration_time);

        if (!$expirationTime) {
            return redirect()->route('expiration-times.index')
                ->with('error', 'Expiration time not found');
        }

        return view('admin.expiration-times.edit', compact('expirationTime'));
    }

    /**
     * Update the specified expiration time
     */
    public function update(Request $request, string $expiration_time)
    {
        $expirationTime = $this->expirationTimeService->show($expiration_time);

        if (!$expirationTime) {
            return redirect()->route('expiration-times.index')
                ->with('error', 'Expiration time not found');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50|unique:expiration_times,label,' . $expiration_time,
            'minutes' => 'required|integer|min:1|unique:expiration_times,minutes,' . $expiration_time,
        ]);

        $this->expirationTimeService->edit($expirationTime, $validated);

        return redirect()->route('expiration-times.index')
            ->with('success', 'Expiration time updated successfully');
    }

    /**
     * Remove the specified expiration time
     */
    public function destroy(string $expiration_time)
    {
        $result = $this->expirationTimeService->delete($expiration_time);

        if (!$result) {
            return redirect()->route('expiration-times.index')
                ->with('error', 'Expiration time not found or cannot be deleted');
        }

        return redirect()->route('expiration-times.index')
            ->with('success', 'Expiration time deleted successfully');
    }
}

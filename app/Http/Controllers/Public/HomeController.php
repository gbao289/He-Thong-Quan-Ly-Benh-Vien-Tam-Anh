<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the public hospital landing page.
     */
    public function index(): View
    {
        $specialties = Specialty::where('is_active', true)->withCount('doctors')->take(6)->get();
        $doctors = Doctor::with(['user', 'specialty'])->where('is_available', true)->take(4)->get();

        return view('public.home', compact('specialties', 'doctors'));
    }
}

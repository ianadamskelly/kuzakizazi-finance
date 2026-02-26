<?php
// File: app/Http/View/Composers/NavigationComposer.php
// NEW FILE: This class will provide data to our navigation view.
namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Campus;

class NavigationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        $campuses = Campus::all();
        
        // Provide a default selected campus if none is in the session
        $selectedCampusId = session('selected_campus_id', $user->employee->campus_id ?? 'all');

        $view->with([
            'campuses' => $campuses,
            'selectedCampusId' => $selectedCampusId,
        ]);
    }
}

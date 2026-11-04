<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modules;
class ModuleController extends Controller
{
    

    public function index()
    {
        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

        $excludedModuleNames = [];

        // If project type is Physio (3), exclude hospital/dental specific modules
        if ($currentProjectTypeId === 3) {
            $excludedModuleNames = [
                'Soap', 
                'Therapy', 
                'Assigned Therapy', 
                'Pathology', 
                'Pathology Reports', 
                'Radiology Tests', 
                'Radiology Reports', 
                'Opd Visits', 
                'OT', 
                'Ipd Admit', 
                'Discharge', 
                'Diagnostic Services'
            ];
        }

        if (!empty($excludedModuleNames)) {
            $modules = Modules::whereNotIn('name', $excludedModuleNames)->get();
        } else {
            $modules = Modules::all(); 
        }

        // Only return modules permitted for current tenant
        if (config('tenant.enabled') && function_exists('tenant_has_module')) {
            $modules = $modules->filter(function ($mod) {
                return tenant_has_module($mod->name);
            })->values();
        }

        return response()->json($modules);  
    }
    
    
}

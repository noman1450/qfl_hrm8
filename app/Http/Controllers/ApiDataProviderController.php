<?php

namespace App\Http\Controllers;

use App\Services\LeaveManagement;
use App\Services\EmployeeManagement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiDataProviderController extends Controller
{
    protected $leaveManagement;

    protected $employeeManagement;

    // public function __construct(
    //     LeaveManagement $leaveManagement,
    //     EmployeeManagement $employeeManagement
    // ) {
    //     $this->leaveManagement = $leaveManagement;
    //     $this->employeeManagement = $employeeManagement;
    // }

    /**
     * all leave management functions in 'App\Services\LeaveManagement'
     */
    public function leaveManagement(Request $request)
    {
        return $this->handleRequest(
            $request,
            LeaveManagement::class,
            $this->leaveManagement
        );
    }

    /**
     * all leave management functions in 'App\Services\EmployeeManagement'
     */
    public function employeeManagement(Request $request)
    {
        return $this->handleRequest(
            $request,
            EmployeeManagement::class,
            $this->employeeManagement
        );
    }

    private function handleRequest($request, $class, $instance)
    {
        if(! method_exists($instance, $request->callFunction)) {
            return response()->json([
                sprintf('Opps! method %s does not exists in %s', $request->callFunction, $class)
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json(
            $instance->{$request->callFunction}($request)
        );
    }
}

<?php

namespace WebkernelTestpackage\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;
use WebkernelTestpackage\Constants\Application;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Package index page
     */
    public function index(Request $request): View
    {
        return view('webkernel-testpackage::index', [
            'package_name' => Application::getName(),
            'version' => Application::getVersion(),
            'description' => Application::getDescription(),
        ]);
    }

    /**
     * Package about page
     */
    public function about(Request $request): View
    {
        return view('webkernel-testpackage::about', [
            'package_name' => Application::getName(),
            'version' => Application::getVersion(),
            'description' => Application::getDescription(),
        ]);
    }
}

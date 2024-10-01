<?php

use App\Http\Controllers\ProfileController;
use App\Http\Requests\ShowDashboardRequest;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function (ShowDashboardRequest $request) {
    $brands = [
        'Betway' => [
            'Betway 1' => [
                'Dept 1' => [
                    ['id' => 1, 'name' => 'John'],
                    ['id' => 2, 'name' => 'Alice'],
                ],
                'Dept 2' => [
                    ['id' => 3, 'name' => 'Brian'],
                    ['id' => 4, 'name' => 'Jason'],
                ]
            ],
            'Betway 2' => [
                'Dept 3' => [
                    ['id' => 5, 'name' => 'Michael'],
                    ['id' => 6, 'name' => 'Linda'],
                    ['id' => 7, 'name' => 'Sharon'],
                    ['id' => 8, 'name' => 'Laura'],
                ],
            ],
            'Both' => [
                'Dept 4' => [
                    ['id' => 13, 'name' => 'Margaret'],
                    ['id' => 14, 'name' => 'Carol'],
                    ['id' => 15, 'name' => 'Stephen'],
                    ['id' => 16, 'name' => 'Justin'],
                ],
            ]
        ],
        'Spin' => [
            'Spin 1' => [
                'Dept 5' => [
                    ['id' => 9, 'name' => 'Charles'],
                    ['id' => 10, 'name' => 'Kimberly'],
                ],
                'Dept 6' => [
                    ['id' => 11, 'name' => 'Jacob'],
                    ['id' => 12, 'name' => 'Shirley'],
                ]
            ],
            'Both' => [
                'Dept 7' => [
                    ['id' => 17, 'name' => 'Paul'],
                    ['id' => 18, 'name' => 'Kevin'],
                    ['id' => 19, 'name' => 'Nicole'],
                    ['id' => 20, 'name' => 'Anna'],
                ],
            ]
        ],
    ];

    $companies = collect($brands)->flatMap(fn(array $companies) => array_keys($companies));

    $employees = collect($brands)
        ->flatMap(fn(array $companies, string $brand) =>
            collect($companies)->flatMap(fn(array $departments, string $company) =>
                collect($departments)->flatMap(fn(array $employees, $department) =>
                    collect($employees)->map(fn(array $employee) => [...$employee, 'brand' => $brand, 'company' => $company, 'department' => $department])
                )
            )
        );

    $brand = array_key_exists($request->input('brand'), $brands) ? $request->input('brand') : null;

    $company = $brand === null ? null :
        (array_key_exists($request->input('company'), $brands[$brand]) ? $request->input('company') : null);

    $departments = $company === null ? [] :
        (array_intersect($request->input('department', []), array_keys($brands[$brand][$company])) ? $request->input('department', []) : []);

    $page = $request->integer('page', 1);
    $limit = $request->integer('limit', 2);

    $records = $employees
        ->filter(fn ($employee) => $brand === null || $employee['brand'] === $brand)
        ->filter(fn ($employee) => $company === null || $employee['company'] === $company)
        ->filter(fn ($employee) => $departments === [] || in_array($employee['department'], $departments))
        ->values();

    $total = $records->count();

    return Inertia::render('Dashboard', [
        'filter' => [
            'options' => [
                'brand' => array_keys($brands),
                'company' => $brand === null ? $companies : array_keys($brands[$brand]),
                'department' => $company === null ? [] : array_keys($brands[$brand][$company]),
            ],
            'selected' => [
                'brand' => $brand,
                'company' => $company,
                'department' => $departments,
            ],
        ],
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total/ $limit),
            'total' => $total,
        ],
        'records' => $records->forPage($page, $limit),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\dashboardAdminController;
use App\Http\Controllers\dashboardManagerController;
use App\Http\Controllers\dashboardKasirController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardHRController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UomsController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MasterproductController;
use App\Http\Controllers\PayrollEmailController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\EmployeeImportController;
use App\Http\Controllers\PayrollsController;
use App\Http\Controllers\taxstatusController;
use App\Http\Controllers\UserprofileController;
use App\Http\Controllers\StatusproductController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BanksController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\VendorgroupController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\DashboardBuyer;
use App\Http\Controllers\DashboardHeadbuyer;
use App\Http\Controllers\DepartController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'check.role:ManagerStore'])->group(function () {


    Route::group(['middleware' => ['permission.active:dashboardManager']], function () {

        Route::get('/dashboardManager', [dashboardManagerController::class, 'index'])->name('pages.dashboardManager');

    });

});

Route::middleware(['auth', 'check.role:Kasir'])->group(function () {
    Route::group(['middleware' => ['permission:permission.active:dashboardKasir']], function () {

    Route::get('/dashboardKasir', [dashboardKasirController::class, 'index'])->name('pages.dashboardKasir');
});
});
Route::middleware(['auth', 'check.role:Admin,HeadHR,HR,Buyer,HeadBuyer'])->group(function () {
    Route::get('/feature-profile', function () {
        return view('pages.feature-profile', ['type_menu' => 'features']);
    });
    Route::put('/feature-profile/update', [UserprofileController::class, 'updatePassword'])->name('feature-profile.update');
    Route::put('/feature-profile', [UserprofileController::class, 'index'])->name('feature-profile');

    Route::match(['GET', 'POST'], '/logout', [LoginController::class, 'destroy'])
        ->name('logout');
});

Route::middleware(['auth', 'check.role:Admin'])->group(function () {

    Route::group(['middleware' => ['permission.active:dashboardAdmin']], function () {

        Route::get('/dashboardAdmin', [DashboardAdminController::class, 'index'])
            ->name('pages.dashboardAdmin');

        Route::get('/dashboardAdmin/edit/{hashedId}', [dashboardAdminController::class, 'edit'])->name('dashboardAdmin.edit');
        Route::get('/dashboardAdmin/show/{hashedId}', [dashboardAdminController::class, 'show'])->name('dashboardAdmin.show');
        Route::put('/dashboardAdmin/{hashedId}', [dashboardAdminController::class, 'update'])->name('dashboardAdmin.update');
        Route::get('/users/users', [dashboardAdminController::class, 'getUsers'])->name('users.users');
    });
    Route::group(['middleware' => ['permission:permission.active:manageRolesPermissions']], function () {
        Route::get('/roles', [RoleController::class, 'index'])
            ->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/edit/{hashedId}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{hashedId}', [RoleController::class, 'update'])->name('roles.update');
        Route::get('/role/role', [RoleController::class, 'getRoles'])->name('role.role');
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->name('permissions.index');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/edit/{hashedId}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{hashedId}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::get('/permissions/permissions', [PermissionController::class, 'getPermissions'])->name('permissions.permissions');
    });
});
Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['permission:permission.active:manageActivity']], function () {
        Route::get('/Activity', [ActivityController::class, 'index'])->name('pages.Activity');
        Route::get('/Activity/show/{hashedId}', [ActivityController::class, 'show'])->name('Activity.show');
        Route::get('/activity/activity', [ActivityController::class, 'getActivity'])->name('activity.activity');
        Route::get('/activity1/activity1', [ActivityController::class, 'getActivity1'])->name('activity1.activity1');
    });
});

Route::middleware(['auth', 'check.role:HeadBuyer'])->group(function () {

    Route::group(['middleware' => ['permission.active:dashboardHeadBuyer']], function () {
        Route::get('/dashboardHeadbuyer', [DashboardHeadbuyer::class, 'index'])
            ->name('pages.dashboardHeadbuyer');
    });
});

Route::middleware(['auth', 'check.role:Buyer'])->group(function () {

    Route::group(['middleware' => ['permission:permission.active:dashboardBuyer']], function () {

        Route::get('/dashboardBuyer', [DashboardBuyer::class, 'index'])
            ->name('pages.dashboardBuyer');
    });
});
Route::middleware(['auth', 'check.role:HR'])->group(function () {

Route::group(['middleware' => ['permission.active:dashboardHR']], function () {
    Route::get('/dashboardHR', [DashboardHRController::class, 'index'])
        ->name('pages.dashboardHR');
});
});


Route::middleware(['auth'])->group(function () {
Route::group(['middleware' => ['permission:permission.active:ManageEmployee']], function () {
    Route::get('/Employee', [EmployeeController::class, 'index'])
        ->name('pages.Employee');
    Route::get('Employee/create', [EmployeeController::class, 'create'])->name('Employee.create');
    Route::post('/Employee', [EmployeeController::class, 'store'])->name('Employee.store');
    Route::get('/Employee/edit/{hashedId}', [EmployeeController::class, 'edit'])->name('Employee.edit');
    Route::get('/Employee/show/{hashedId}', [EmployeeController::class, 'show'])->name('Employee.show');
    Route::put('/Employee/{hashedId}', [EmployeeController::class, 'update'])->name('Employee.update');
    Route::get('/employees/employees', [EmployeeController::class, 'getEmployees'])->name('employees.employees');
    Route::post('/employees/transfer-all-to-payroll', [EmployeeController::class, 'transferAllToPayroll'])->name('employees.transferAllToPayroll');
    // employeeall
    Route::get('/Employeeall', [EmployeeController::class, 'indexall'])
        ->name('pages.Employeeall');
    Route::get('/employeesall/employeesall', [EmployeeController::class, 'getEmployeesall'])->name('employeesall.employeesall');
    Route::get('/Import', [EmployeeImportController::class, 'index'])
        ->name('pages.Import');
    Route::post('/Import', [EmployeeImportController::class, 'import'])->name('Import.employee');
    // userimport
    Route::get('/Importuser', [EmployeeImportController::class, 'indexuser'])
        ->name('pages.Importuser');
    Route::post('/Importuser', [EmployeeImportController::class, 'importuser'])->name('Importuser.user');
    // payrolls
    Route::get('/Importpayroll', [EmployeeImportController::class, 'indexpayrolls'])
        ->name('pages.Importpayroll');
    Route::post('/Importpayroll', [EmployeeImportController::class, 'importpayroll'])->name('Importpayroll.user');
    // });
    Route::post('/payrolls/generate-all', [PayrollsController::class, 'generateAll'])->name('payrolls.generateAll');

});
});

Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission:permission.active:ManageEmployee']], function () {
    //payrolls
    Route::get('/Payrolls', [PayrollsController::class, 'index'])
        ->name('pages.Payrolls');
    Route::get('/Payrolls/edit/{hashedId}', [PayrollsController::class, 'edit'])->name('Payrolls.edit');
    Route::put('/Payrolls/{hashedId}', [PayrollsController::class, 'update'])->name('Payrolls.update');
    Route::get('/payrolls/payrolls', [PayrollsController::class, 'getPayrolls'])->name('payrolls.payrolls');
    Route::get('/Payrolls/show/{hashedId}', [PayrollsController::class, 'show'])->name('Payrolls.show');
    Route::delete('/payrolls/delete', [PayrollsController::class, 'deletepayrolls'])->name('payrolls.delete');
    Route::get('/email', [PayrollEmailController::class, 'index'])->name('payroll.email.index');
    Route::post('/email/send', [PayrollEmailController::class, 'send'])->name('payroll.email.send');
    Route::get('/email/preview/{payroll}', [PayrollEmailController::class, 'preview'])->name('payroll.email.preview');
    Route::get('/payrolls/{hashedId}/generate', [PayrollsController::class, 'generate'])->name('payrolls.generate');
});
});

Route::middleware(['auth'])->group(function () {

// Position    
Route::group(['middleware' => ['permission:permission.active:ManagePositions']], function () {

    Route::get('/Position', [PositionController::class, 'index'])
        ->name('pages.Position');
    Route::get('Position/create', [PositionController::class, 'create'])->name('Position.create');
    Route::post('/Position', [PositionController::class, 'store'])->name('Position.store');
    Route::get('/Position/edit/{hashedId}', [PositionController::class, 'edit'])->name('Position.edit');
    Route::put('/Position/{hashedId}', [PositionController::class, 'update'])->name('Position.update');
    Route::get('/positions/positions', [PositionController::class, 'getPositions'])->name('positions.positions');
});
});


Route::middleware(['auth'])->group(function () {

// Department    
Route::group(['middleware' => ['permission:permission.active:ManageDepartments']], function () {

    Route::get('/Department', [DepartmentController::class, 'index'])
        ->name('pages.Department');
    Route::get('Department/create', [DepartmentController::class, 'create'])->name('Department.create');
    Route::post('/Department', [DepartmentController::class, 'store'])->name('Department.store');
    Route::get('/Department/edit/{hashedId}', [DepartmentController::class, 'edit'])->name('Department.edit');
    Route::put('/Department/{hashedId}', [DepartmentController::class, 'update'])->name('Department.update');
    Route::get('/departments/departments', [DepartmentController::class, 'getDepartments'])->name('departments.departments');
});
});

Route::middleware(['auth'])->group(function () {

// store  
Route::group(['middleware' => ['permission:permission.active:ManageStores']], function () {

    Route::get('/Store', [StoreController::class, 'index'])
        ->name('pages.Store');
    Route::get('Store/create', [StoreController::class, 'create'])->name('Store.create');
    Route::post('/Store', [StoreController::class, 'store'])->name('Store.store');
    Route::get('/Store/edit/{hashedId}', [StoreController::class, 'edit'])->name('Store.edit');
    Route::put('/Store/{hashedId}', [StoreController::class, 'update'])->name('Store.update');
    Route::get('/stores/stores', [StoreController::class, 'getStores'])->name('stores.stores');
});
});

Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission:permission.active:ManageBanks']], function () {

    Route::get('/Banks', [BanksController::class, 'index'])
        ->name('pages.Banks');
    Route::get('Banks/create', [BanksController::class, 'create'])->name('Banks.create');
    Route::post('/Banks', [BanksController::class, 'store'])->name('Banks.store');
    Route::get('/Banks/edit/{hashedId}', [BanksController::class, 'edit'])->name('Banks.edit');
    Route::put('/Banks/{hashedId}', [BanksController::class, 'update'])->name('Banks.update');
    Route::get('/banks/banks', [BanksController::class, 'getBanks'])->name('banks.banks');
});
});

// uoms
Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission.active:ManageUoms']], function () {

    Route::get('/Uoms', [UomsController::class, 'index'])
        ->name('pages.Uoms');
    Route::get('Uoms/create', [UomsController::class, 'create'])->name('Uoms.create');
    Route::post('/Uoms', [UomsController::class, 'store'])->name('Uoms.store');
    Route::get('/Uoms/edit/{hashedId}', [UomsController::class, 'edit'])->name('Uoms.edit');
    Route::put('/Uoms/{hashedId}', [UomsController::class, 'update'])->name('Uoms.update');
    Route::get('/uoms/uoms', [UomsController::class, 'getUoms'])->name('uoms.uoms');
});
});

Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission.active:ManageVendorgroups']], function () {

    Route::get('/Vendorgroup', [VendorgroupController::class, 'index'])
        ->name('pages.Vendorgroup');
    Route::get('Vendorgroup/create', [VendorgroupController::class, 'create'])->name('Vendorgroup.create');
    Route::post('/Vendorgroup', [VendorgroupController::class, 'store'])->name('Vendorgroup.store');
    Route::get('/Vendorgroup/edit/{hashedId}', [VendorgroupController::class, 'edit'])->name('Vendorgroup.edit');
    Route::get('/Vendorgroup/detail/{hashedId}', [VendorController::class, 'detail'])->name('Vendorgroup.detail');
    Route::put('/Vendorgroup/{hashedId}', [VendorgroupController::class, 'update'])->name('Vendorgroup.update');
    Route::get('/vendorgroups/vendorgroups', [VendorgroupController::class, 'getVendorgroups'])->name('vendorgroups.vendorgroups');
    Route::get('/vendors/vendors/{hashedId}', [VendorController::class, 'getVendors'])->name('vendors.vendors');
    Route::get('vendorgroup/logs/data', [VendorgroupController::class, 'logs'])->name('vendorgroup.logs');
    Route::get('vendor/logs/data', [VendorgroupController::class, 'logs'])->name('vendor.logs');
    Route::get('/Importvendorgroup', [VendorgroupController::class, 'indeximportvendorgroup'])
        ->name('pages.Importvendorgroup');
    Route::post('/Importvendorgroup', [VendorgroupController::class, 'importvendorgroup'])->name('Importvendorgroup.vendorgroup');
    Route::get('/Vendorgroup/downloadvendorgroup/{filename}', [VendorGroupController::class, 'downloadvendorgroup'])->name('Vendorgroup.downloadvendorgroup');
});
});

Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission.active:ManageVendor']], function () {

    Route::get('/Vendor/edit/{hashedId}', [VendorController::class, 'edit'])->name('Vendor.edit');
    Route::get('Vendor/create/{hashedId}', [VendorController::class, 'create'])->name('Vendor.create');
    Route::post('/Vendor', [VendorController::class, 'store'])->name('Vendor.store');
    Route::get('vendor/vendor/data', [VendorController::class, 'logs'])->name('vendor.vendor');
    Route::get('/vendor/filters/{hashedId}', [VendorController::class, 'getVendorFilters'])->name('vendor.filters');
    Route::get('/Importvendor', [VendorController::class, 'indeximportvendor'])
        ->name('pages.Importvendor');
    Route::post('/Importvendor', [VendorController::class, 'importvendor'])->name('Importvendor.vendor');
    Route::get('/Vendor/downloadvendor/{filename}', [VendorController::class, 'downloadvendor'])->name('Vendor.downloadvendor');

});
});

Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission.active:ManageDepart']], function () {

    Route::get('/Depart', [DepartController::class, 'index'])
        ->name('pages.Depart');
    // Route::get('Uoms/create', [UomsController::class, 'create'])->name('Uoms.create');
    // Route::post('/Uoms', [UomsController::class, 'store'])->name('Uoms.store');
    Route::get('/Depart/edit/{hashedId}', [DepartController::class, 'edit'])->name('Depart.edit');
    // Route::put('/Uoms/{hashedId}', [UomsController::class, 'update'])->name('Uoms.update');
    Route::get('/depart/depart', [DepartController::class, 'getDeparts'])->name('depart.depart');
});
});

Route::middleware(['auth'])->group(function () {

// Brands
Route::group(['middleware' => ['permission.active:ManageBrands']], function () {

    Route::get('/Brands', [BrandsController::class, 'index'])
        ->name('pages.Brands');
    Route::get('Brands/create', [BrandsController::class, 'create'])->name('Brands.create');
    Route::post('/Brands', [BrandsController::class, 'store'])->name('Brands.store');
    Route::get('/Brands/edit/{hashedId}', [BrandsController::class, 'edit'])->name('Brands.edit');
    Route::put('/Brands/{hashedId}', [BrandsController::class, 'update'])->name('Brands.update');
    Route::get('/brands/brands', [BrandsController::class, 'getBrands'])->name('brands.brands');
});
});

Route::middleware(['auth'])->group(function () {

// Categories
Route::group(['middleware' => ['permission.active:ManageCategories']], function () {

    Route::get('/Categories', [CategoriesController::class, 'index'])
        ->name('pages.Categories');
    Route::get('Categories/create', [CategoriesController::class, 'create'])->name('Categories.create');
    Route::post('/Categories', [CategoriesController::class, 'store'])->name('Categories.store');
    // Route::get('/Categories/edit/{hashedId}', [CategoriesController::class, 'edit'])->name('Categories.edit');
// Route::put('/Categories/{hashedId}', [CategoriesController::class, 'update'])->name('Categories.update');
    Route::get('/categories/categories', [CategoriesController::class, 'getCategories'])->name('categories.categories');
    Route::get('categories/tree', [CategoriesController::class, 'getCategoryTree'])->name('categories.tree');
});
});

Route::middleware(['auth'])->group(function () {

// Tax status
Route::group(['middleware' => ['permission.active:ManageTaxstatus']], function () {

    Route::get('/Taxstatus', [TaxstatusController::class, 'index'])
        ->name('pages.Taxstatus');
    Route::get('Taxstatus/create', [TaxstatusController::class, 'create'])->name('Taxstatus.create');
    Route::post('/Taxstatus', [TaxstatusController::class, 'store'])->name('Taxstatus.store');
    Route::get('/Taxstatus/edit/{hashedId}', [TaxstatusController::class, 'edit'])->name('Taxstatus.edit');
    Route::put('/Taxstatus/{hashedId}', [TaxstatusController::class, 'update'])->name('Taxstatus.update');
    Route::get('/taxstatus/taxstatus', [TaxstatusController::class, 'getTaxstatuses'])->name('taxstatus.taxstatus');
});
});


Route::middleware(['auth'])->group(function () {

// Status Product
Route::group(['middleware' => ['permission.active:ManageStatusproduct']], function () {
  
    Route::get('/Statusproduct', [StatusproductController::class, 'index'])
        ->name('pages.Statusproduct');
    Route::get('Statusproduct/create', [StatusproductController::class, 'create'])->name('Statusproduct.create');
    Route::post('/Statusproduct', [StatusproductController::class, 'store'])->name('Statusproduct.store');
    Route::get('/Statusproduct/edit/{hashedId}', [StatusproductController::class, 'edit'])->name('Statusproduct.edit');
    Route::put('/Statusproduct/{hashedId}', [StatusproductController::class, 'update'])->name('Statusproduct.update');
    Route::get('/statusproduct/statusproduct', [StatusproductController::class, 'getStatusproducts'])->name('statusproduct.statusproduct');
});
});

Route::middleware(['auth'])->group(function () {

// Status Product
Route::group(['middleware' => ['permission.active:ManageMasterproducts']], function () {

    Route::get('/Masterproducts', [MasterproductController::class, 'index'])
        ->name('pages.Masterproducts');
    Route::get('Masterproducts/create', [MasterproductController::class, 'create'])->name('Masterproducts.create');
    Route::post('/Masterproducts', [MasterproductController::class, 'store'])->name('Masterproducts.store');
    Route::get('/Masterproducts/edit/{hashedId}', [MasterproductController::class, 'edit'])->name('Masterproducts.edit');
    Route::put('/Masterproducts/{hashedId}', [MasterproductController::class, 'update'])->name('Masterproducts.update');
    Route::get('/masterproducts/masterproducts', [MasterproductController::class, 'getMasterproducts'])->name('masterproducts.masterproducts');
});
});

Route::middleware(['auth'])->group(function () {

Route::group(['middleware' => ['permission.active:ManageCompanies']], function () {
    Route::get('/Company', [CompanyController::class, 'index'])
        ->name('pages.Company');
    Route::get('Company/create', [CompanyController::class, 'create'])->name('Company.create');
    Route::post('/Company', [CompanyController::class, 'store'])->name('Company.store');
    Route::get('/Company/edit/{hashedId}', [CompanyController::class, 'edit'])->name('Company.edit');
    Route::put('/Company/{hashedId}', [CompanyController::class, 'update'])->name('Company.update');
    Route::get('/company/company', [CompanyController::class, 'getCompanys'])->name('company.company');
});
});



Route::group(['middleware' => 'guest'], function () {
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/session', [LoginController::class, 'store'])->name('session');
        Route::get('/', [LoginController::class, 'index'])->name('login');
        Route::get('/portofolio', function () {
            return view('pages.portofolio');
        });
    });
});


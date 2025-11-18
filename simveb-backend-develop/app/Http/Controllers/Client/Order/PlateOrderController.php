<?php

namespace App\Http\Controllers\Client\Order;

use App\Enums\ProfileTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plate\PlateOrderRequest;
use App\Http\Requests\Plate\RejectPlateOrderRequest;
use App\Http\Requests\Plate\ConfirmPlateOrderRequest;
use App\Http\Requests\Plate\PayPlateOrderRequest;
use App\Http\Requests\Plate\ValidatePlateOrderRequest;
use App\Models\Space\Space;
use App\Models\Auth\ProfileType;
use App\Models\Order\Invoice;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateOrder;
use App\Models\Plate\PlateShape;
use App\Repositories\Plate\PlateOrderRepository;
use App\Services\InvoiceService;

class PlateOrderController extends Controller
{
    public function __construct(private readonly PlateOrderRepository $plateOrderRepository)
    {
        $this->authorizeResource(PlateOrder::class);
        $this->middleware('permission:validate-plate-order')->only(['confirmationFileFormat', 'confirmOrder', 'rejectOrder']);
    }

    public function index()
    {
        return response($this->plateOrderRepository->myOrders(true));
    }

    public function orderRequests()
    {
        return response($this->plateOrderRepository->orderRequests(true, PlateOrder::relations()));
    }

    public function show(PlateOrder $plateOrder)
    {
        return response($plateOrder->load(PlateOrder::relations())->append('order_details'));
    }

    public function create()
    {
        $data = [
            'colors' => PlateColor::select(['id', 'label', 'color_code', 'text_color', 'cost'])->get(),
            'shapes' => PlateShape::select(['id', 'name', 'cost'])->get(),
        ];

        if (getOnlineProfile()->type->code == ProfileTypesEnum::anatt->name) {
            $data['sellers'] = Space::where('profile_type_id', ProfileType::where('code', ProfileTypesEnum::approved->name)->first()->id)->select(['id', 'profile_type_id', 'institution_id'])->with(['institution:id,name,idu,telephone']) ->get();
        }
        return response($data);
    }

    public function store(PlateOrderRequest $request)
    {
        return response($this->plateOrderRepository->store($request->validated()));
    }

    public function confirmationFileFormat()
    {
        $path = 'format-import/format_order_confirmation.xlsx';

        return response(file_exists(public_path($path)) ? asset($path) : '');
    }

    public function confirmOrder(ConfirmPlateOrderRequest $request)
    {
        [$success, $result] = $this->plateOrderRepository->confirmOrder($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function rejectOrder(RejectPlateOrderRequest $request)
    {
        [$success, $result] = $this->plateOrderRepository->rejectOrder($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function payOrder(PayPlateOrderRequest $request)
    {
        return response($this->plateOrderRepository->payOrder($request->validated()));
    }

    public function validateOrder(ValidatePlateOrderRequest $request)
    {
        return response($this->plateOrderRepository->validateOrder($request->validated()));
    }

    public function invoices()
    {
        return $this->plateOrderRepository->invoices(true);
    }

    public function generateInvoiceFile(Invoice $invoice)
    {
        return (new InvoiceService)->generate($invoice, true);
    }
}

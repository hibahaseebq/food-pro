<?php

namespace App\Http\Controllers\Customer;

use App\DTO\OrderDTO;
use App\Models\Cart\CartItem;
use App\Models\Orders\Order;
use App\Models\Restaurant\Branch;
use App\Models\Restaurant\Restaurant;
use App\Http\Resources\OrderResource;
use App\Services\Customer\CustomerOrderService;
use App\Services\Customer\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Cart\AddToCartServiceV2;

class OrderController extends CustomerController
{

    public function orderHistory()
    {
        $orders = $this->customerService->getOrderHistory();

        return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Order history retrieved successfully', $orders);
    }

    public function activeOrder()
    {
        $activeOrder = $this->customerService->getActiveOrder();

        if ($activeOrder) {
            return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Active order retrieved successfully', new OrderResource($activeOrder));
        }

        return Helpers::sendFailureResponse(Response::HTTP_NOT_FOUND, 'No active order found');
    }

    public function viewOrderDetails($orderId)
    {
        $orderDetails = $this->customerService->getOrderDetails($orderId);

        return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Order details retrieved successfully', $orderDetails);
    }

    public function checkout(CustomerOrderService $customerOrderService)
    {
        $data = $customerOrderService->checkout();
        return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Order summary', $data);
    }
    public function createOrder(CustomerOrderService $customerOrderService)
    {
        $data = $customerOrderService->createOrder();
        if ($data) {
            return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Order created successfully', $data);
        } else {
            return Helpers::sendFailureResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Could not process your order');
        }

    }
    public function cancelOrder()
    {

    }

}

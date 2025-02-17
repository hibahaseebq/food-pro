<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\ChangePasswordRequest;
use App\Services\Customer\CustomerProfileService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Customer\ProfileResource;
use App\Http\Requests\CustomerRequests\UpdateProfileRequest;
use App\Http\Requests\CustomerRequests\UpdateCustomerAddressRequest;
use App\Models\User\User;
use App\DTO\User\CustomerDTO;


class ProfileController extends Controller
{

    protected $customerProfileService;
    public function __construct(CustomerProfileService $customerProfileService)
    {
        $this->customerProfileService = $customerProfileService;
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->customerProfileService->changePassword($request);
        if (!$result) {
            return Helpers::sendFailureResponse(Response::HTTP_BAD_REQUEST);

        } else {
            return Helpers::sendSuccessResponse(Response::HTTP_OK, 'changed password successfully');
        }
    }


    public function editProfile(UpdateProfileRequest $request)
    {

        $updatedUser = $this->customerProfileService->updateProfile($request->all());


        return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Profile updated successfully', $updatedUser);
    }

    public function updateCustomerAddress(UpdateCustomerAddressRequest $request)
    {
        $customerId = $request->get('customer_id');
        $isDefaultAddress = $request->routeIs('updateCustomerDefaultAddress');

        $data = [
            'user_id' => $customerId,
            'address' => $request->address ?? null,
            'delivery_address' => $isDefaultAddress ? null : ($request->delivery_address ?? null),
            'favorites' => $request->favorites ?? null
        ];

        // Ensure either address or delivery_address is present
        if (is_null($data['address']) && is_null($data['delivery_address'])) {
            return Helpers::sendFailureResponse(Response::HTTP_BAD_REQUEST);
        }

        $customerDTO = new CustomerDTO($data);

        $this->customerProfileService->updateCustomerInfo($customerId, $customerDTO);

        return Helpers::sendSuccessResponse(Response::HTTP_OK, 'Customer address updated successfully');
    }


    public function viewProfile()
    {
        $customer = $this->customerProfileService->getProfile();

        return Helpers::sendSuccessResponse(
            Response::HTTP_OK,
            'Customer profile retrieved successfully',
            new ProfileResource($customer)
        );
    }


}

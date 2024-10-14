<?php

namespace App\Services\Menu;

use App\DTO\AddonDTO;
use App\DTO\MenuDTO;
use App\DTO\MenuItemDTO;
use App\DTO\VariationDTO;
use App\Interfaces\menu\MenuServiceInterface;
use App\Models\Menu\Addon;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuItem;
use App\Models\Menu\Variation;
use App\Models\Restaurant\Branch;
use App\Models\Restaurant\Restaurant;
use App\Models\User\RestaurantOwner;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class MenuService implements MenuServiceInterface
{

    public function createMenu(array $data, $branch_id)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();


            // Find the restaurant owner
            $owner = RestaurantOwner::where('user_id', $user->id)->firstOrFail();


            // Find the restaurant associated with the owner
            $restaurant = Restaurant::where('owner_id', $owner->id)->firstOrFail();


            // Check if the specified branch belongs to the restaurant
            $branch = Branch::where('restaurant_id', $restaurant->id)
                            ->where('id', $branch_id)
                            ->firstOrFail();

            $data['branch_id']=$branch->id;
            $data['restaurant_id']=$restaurant->id;

            // Create a DTO (Data Transfer Object) for the menu
            $menu= Menu::create((new MenuDTO($data))->toArray());

            // Create the menu in the database

            // Return the created menu
            return ['success' => true, 'menu' => $menu];

        } catch (Exception $e) {
            // Return the exception message and status
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function addMenuItem(array $data, int $menu_id)
    {
        try {
            // Find the menu
            $menu = Menu::findOrFail($menu_id);

            $data['menu_id']=$menu->id;








            // Create a DTO (Data Transfer Object) for the menu
            $menuItem= MenuItem::create((new MenuItemDTO($data))->toArray());

            dd($menuItem);





            // Return the created menu item
            return ['success' => true, 'menuItem' => $menuItem];

        } catch (ModelNotFoundException $e) {
            // Handle the case where the menu is not found
            return ['success' => false, 'error' => 'Menu not found'];
        } catch (Exception $e) {

            dd($e);


        }
    }


    public function createAddon(array $data, int $menu_item_id)
    {
        try {
            // Find the menu item
            $menu_item = MenuItem::findOrFail($menu_item_id);

            $data['menu_item_id']=$menu_item->id;


            $addOn = Addon::create((new AddonDTO($data))->toArray());

            return ['success' => true, 'addon' => $addOn]; // Return the addon
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Unable to create addon'];
        }
    }



    public function updateMenu(int $menu_id, array $data): array
    {
        try {
            // Find the menu by its ID
            $menu = Menu::findOrFail($menu_id);

            // Update the menu with only name and description
            $menu->update($data);

            // Return success response
            return ['success' => true, 'menu' => $menu];
        } catch (Exception $e) {
            // Catch any exception and return failure response
            return ['success' => false, 'error' => 'Unable to update menu'];
        }
    }

    public function updateMenuItem(int $menu_item_id, array $data): array
    {
        try {
            // Find the menu item by its ID
            $menu_item = MenuItem::findOrFail($menu_item_id);

            // Update the menu item with the provided data
            $menu_item->update($data);

            // Return success response with updated menu item
            return ['success' => true, 'menu_item' => $menu_item];
        } catch (Exception $e) {
            // Catch any exception and return failure response
            return ['success' => false, 'error' => 'Unable to update menu item: ' . $e->getMessage()];
        }
    }


    public function storeChoices(array $data) {




            $user = Auth::user();


            $owner = RestaurantOwner::where('user_id', $user->id)->firstOrFail();

            $restaurant = Restaurant::where('owner_id', $owner->id)->firstOrFail();

            $data['restaurant_id']=$restaurant->id;


        if ($data['isChoice'] == 1) {

            $variation= Variation::create((new VariationDTO($data))->toArray());


            return response()->json([
                'success' => true,
                'message' => 'Variation saved successfully!',
                'data' => $variation,
            ]);
        } else {
            // If isChoice is not 1, create an Addon
            $addOn= Addon::create((new AddonDTO($data))->toArray());


            return response()->json([
                'success' => true,
                'message' => 'Addon saved successfully!',
                'data' => $addOn,
            ]);
        }
    }








}

<?php

namespace App\Services\Menu;

use App\DTO\Menu\ChoiceGroupDTO;
use App\DTO\Menu\ChoiceItemsDTO;
use App\Interfaces\ChoiceGroupServiceInterface;
use App\Models\Menu\Addon;
use App\Models\Menu\AssignedChoiceGroup;
use App\Models\Menu\Choice;
use App\Models\Menu\ChoiceGroup;
use App\Models\Menu\MenuItem;
use Illuminate\Support\Facades\DB;

class ChoiceGroupService implements ChoiceGroupServiceInterface
{

    public function addChoiceGroup($data){
        $restaurant = MenuBaseService::getRestaurant();
        $data['restaurant_id']=$restaurant->id;
        $choiceGroupDTO = new ChoiceGroupDTO($data);
        $choiceGroup= ChoiceGroup::create($choiceGroupDTO->toArray());
        return $choiceGroup->toArray();
    }

    public function addChoiceItems($data, $choices){
        try{
            $choicesToInsert = [];
            foreach ($choices as $choice) {
                $choicesToInsert[] = [
                    'choice_group_id' => $data['id'],
                    'name' => $choice['name'],
                    'price' => $choice['price'],
                ];
            }
            $insertedChoices  =Choice::insert($choicesToInsert);
            return $insertedChoices;
        }
        catch(\Exception $e){
            return false;
        }
    }
    public function assignChoiceGroup($data){
        try{
            $restaurant = MenuBaseService::getRestaurant();
            return AssignedChoiceGroup::create($data);
        }
        catch(\Exception $e){
            return false;
        }
    }
    public function createChoiceGroupWithChoices($data){
        try{
            DB::beginTransaction();
            $restaurant = MenuBaseService::getRestaurant();
            $restaurantId = $restaurant->id;
            // get restaurant owner id and add choice group
            $choiceGroup = $this->addChoiceGroup($data);
            $data['choice_group_id']=$choiceGroup['id'];
            $choices = json_decode($data['choices'], true);
            foreach($choices as $choice){
                $choice['choice_group_id']=$choiceGroup['id'];
                $choice['choice_type']=$data['choice_type'];
                $choiceItemDTO = new ChoiceItemsDTO($choice);
                $choiceItem = Choice::create($choiceItemDTO->toArray());
            }
            DB::commit();
            return [$choiceGroup,$choices];
        }
        catch(\Exception $e){
            DB::rollBack();
            return false;
        }
    }

    public function getAllChoiceGroupsByRestaurant(){
        $restaurant = MenuBaseService::getRestaurant();
        return $restaurant->load(['choiceGroups.choices', 'choiceGroups.addons']);
    }
    public function deleteChoiceGroup($data){
        try{
            $choiceGroup = ChoiceGroup::where('id', $data['id']);
            $choiceGroup->delete();
            return true;
        }
        catch(\Exception $e){
            return false;
        }
    }

    public function updateChoiceGroup($data){
        try{
            DB::beginTransaction();
            $restaurant = MenuBaseService::getRestaurant();
            $data['restaurant_id']=$restaurant->id;
            // find the choice group to update
            $choiceGroup = ChoiceGroup::find($data['id']);
            $storedChoices = $choiceGroup->choices->keyBy('id');
            $userChoices = collect(json_decode($data['choices'], true))->keyBy('id');
            $choiceGroupDTO = new ChoiceGroupDTO($data);
            $choiceGroup->update($choiceGroupDTO->toArray());
// Update or delete existing choices
            $storedChoices->each(function ($choice) use ($userChoices, $data) {
                if ($userChoices->has($choice->id)) {
                    // Update existing choice
                    $userChoiceData = $userChoices[$choice->id];
                    $userChoiceData['choice_group_id'] = $data['id'];
                    $choice->update((new ChoiceItemsDTO($userChoiceData))->toArray());
                } else {
                    // Delete choice not in the user's input
                    $choice->delete();
                }
            });

// Insert new choices if present
            if (!empty($data['new_choices'])) {
                $newChoices = json_decode($data['new_choices'], true);
                $this->addChoiceItems($data, $newChoices);
            }

            DB::commit();
        }
        catch(\Exception $e){
            DB::rollBack();
        }
    }


}

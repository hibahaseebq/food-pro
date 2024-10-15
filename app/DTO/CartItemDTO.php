<?php

namespace App\DTO;

class CartItemDTO extends BaseDTO
{

    public function __construct(array $data) {

        $this->session_id = $data['session_id'];
        $this->menu_item_id = $data['menu_item_id'];
        $this->quantity = $data['quantity'];
        $this->price = $data['price']??0.0; // if we hard code the price in database column it won't be updated in realtime
        $this->addon_id = $data['addon_id']??null;
        $this->size_variation_id = $data['size_variation_id']??null;
        $this->choice_group_id = $data['choice_group_id']??null;
        $this->choice_id = $data['choice_id']??null;
    }
}

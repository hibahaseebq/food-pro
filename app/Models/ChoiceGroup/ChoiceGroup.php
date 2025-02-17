<?php

namespace App\Models\ChoiceGroup;

use App\Models\Menu\MenuItem;
use App\Models\Restaurant\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChoiceGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function menuItem(){
        return $this->belongsTo(MenuItem::class);
    }
    public function choices(){
        return $this->hasMany(Choice::class,'choice_group_id','id');
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }

}

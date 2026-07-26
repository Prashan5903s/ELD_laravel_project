<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationMenu extends Model
{
    use HasFactory;

    protected $table = 'navigation_menus';

     public function childMenus()
    {
        return $this->hasMany(NavigationMenu::class, 'app_menu_id', 'menu_id');
    }

}

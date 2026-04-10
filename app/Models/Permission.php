<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'group'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public static function groups()
    {
        return [
            'Product' => 'Product Management',
            'Category' => 'Category Management',
            'Order' => 'Order Management',
            'Customer' => 'Customer Management',
            'User' => 'User Management',
            'Coupon' => 'Coupon Management',
            'Marketing' => 'Marketing',
            'Settings' => 'Settings',
            'Report' => 'Reports',
            'Other' => 'Other',
        ];
    }
}

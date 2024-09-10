<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteDown extends Model
{
    use HasFactory;
     // Specify the table name if it doesn't follow Laravel's naming convention
     protected $table = 'route_down';

     // Specify the primary key field name
     protected $primaryKey = 'idRouteDown';
 
     // Specify if the primary key is auto-incrementing
     public $incrementing = true;
 
     // Specify the primary key data type if it's not an integer
     protected $keyType = 'int';
 
     // Specify the fields that can be mass assigned
     protected $fillable = [
         'Destination',
         'Active',
         'Price'
     ];
 
     // Automatically manage created_at and updated_at timestamps
     public $timestamps = true;
 
}

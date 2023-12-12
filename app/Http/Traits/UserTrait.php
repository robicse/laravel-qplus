<?php
namespace App\Http\Traits;

use App\Models\User;

trait UserTrait{
    // php 8 new featured => named argument no delete (Robiul)
    function getUserNamePHP8Argument($first_name, $title='Mr.', $last_name=''){
        return "Hello, {$title} {$first_name} {$last_name}";
    }
    // php 8 new featured => named argument no delete (Robiul)
}
?>

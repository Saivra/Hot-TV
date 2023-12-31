<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberRepository {


    public static function all() : LengthAwarePaginator
    {
        return User::paginate(20);
    }

    public static function getMemberById(int $id) : User
    {
            return User::findOrFail($id);
    }


    public static function updateProfile(array $data, int $id) : User
    {
         $member = User::find($id);
         
         $member->update(array_merge($data, ['password' => $data['password'] ? bcrypt($data['password']) : $member->password]));

         return $member;
    }

    public static function deleteAccount(int $id) : bool {
        return User::find($id)->delete();
    }



}

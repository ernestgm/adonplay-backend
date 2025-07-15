<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Verifica si el usuario autenticado es administrador.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function isAdmin($user)
    {
        return $user->roles()->where('code', 'admin')->exists();
    }
}

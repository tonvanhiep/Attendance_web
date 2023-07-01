<?php

namespace App\Broadcasting;

use App\Models\AccountsModel;

class JoinChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\AccountsModel  $user
     * @return array|bool
     */
    public function join(AccountsModel $user)
    {
        //
    }
}

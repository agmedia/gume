<?php

namespace App\Http\Livewire\Back\Catalog;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Bouncer;

class UserSearchInput extends Component
{
    /**
     * @var string
     */
    public $search = '';

    /**
     * @var array
     */
    public $search_results = [];

    /**
     * @var int
     */
    public $user_id = 0;

    /**
     * @var bool
     */
    public $show_add_window = false;

    /**
     * @var null|bool
     */
    public $list = null;

    /**
     * @var array
     */
    public $new = [
        'name' => '',
        'email' => '',
    ];


    /**
     *
     */
    public function mount()
    {
        if ($this->user_id) {
            $user = User::query()->find($this->user_id);

            if ($user) {
                $this->search = $user->name;
            }
        }
    }


    /**
     *
     */
    public function viewAddWindow()
    {
        $this->show_add_window =! $this->show_add_window;
    }


    /**
     *
     */
    public function updatingSearch($value)
    {
        $this->search         = $value;
        $this->search_results = [];

        if ($this->search != '') {
            $this->search_results = User::query()->where('name', 'LIKE', '%' . $this->search . '%')
                                                  ->limit(5)
                                                  ->get();
        }
    }


    /**
     * @param $id
     */
    public function addUser($id)
    {
        $user = User::query()->where('id', $id)->first();

        $this->search_results = [];
        $this->search         = $user->name;
        $this->user_id     = $user->id;

        if ($this->list) {
            return $this->emit('userSelect', ['user' => $user]);
        }
    }


    /**
     *
     */
    public function makeNewUser()
    {
        if ($this->new['name'] == '' || $this->new['email'] == '' || ! filter_var($this->new['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->emit('error_alert', ['message' => 'Molimo vas da ispravno popunite sve podatke!']);
        }

        $email_exist = User::query()->where('email', $this->new['email'])->first();

        if ($email_exist) {
            return $this->emit('error_alert', ['message' => 'Korisnik sa ' . $this->new['email'] . ' emailom već postoji, molimo vas da izaberete drugi..!']);
        }

        $public_user = User::query()->create([
            'name'     => $this->new['name'],
            'email'    => $this->new['email'],
            'password' => Hash::make('pneumax'),
        ]);

        Log::info($public_user->toArray());

        if ($public_user) {
            Log::info(1);
            Bouncer::assign('customer')->to($public_user);

            Log::info(2);

            UserDetail::query()->create([
                'user_id'    => $public_user->id,
                'fname'      => $this->new['name'],
                'role'       => 'customer',
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info(3);

            $this->show_add_window = false;
            $this->user_id = $public_user->id;
            $this->search   = $public_user->name;

            return $this->emit('success_alert', ['message' => 'Korisnik je uspješno dodan..!']);
        }

        return $this->emit('error_alert', ['message' => 'Uuups! Dogodila se greška sa unosom korisnika, molimo pokušajte ponovo ili kontaktirajte administratora!']);


    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        if ($this->search == '') {
            $this->user_id = 0;

            if ($this->list) {
                $this->emit('userSelect', ['user' => ['id' => '']]);
            }
        }

        return view('livewire.back.catalog.user-search-input');
    }
}

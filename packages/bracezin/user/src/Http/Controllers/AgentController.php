<?php

namespace User\Http\Controllers;

use Arr;

class AgentController extends PackageController
{
    public function getRepository()
    {
        return $this->allUserRepository;
    }

    public function indexDataInit($input)
    {
        $input['type'] = 'agent';
        $input['with'] = @$input['with'] ?: 'detail';

        return $input;
    }

    public function storeDataInit($input)
    {
        $input['username'] = Arr::get($input, 'username') ? Arr::get($input, 'username') : (Arr::get($input, 'email') ? Arr::get($input, 'email') : (Arr::get($input, 'mobile') ? Arr::get($input, 'mobile') : Arr::get($input, 'name')));
        $input['type'] = Arr::get($input, 'type') ? Arr::get($input, 'type') : 'agent';
        $input['password'] = Arr::get($input, 'password') ? Arr::get($input, 'password') : Config('userConfig.default_user_password', 'secret');

        if (Arr::get($input, 'email')) {
            Validation::checkOn($input, ['email' => 'required|string|email|min:4|max:50|unique:users,email']);
        }
        if (Arr::get($input, 'mobile')) {
            Validation::checkOn($input, ['mobile' => 'required|string|min:8|max:12|unique:users,mobile']);
        }
        if (Arr::get($input, 'username')) {
            Validation::checkOn($input, ['username' => 'required|string|min:4|max:50|unique:users,username']);
        }

        return $input;
    }

    public function updateResponseInit($item, $input)
    {
        $item = $this->repository->findById($item->id, ['with' => 'detail']);

        return $item;
    }
}

<?php

namespace Database\Factories\Remark\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Layout\Models\Menu;
use Layout\Models\MenuGroup;
use Layout\Models\SubMenu;
use Remark\Models\comment;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userIds = User::get()->pluck('id')->toArray();
        $resource = $this->faker->randomElement(['menu_group', 'menu', 'submenu']);
        switch ($resource) {
            case 'menu_group':
                $resourceId = MenuGroup::inRandomOrder()->first()?->id;
                break;
            case 'menu':
                $resourceId = Menu::inRandomOrder()->first()?->id;
                break;
            case 'submenu':
                $resourceId = SubMenu::inRandomOrder()->first()?->id;
                break;
            default:
                $resourceId = null;
        }

        return [
            'resource_id' => $resourceId,
            'resource_type' => $resource,
            'comment_id' => null,
            'user_id' => $this->faker->randomElement($userIds),
            'title' => $this->faker->text($maxNbChars = 45),
            'description' => $this->faker->realtext($maxNbChars = 40),
            'rating' => $this->faker->numberBetween(1, 5),
        ];
    }
}

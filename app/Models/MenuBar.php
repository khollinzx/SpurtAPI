<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuBar extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'link'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches Roles model by title
     * @param string $name
     * @return mixed
     */
    public static function getMenuByName(string $name)
    {
        return self::where('title', ucwords($name))->first();
    }

    public function menus(): array
    {
        return [
            [
                'title' => 'Career',
                'link' => 'solutions/career'
            ],
            [
                'title' => 'Clients',
                'link' => 'clients'
            ],
            [
                'title' => 'Consultants',
                'link' => 'consultants'
            ],
            [
                'title' => 'Admin',
                'link' => 'admin'
            ],
            [
                'title' => 'Service',
                'link' => 'paperclip/service'
            ],
            [
                'title' => 'Expert',
                'link' => 'solutions/expert'
            ],
            [
                'title' => 'Request Demo',
                'link' => 'spurtx/request'
            ],
            [
                'title' => 'TalentBase',
                'link' => 'solutions/talent-base'
            ],
            [
                'title' => 'Nomination',
                'link' => 'madein/nominations'
            ],
            [
                'title' => 'Interview',
                'link' => 'madein/interview'
            ],
            [
                'title' => 'Pre-Consultation',
                'link' => 'solution/pre-consultation'
            ],
            [
                'title' => 'Publications',
                'link' => 'solutions/publications'
            ],
            [
                'title' => 'Invoice',
                'link' => 'invoice'
            ],
            [
                'title' => 'Scheduled',
                'link' => 'schedules'
            ],
            [
                'title' => 'Review',
                'link' => 'madein/review'
            ]
        ];
    }

    /**
     * This is used to populate Ghana Banks to banks database
     */
    public function initMenu(){
        $Menus = $this->menus();

        foreach ($Menus as $menu){
            $Rec = new self();
            $Rec->title = $menu['title'];
            $Rec->link = strtolower($menu['link']);
            $Rec->save();
        }
    }

    /**
     * @return array
     */
    public static function getMenuTitles(): array
    {
        $result = [];
        $menus = self::all();

        $menus->each(function ($menu) use (&$result){
            $result[$menu->title] = $menu->title;
        });

        return $result;
    }

    /**
     * @return array
     */
    public static function getAllMenus()
    {
        return self::all();
    }

    /** fetches all Admins
     * @return mixed|User[]|Collection
     */
    public static function fetchAllMenus()
    {
        return self::orderByDesc('id')
            ->get();
    }
}
